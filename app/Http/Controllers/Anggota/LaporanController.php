<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Http\Requests\Anggota\StoreLaporanRequest;
use App\Http\Requests\Anggota\UpdateLaporanRequest;
use App\Models\AktivitasLog;
use App\Models\Laporan;
use App\Models\Usaha;
use App\Services\NotificationService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class LaporanController extends Controller
{
    /**
     * Inject NotificationService agar seluruh pengiriman notifikasi
     * menggunakan service terpusat.
     */
    public function __construct(
        private NotificationService $notification
    ) {}

    /**
     * Daftar laporan milik anggota yang sedang login.
     */
    public function index(Request $request): View
    {
        $anggota = Auth::user()->anggota;

        /*
         * Jika akun login belum terhubung dengan data anggota,
         * tampilkan halaman laporan dengan paginator kosong.
         */
        if (! $anggota) {
            $laporanList = new LengthAwarePaginator(
                items: [],
                total: 0,
                perPage: 10,
                currentPage: LengthAwarePaginator::resolveCurrentPage(),
                options: [
                    'path'  => $request->url(),
                    'query' => $request->query(),
                ]
            );

            return view('anggota.laporan.index', [
                'laporanList'     => $laporanList,
                'search'          => $request->input('search', ''),
                'filterStatus'    => $request->input('status', ''),
                'filterJenis'     => $request->input('jenis', ''),
                'usahaIds'        => [],
                'punyaUsahaAktif' => false,
            ]);
        }

        $usahaIds = Usaha::where('anggota_id', $anggota->id)
            ->pluck('id');

        $punyaUsahaAktif = Usaha::query()
            ->where('anggota_id', $anggota->id)
            ->where('status_usaha', 'aktif')
            ->where('status_validasi_syariah', 'disetujui')
            ->whereNotNull('tanggal_aktivasi_monitoring')
            ->exists();

        $query = Laporan::query()
            ->whereIn('laporan.usaha_id', $usahaIds)
            ->join('usaha', 'laporan.usaha_id', '=', 'usaha.id')
            ->select(
                'laporan.*',
                'usaha.nama_usaha',
                'usaha.jenis_usaha'
            );

        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('usaha.nama_usaha', 'like', "%{$search}%")
                    ->orWhere(
                        'laporan.periode_ke',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        if ($request->filled('status')) {
            $query->where(
                'laporan.status_laporan',
                $request->input('status')
            );
        }

        if ($request->filled('jenis')) {
            $query->where(
                'usaha.jenis_usaha',
                $request->input('jenis')
            );
        }

        $laporanList = $query
            ->with([
                'usaha',
                'validasi',
            ])
            ->orderByDesc('laporan.created_at')
            ->paginate(10)
            ->withQueryString();

        return view('anggota.laporan.index', [
            'laporanList'     => $laporanList,
            'search'          => $request->input('search', ''),
            'filterStatus'    => $request->input('status', ''),
            'filterJenis'     => $request->input('jenis', ''),
            'usahaIds'        => $usahaIds,
            'punyaUsahaAktif' => $punyaUsahaAktif,
        ]);
    }

    /**
     * Menampilkan form input laporan monitoring baru.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $anggota = Auth::user()->anggota;

        if (! $anggota) {
            return redirect()
                ->route('anggota.dashboard')
                ->with(
                    'error',
                    'Data anggota Anda belum terdaftar. Hubungi admin.'
                );
        }

        $usahaList = Usaha::query()
            ->where('anggota_id', $anggota->id)
            ->where('status_usaha', 'aktif')
            ->where('status_validasi_syariah', 'disetujui')
            ->whereNotNull('tanggal_aktivasi_monitoring')
            ->orderBy('nama_usaha')
            ->get();

        if ($usahaList->isEmpty()) {
            return redirect()
                ->route('anggota.usaha-saya.index')
                ->with(
                    'error',
                    'Belum ada usaha yang dapat dimonitor. Usaha harus aktif dan akadnya sudah disetujui DPS.'
                );
        }

        $selectedUsahaId = $request->query('usaha_id');

        $periodeUsaha = $usahaList
            ->mapWithKeys(function (Usaha $usaha): array {
                $periode = $usaha->periode_monitoring_berikutnya;

                return [
                    $usaha->id => [
                        'periode_ke'      => $periode['periode_ke'],
                        'tanggal_mulai'   => $periode['tanggal_mulai']?->format('Y-m-d'),
                        'tanggal_selesai' => $periode['tanggal_selesai']?->format('Y-m-d'),
                        'label'           => $periode['label'],
                        'boleh_dibuat'    => $usaha->boleh_membuat_laporan_baru,
                        'pesan'           => $usaha->pesan_periode_monitoring,
                    ],
                ];
            });

        return view(
            'anggota.laporan.create',
            compact('usahaList', 'selectedUsahaId', 'periodeUsaha')
        );
    }

    /**
     * Menyimpan laporan monitoring baru.
     */
    public function store(
        StoreLaporanRequest $request
    ): RedirectResponse {
        $anggota = Auth::user()->anggota;
        $validated = $request->validated();
        $fileBaru = null;

        if ($request->hasFile('file_bukti')) {
            try {
                $fileBaru = $this->uploadFileBukti(
                    $request->file('file_bukti')
                );
            } catch (Throwable $exception) {
                report($exception);

                return redirect()
                    ->back()
                    ->withInput()
                    ->with(
                        'error',
                        'File bukti belum berhasil diunggah. Silakan coba kembali.'
                    );
            }
        }

        try {
            $laporan = DB::transaction(function () use (
                $validated,
                $anggota,
                $request,
                $fileBaru
            ): Laporan {
                /*
                 * Kunci baris usaha agar dua request bersamaan tidak dapat
                 * membuat periode yang sama.
                 */
                $usaha = Usaha::query()
                    ->whereKey($validated['usaha_id'])
                    ->where('anggota_id', $anggota->id)
                    ->where('status_usaha', 'aktif')
                    ->where('status_validasi_syariah', 'disetujui')
                    ->whereNotNull('tanggal_aktivasi_monitoring')
                    ->lockForUpdate()
                    ->firstOrFail();

                if (! $usaha->boleh_membuat_laporan_baru) {
                    throw new RuntimeException(
                        'PERIODE_TIDAK_TERSEDIA|' . $usaha->pesan_periode_monitoring
                    );
                }

                $periode = $usaha->periode_monitoring_berikutnya;

                if (
                    ! $periode['tanggal_mulai']
                    || ! $periode['tanggal_selesai']
                ) {
                    throw new RuntimeException('PERIODE_BELUM_TERSEDIA');
                }

                $sudahAda = Laporan::query()
                    ->where('usaha_id', $usaha->id)
                    ->where('periode_ke', $periode['periode_ke'])
                    ->exists();

                if ($sudahAda) {
                    throw new RuntimeException('PERIODE_SUDAH_ADA');
                }

                $data = [
                    'usaha_id'          => $usaha->id,
                    'periode_ke'        => $periode['periode_ke'],
                    'tanggal_mulai'     => $periode['tanggal_mulai']->toDateString(),
                    'tanggal_selesai'   => $periode['tanggal_selesai']->toDateString(),
                    'omzet'             => $validated['omzet'],
                    'biaya_operasional' => $validated['biaya_operasional'],
                    'laba_bersih'       => (float) $validated['omzet']
                        - (float) $validated['biaya_operasional'],
                    'keterangan'        => $validated['keterangan'] ?? null,
                    'file_bukti'        => $fileBaru,
                    'status_laporan'    => 'dikirim',
                ];

                $laporan = Laporan::create($data);

                AktivitasLog::create([
                    'akun_id' => Auth::id(),
                    'aktivitas' => 'Kirim Laporan',
                    'keterangan' => 'Mengirim laporan monitoring periode ke-'
                        . $laporan->periode_ke
                        . ' untuk usaha ID: '
                        . $laporan->usaha_id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                return $laporan;
            });
        } catch (Throwable $exception) {
            if ($fileBaru) {
                $this->hapusFileBukti($fileBaru);
            }

            if ($this->adalahDuplikatPeriode($exception)) {
                return redirect()
                    ->route('anggota.laporan.index')
                    ->with(
                        'error',
                        'Laporan untuk periode tersebut sudah pernah dikirim.'
                    );
            }

            if (
                $exception instanceof RuntimeException
                && str_starts_with(
                    $exception->getMessage(),
                    'PERIODE_TIDAK_TERSEDIA|'
                )
            ) {
                $pesan = Str::after(
                    $exception->getMessage(),
                    'PERIODE_TIDAK_TERSEDIA|'
                );

                return redirect()
                    ->route('anggota.laporan.index')
                    ->with('error', $pesan);
            }

            if (
                $exception instanceof RuntimeException
                && $exception->getMessage() === 'PERIODE_BELUM_TERSEDIA'
            ) {
                return redirect()
                    ->route('anggota.laporan.index')
                    ->with(
                        'error',
                        'Periode monitoring belum tersedia. Pastikan akad telah disetujui DPS dan tanggal aktivasi monitoring sudah tercatat.'
                    );
            }

            report($exception);

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Laporan belum berhasil disimpan. Silakan coba kembali.'
                );
        }

        $laporan->load('usaha.anggota.akun');

        $this->notification->notifyPengurus(
            'Laporan Baru Masuk',
            'Laporan periode ke-'
                . $laporan->periode_ke
                . ' dari anggota '
                . $laporan->usaha->anggota->akun->nama
                . ' (usaha: '
                . $laporan->usaha->nama_usaha
                . ') telah dikirim dan menunggu validasi.',
            'info'
        );

        return redirect()
            ->route('anggota.laporan.index')
            ->with(
                'success',
                'Laporan periode ke-'
                    . $laporan->periode_ke
                    . ' berhasil dikirim. Pengurus akan segera memvalidasi laporan Anda.'
            );
    }

    /**
     * Menampilkan detail laporan milik anggota.
     */
    public function show(
        Laporan $laporan
    ): View|RedirectResponse {
        $this->cekAksesMilikAnggota($laporan);

        $laporan->load([
            'usaha.anggota.akun',
            'usaha',
            'validasi.validator',
            'validasi.dps',
        ]);

        return view(
            'anggota.laporan.show',
            compact('laporan')
        );
    }

    /**
     * Menampilkan form revisi laporan yang ditolak.
     */
    public function edit(
        Laporan $laporan
    ): View|RedirectResponse {
        $this->cekAksesMilikAnggota($laporan);

        if ($laporan->status_laporan !== 'ditolak') {
            return redirect()
                ->route(
                    'anggota.laporan.show',
                    $laporan->id
                )
                ->with(
                    'error',
                    'Laporan hanya dapat direvisi setelah ditolak oleh Pengurus.'
                );
        }

        $laporan->load('usaha');

        return view(
            'anggota.laporan.edit',
            compact('laporan')
        );
    }

    /**
     * Memperbarui laporan yang ditolak dan mengirimkannya kembali.
     */
    public function update(
        UpdateLaporanRequest $request,
        Laporan $laporan
    ): RedirectResponse {
        $this->cekAksesMilikAnggota($laporan);

        if ($laporan->status_laporan !== 'ditolak') {
            return redirect()
                ->route(
                    'anggota.laporan.show',
                    $laporan->id
                )
                ->with(
                    'error',
                    'Laporan hanya dapat direvisi setelah ditolak oleh Pengurus.'
                );
        }

        $validated = $request->validated();
        $fileLama = $laporan->file_bukti;
        $fileBaru = null;

        if ($request->hasFile('file_bukti')) {
            try {
                $fileBaru = $this->uploadFileBukti(
                    $request->file('file_bukti')
                );
            } catch (Throwable $exception) {
                report($exception);

                return redirect()
                    ->back()
                    ->withInput()
                    ->with(
                        'error',
                        'File bukti baru belum berhasil diunggah. File lama tetap aman.'
                    );
            }
        }

        try {
            $laporan = DB::transaction(function () use (
                $laporan,
                $validated,
                $request,
                $fileBaru
            ): Laporan {
                $laporanTerkunci = Laporan::query()
                    ->whereKey($laporan->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $this->cekAksesMilikAnggota($laporanTerkunci);

                if ($laporanTerkunci->status_laporan !== 'ditolak') {
                    throw new RuntimeException('LAPORAN_TIDAK_BISA_DIREVISI');
                }

                $data = [
                    'omzet'             => $validated['omzet'],
                    'biaya_operasional' => $validated['biaya_operasional'],
                    'laba_bersih'       => (float) $validated['omzet']
                        - (float) $validated['biaya_operasional'],
                    'keterangan'        => $validated['keterangan'] ?? null,
                    'status_laporan'    => 'dikirim',
                ];

                if ($fileBaru) {
                    $data['file_bukti'] = $fileBaru;
                }

                $laporanTerkunci->update($data);
                $laporanTerkunci->load('usaha.anggota.akun');

                AktivitasLog::create([
                    'akun_id' => Auth::id(),
                    'aktivitas' => 'Revisi Laporan',
                    'keterangan' => 'Merevisi dan mengirim ulang laporan periode ke-'
                        . $laporanTerkunci->periode_ke
                        . ' usaha: '
                        . $laporanTerkunci->usaha->nama_usaha,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                return $laporanTerkunci;
            });
        } catch (Throwable $exception) {
            if ($fileBaru) {
                $this->hapusFileBukti($fileBaru);
            }

            if (
                $exception instanceof RuntimeException
                && $exception->getMessage() === 'LAPORAN_TIDAK_BISA_DIREVISI'
            ) {
                return redirect()
                    ->route(
                        'anggota.laporan.show',
                        $laporan->id
                    )
                    ->with(
                        'error',
                        'Status laporan telah berubah dan laporan tidak dapat direvisi lagi.'
                    );
            }

            report($exception);

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Laporan belum berhasil direvisi. File lama tetap aman.'
                );
        }

        /*
         * File lama baru dihapus setelah update database berhasil.
         */
        if ($fileBaru && $fileLama && $fileLama !== $fileBaru) {
            $this->hapusFileBukti($fileLama);
        }

        $this->notification->notifyPengurus(
            'Laporan Direvisi & Dikirim Ulang',
            'Anggota '
                . $laporan->usaha->anggota->akun->nama
                . ' telah memperbaiki dan mengirim ulang laporan periode ke-'
                . $laporan->periode_ke
                . ' (usaha: '
                . $laporan->usaha->nama_usaha
                . ').',
            'info'
        );

        return redirect()
            ->route('anggota.laporan.index')
            ->with(
                'success',
                'Laporan berhasil direvisi dan dikirim ulang. Pengurus akan memvalidasi kembali.'
            );
    }

    /**
     * Memastikan laporan hanya dapat diakses oleh anggota pemiliknya.
     */
    private function cekAksesMilikAnggota(
        Laporan $laporan
    ): void {
        $anggota = Auth::user()->anggota;

        if (! $anggota) {
            abort(
                403,
                'Data anggota tidak ditemukan.'
            );
        }

        $usahaIds = Usaha::where(
            'anggota_id',
            $anggota->id
        )->pluck('id');

        if (! $usahaIds->contains($laporan->usaha_id)) {
            abort(
                403,
                'Anda tidak memiliki akses ke laporan ini.'
            );
        }
    }

    /**
     * Mengunggah file bukti transaksi ke folder public.
     */
    private function uploadFileBukti(
        UploadedFile $file
    ): string {
        $folder = public_path('uploads/laporan');

        if (! file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        $filename = 'laporan_'
            . Str::random(10)
            . '_'
            . time()
            . '.'
            . strtolower($file->getClientOriginalExtension());

        $file->move($folder, $filename);

        return $filename;
    }

    /**
     * Menghapus file bukti jika tersedia.
     */
    private function hapusFileBukti(
        ?string $filename
    ): void {
        if (! $filename) {
            return;
        }

        $path = public_path(
            'uploads/laporan/' . $filename
        );

        if (file_exists($path)) {
            @unlink($path);
        }
    }

    /**
     * Mengenali benturan unique index usaha_id + periode_ke.
     */
    private function adalahDuplikatPeriode(
        Throwable $exception
    ): bool {
        if (
            $exception instanceof RuntimeException
            && $exception->getMessage() === 'PERIODE_SUDAH_ADA'
        ) {
            return true;
        }

        if (! $exception instanceof QueryException) {
            return false;
        }

        $message = strtolower($exception->getMessage());

        return str_contains($message, 'unique_usaha_periode')
            || str_contains($message, 'duplicate entry')
            || (string) $exception->getCode() === '23000';
    }
}
