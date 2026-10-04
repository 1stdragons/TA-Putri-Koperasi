<?php

namespace App\Http\Controllers\Dps;

use App\Http\Controllers\Controller;
use App\Models\AktivitasLog;
use App\Models\Usaha;
use App\Models\ValidasiAkad;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class ValidasiSyariahController extends Controller
{
    public function __construct(
        private NotificationService $notification
    ) {}

    /**
     * Menampilkan daftar akad usaha yang sedang menunggu pemeriksaan DPS.
     *
     * DPS hanya memvalidasi dokumen akad syariah usaha.
     * DPS tidak memvalidasi laporan monitoring 14 hari.
     */
    public function index(Request $request): View
    {
        $query = Usaha::query()
            ->join('anggota', 'usaha.anggota_id', '=', 'anggota.id')
            ->join('akun', 'anggota.akun_id', '=', 'akun.id')
            ->select(
                'usaha.*',
                'akun.nama as nama_anggota',
                'anggota.nomor_anggota'
            )
            ->where(
                'usaha.status_validasi_syariah',
                'menunggu'
            )
            ->whereNotNull('usaha.file_akad');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);

            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('usaha.nama_usaha', 'like', "%{$search}%")
                    ->orWhere('akun.nama', 'like', "%{$search}%")
                    ->orWhere(
                        'anggota.nomor_anggota',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        if ($request->filled('jenis_akad')) {
            $query->where(
                'usaha.jenis_akad',
                $request->jenis_akad
            );
        }

        /*
         * Daftar kerja DPS hanya berisi status menunggu.
         * Parameter ini tetap dibaca agar kompatibel dengan query string lama,
         * tetapi nilai selain menunggu tidak akan membuka data perlu revisi.
         */
        if (
            $request->filled('status_syariah')
            && $request->status_syariah === 'menunggu'
        ) {
            $query->where(
                'usaha.status_validasi_syariah',
                'menunggu'
            );
        }

        $sort = $request->sort === 'nama_usaha'
            ? 'usaha.nama_usaha'
            : 'usaha.updated_at';

        $dir = $request->dir === 'asc'
            ? 'asc'
            : 'desc';

        $usahaList = $query
            ->with([
                'anggota.akun',
                'validasiAkadTerakhir.dps',
            ])
            ->orderBy($sort, $dir)
            ->paginate(10)
            ->withQueryString();

        $totalMenunggu = Usaha::query()
            ->where(
                'status_validasi_syariah',
                'menunggu'
            )
            ->whereNotNull('file_akad')
            ->count();

        return view('dps.validasi-syariah.index', [
            'usahaList'     => $usahaList,
            'search'        => $request->search,
            'filterAkad'    => $request->jenis_akad,
            'filterSyariah' => $request->status_syariah,
            'sort'          => $request->sort ?? 'updated_at',
            'dir'           => $dir,
            'totalMenunggu' => $totalMenunggu,
        ]);
    }

    /**
     * Menampilkan detail akad usaha yang sedang menunggu pemeriksaan DPS.
     */
    public function show(
        Usaha $usaha
    ): View|RedirectResponse {
        if ($usaha->status_validasi_syariah !== 'menunggu') {
            return redirect()
                ->route('dps.validasi-syariah.index')
                ->with(
                    'error',
                    'Akad usaha ini tidak sedang menunggu validasi DPS.'
                );
        }

        if (! $usaha->file_akad) {
            return redirect()
                ->route('dps.validasi-syariah.index')
                ->with(
                    'error',
                    'Dokumen akad usaha belum diunggah.'
                );
        }

        $usaha->load([
            'anggota.akun',
            'validasiAkad.dps',
            'validasiAkadTerakhir.dps',
        ]);

        /*
         * Variabel ini tetap dikirim agar Blade lama tidak error
         * apabila masih memiliki tampilan laporan atau preview evaluasi.
         *
         * DPS tidak memproses laporan monitoring.
         */
        $laporanTerbaru = null;
        $previewEvaluasi = null;

        return view(
            'dps.validasi-syariah.show',
            compact(
                'usaha',
                'laporanTerbaru',
                'previewEvaluasi'
            )
        );
    }

    /**
     * Menyimpan keputusan DPS terhadap iterasi akad yang sedang menunggu.
     *
     * Saat dokumen akad diunggah, ValidasiAkad::buatIterasiBaru()
     * sudah membuat satu baris riwayat berstatus menunggu.
     * Karena itu, keputusan DPS harus memperbarui baris tersebut dan
     * tidak boleh membuat iterasi baru.
     *
     * Jika disetujui:
     * - status validasi syariah menjadi disetujui;
     * - status usaha menjadi aktif;
     * - tanggal aktivasi monitoring diisi otomatis;
     * - anggota boleh memulai monitoring 14 hari.
     *
     * Jika perlu revisi:
     * - status validasi syariah menjadi perlu_revisi;
     * - status usaha menjadi belum_aktif;
     * - tanggal aktivasi monitoring dikosongkan;
     * - usaha keluar dari daftar kerja DPS sampai akad baru diunggah.
     */
    public function proses(
        Request $request,
        Usaha $usaha
    ): RedirectResponse {
        $request->validate([
            'keputusan' => [
                'required',
                'in:disetujui,perlu_revisi',
            ],
            'catatan_dps' => [
                'nullable',
                'string',
                'max:1000',
                function (
                    string $attribute,
                    mixed $value,
                    \Closure $fail
                ) use ($request): void {
                    if ($request->keputusan !== 'perlu_revisi') {
                        return;
                    }

                    $catatan = trim((string) ($value ?? ''));

                    if ($catatan === '') {
                        $fail(
                            'Catatan DPS wajib diisi jika keputusan adalah "Perlu Revisi".'
                        );

                        return;
                    }

                    if (mb_strlen($catatan) < 5) {
                        $fail(
                            'Catatan DPS minimal 5 karakter jika keputusan adalah "Perlu Revisi".'
                        );
                    }
                },
            ],
        ], [
            'keputusan.required' =>
            'Keputusan validasi wajib dipilih.',
            'keputusan.in' =>
            'Keputusan validasi tidak valid.',
            'catatan_dps.string' =>
            'Catatan DPS harus berupa teks.',
            'catatan_dps.max' =>
            'Catatan DPS maksimal 1000 karakter.',
        ]);

        if ($usaha->status_validasi_syariah !== 'menunggu') {
            return redirect()
                ->route('dps.validasi-syariah.index')
                ->with(
                    'error',
                    'Akad usaha ini sudah diproses atau sedang menunggu unggahan revisi.'
                );
        }

        if (! $usaha->file_akad) {
            return redirect()
                ->route('dps.validasi-syariah.index')
                ->with(
                    'error',
                    'Dokumen akad usaha belum diunggah.'
                );
        }

        $usaha->load('anggota.akun');

        try {
            $hasilProses = DB::transaction(function () use (
                $request,
                $usaha
            ): array {
                /*
                 * Kunci data usaha agar dua permintaan yang datang hampir
                 * bersamaan tidak dapat memproses akad yang sama dua kali.
                 */
                $usahaTerkunci = Usaha::query()
                    ->lockForUpdate()
                    ->find($usaha->id);

                if (! $usahaTerkunci) {
                    return [
                        'berhasil' => false,
                        'pesan' => 'Data usaha tidak ditemukan.',
                    ];
                }

                if (
                    $usahaTerkunci->status_validasi_syariah
                    !== 'menunggu'
                ) {
                    return [
                        'berhasil' => false,
                        'pesan' =>
                        'Akad usaha ini sudah diproses sebelumnya atau sedang menunggu unggahan revisi.',
                    ];
                }

                if (! $usahaTerkunci->file_akad) {
                    return [
                        'berhasil' => false,
                        'pesan' => 'Dokumen akad usaha belum diunggah.',
                    ];
                }

                /*
                 * Ambil iterasi menunggu yang sudah dibuat saat dokumen
                 * akad diunggah. Baris ini yang diperbarui oleh DPS.
                 */
                $validasiMenunggu = ValidasiAkad::query()
                    ->where('usaha_id', $usahaTerkunci->id)
                    ->where('status', 'menunggu')
                    ->orderByDesc('iterasi')
                    ->lockForUpdate()
                    ->first();

                if (! $validasiMenunggu) {
                    return [
                        'berhasil' => false,
                        'pesan' =>
                        'Riwayat pengajuan akad berstatus menunggu tidak ditemukan. Unggah ulang dokumen akad sebelum diproses DPS.',
                    ];
                }

                $catatanDps = trim(
                    (string) ($request->catatan_dps ?? '')
                );

                $validasiMenunggu->update([
                    'dps_id' => Auth::id(),
                    'file_akad_divalidasi' =>
                    $usahaTerkunci->file_akad,
                    'jenis_akad_divalidasi' =>
                    $usahaTerkunci->jenis_akad,
                    'status' => $request->keputusan,
                    'catatan_dps' =>
                    $catatanDps !== '' ? $catatanDps : null,
                    'divalidasi_pada' => now(),
                ]);

                if ($request->keputusan === 'disetujui') {
                    /*
                     * Tanggal aktivasi hanya diisi ketika akad benar-benar
                     * disetujui. Nilai lama dipertahankan bila sudah tersedia.
                     */
                    $tanggalAktivasi =
                        $usahaTerkunci->tanggal_aktivasi_monitoring
                        ?? now()->toDateString();

                    $usahaTerkunci->update([
                        'status_validasi_syariah' => 'disetujui',
                        'status_usaha' => 'aktif',
                        'tanggal_aktivasi_monitoring' =>
                        $tanggalAktivasi,
                    ]);
                } else {
                    /*
                     * Akad yang perlu revisi belum boleh digunakan
                     * untuk monitoring sampai dokumen baru diunggah
                     * dan disetujui DPS.
                     */
                    $usahaTerkunci->update([
                        'status_validasi_syariah' => 'perlu_revisi',
                        'status_usaha' => 'belum_aktif',
                        'tanggal_aktivasi_monitoring' => null,
                    ]);
                }

                AktivitasLog::create([
                    'akun_id' => Auth::id(),
                    'aktivitas' =>
                    $request->keputusan === 'disetujui'
                        ? 'Setujui Akad Syariah'
                        : 'Minta Revisi Akad Syariah',
                    'keterangan' =>
                    'Validasi syariah usaha: '
                        . $usahaTerkunci->nama_usaha
                        . ' — iterasi ke-'
                        . $validasiMenunggu->iterasi
                        . ' — keputusan: '
                        . $request->keputusan,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                return [
                    'berhasil' => true,
                    'pesan' => null,
                    'iterasi' => $validasiMenunggu->iterasi,
                ];
            });

            if (! $hasilProses['berhasil']) {
                return redirect()
                    ->route('dps.validasi-syariah.index')
                    ->with(
                        'error',
                        $hasilProses['pesan']
                    );
            }
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Keputusan validasi belum berhasil disimpan. Silakan coba kembali.'
                );
        }

        $akunAnggotaId = $usaha->anggota->akun_id;
        $namaUsaha = $usaha->nama_usaha;

        if ($request->keputusan === 'disetujui') {
            $this->notification->notifyAnggota(
                $akunAnggotaId,
                'Akad Usaha Disetujui DPS',
                'Dokumen akad usaha Anda "'
                    . $namaUsaha
                    . '" telah disetujui oleh Dewan Pengawas Syariah. '
                    . 'Usaha sudah aktif dan periode monitoring 14 hari pertama telah dimulai.',
                'sukses'
            );

            $pesanSukses =
                'Akad usaha "'
                . $namaUsaha
                . '" berhasil disetujui pada iterasi ke-'
                . $hasilProses['iterasi']
                . '. Usaha telah diaktifkan dan monitoring 14 hari sudah dimulai.';
        } else {
            $this->notification->notifyAnggota(
                $akunAnggotaId,
                'Akad Usaha Perlu Revisi',
                'Dokumen akad usaha Anda "'
                    . $namaUsaha
                    . '" memerlukan revisi. Alasan: '
                    . trim((string) $request->catatan_dps)
                    . '. Silakan perbaiki dokumen atau data akad, lalu ajukan kembali.',
                'peringatan'
            );

            $pesanSukses =
                'Permintaan revisi akad usaha "'
                . $namaUsaha
                . '" pada iterasi ke-'
                . $hasilProses['iterasi']
                . ' telah dikirim kepada anggota.';
        }

        return redirect()
            ->route('dps.validasi-syariah.index')
            ->with(
                'success',
                $pesanSukses
            );
    }
}
