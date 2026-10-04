<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\AktivitasLog;
use App\Models\Laporan;
use App\Models\Validasi;
use App\Services\EvaluasiService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;
use Illuminate\View\View;

class ValidasiLaporanController extends Controller
{
    private array $allowedSorts = [
        'created_at',
        'nama_usaha',
        'omzet',
        'laba_bersih',
    ];

    public function __construct(
        private EvaluasiService $evaluasiService,
        private NotificationService $notification
    ) {}

    /**
     * Daftar laporan yang sudah dikirim anggota.
     */
    public function index(Request $request): View
    {
        $query = Laporan::query()
            ->join('usaha', 'laporan.usaha_id', '=', 'usaha.id')
            ->join('anggota', 'usaha.anggota_id', '=', 'anggota.id')
            ->join('akun', 'anggota.akun_id', '=', 'akun.id')
            ->select(
                'laporan.*',
                'usaha.nama_usaha',
                'usaha.jenis_usaha',
                'anggota.nomor_anggota',
                'akun.nama as nama_anggota'
            )
            ->where('laporan.status_laporan', '!=', 'draft');

        // Search berdasarkan usaha, anggota, atau nomor anggota.
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('usaha.nama_usaha', 'like', "%{$search}%")
                    ->orWhere('akun.nama', 'like', "%{$search}%")
                    ->orWhere('anggota.nomor_anggota', 'like', "%{$search}%");
            });
        }

        // Filter status laporan.
        if ($request->filled('status')) {
            $query->where(
                'laporan.status_laporan',
                $request->status
            );
        }

        // Filter jenis usaha.
        if ($request->filled('jenis_usaha')) {
            $query->where(
                'usaha.jenis_usaha',
                $request->jenis_usaha
            );
        }

        // Filter periode berdasarkan bulan dan tahun.
        if ($request->filled('periode')) {
            [$tahun, $bulan] = explode('-', $request->periode);

            $query->whereYear('laporan.tanggal_mulai', $tahun)
                ->whereMonth('laporan.tanggal_mulai', $bulan);
        }

        // Sorting.
        $sort = in_array(
            $request->sort,
            $this->allowedSorts,
            true
        )
            ? $request->sort
            : 'created_at';

        $dir = $request->dir === 'asc'
            ? 'asc'
            : 'desc';

        $sortMap = [
            'nama_usaha'  => 'usaha.nama_usaha',
            'omzet'       => 'laporan.omzet',
            'laba_bersih' => 'laporan.laba_bersih',
            'created_at'  => 'laporan.created_at',
        ];

        $query->orderBy(
            $sortMap[$sort] ?? 'laporan.created_at',
            $dir
        );

        $laporanList = $query
            ->with([
                'usaha.anggota.akun',
                'validasi',
            ])
            ->paginate(10)
            ->withQueryString();

        $jumlahMenunggu = Laporan::where(
            'status_laporan',
            'dikirim'
        )->count();

        $periodeList = Laporan::where(
            'status_laporan',
            '!=',
            'draft'
        )
            ->selectRaw(
                "DATE_FORMAT(tanggal_mulai, '%Y-%m') as periode,
                 DATE_FORMAT(tanggal_mulai, '%M %Y') as label"
            )
            ->groupBy('periode', 'label')
            ->orderBy('periode', 'desc')
            ->pluck('label', 'periode');

        return view('pengurus.validasi-laporan.index', [
            'laporanList'    => $laporanList,
            'periodeList'    => $periodeList,
            'jumlahMenunggu' => $jumlahMenunggu,
            'search'         => $request->search,
            'filterStatus'   => $request->status,
            'filterJenis'    => $request->jenis_usaha,
            'filterPeriode'  => $request->periode,
            'sort'           => $sort,
            'dir'            => $dir,
        ]);
    }

    /**
     * Detail laporan dan preview data monitoring.
     */
    public function show(
        Laporan $laporan
    ): View|RedirectResponse {
        if ($laporan->status_laporan === 'draft') {
            return redirect()
                ->route('pengurus.validasi-laporan.index')
                ->with(
                    'error',
                    'Laporan masih berstatus draft dan belum dapat divalidasi.'
                );
        }

        $laporan->load([
            'usaha.anggota.akun',
            'usaha.anggota',
            'validasi.validator',
            'validasi.dps',
        ]);

        $laporanSebelumnya = Laporan::where(
            'usaha_id',
            $laporan->usaha_id
        )
            ->where(
                'periode_ke',
                $laporan->periode_ke - 1
            )
            ->where(
                'status_laporan',
                'divalidasi'
            )
            ->first();

        $omzetSebelumnya = $laporanSebelumnya
            ? (float) $laporanSebelumnya->omzet
            : 0;

        $omzetSekarang = (float) $laporan->omzet;
        $biaya = (float) $laporan->biaya_operasional;

        /*
         * Data berikut hanya preview bagi Pengurus.
         * Pengurus memeriksa kebenaran laporan, sedangkan hasil evaluasi
         * resmi baru dihitung dan disimpan otomatis oleh sistem setelah
         * laporan disetujui Pengurus.
         */
        if ($omzetSebelumnya > 0) {
            $pctKenaikan = round(
                (
                    ($omzetSekarang - $omzetSebelumnya)
                    / $omzetSebelumnya
                ) * 100,
                2
            );
        } else {
            $pctKenaikan = $omzetSekarang > 0
                ? 100.00
                : 0.00;
        }

        $rasioBiaya = $omzetSekarang > 0
            ? round(($biaya / $omzetSekarang) * 100, 2)
            : 100.00;

        $indikatorOmzet = $pctKenaikan >= 5;
        $indikatorBiaya = $rasioBiaya < 60;

        if ($indikatorOmzet && $indikatorBiaya) {
            $prediksiEvaluasi = 'aktif';
        } elseif (! $indikatorOmzet && ! $indikatorBiaya) {
            $prediksiEvaluasi = 'tidak_aktif';
        } else {
            $prediksiEvaluasi = 'peringatan';
        }

        $riwayatLaporan = Laporan::where(
            'usaha_id',
            $laporan->usaha_id
        )
            ->where(
                'periode_ke',
                '<',
                $laporan->periode_ke
            )
            ->orderByDesc('periode_ke')
            ->limit(3)
            ->get();

        return view(
            'pengurus.validasi-laporan.show',
            compact(
                'laporan',
                'laporanSebelumnya',
                'omzetSebelumnya',
                'pctKenaikan',
                'rasioBiaya',
                'indikatorOmzet',
                'indikatorBiaya',
                'prediksiEvaluasi',
                'riwayatLaporan'
            )
        );
    }

    /**
     * Menyetujui laporan secara administratif.
     *
     * Pengurus hanya memvalidasi kelengkapan dan kebenaran laporan.
     * Setelah disetujui, sistem otomatis menjalankan evaluasi periode
     * 14 hari. Laporan monitoring tidak diteruskan kepada DPS.
     */
    public function setujui(
        Request $request,
        Laporan $laporan
    ): RedirectResponse {
        $data = $request->validateWithBag('setujui', [
            'catatan_pengurus' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ], [
            'catatan_pengurus.max' =>
            'Catatan pengurus maksimal 1000 karakter.',
        ]);

        if ($laporan->status_laporan !== 'dikirim') {
            return redirect()
                ->route('pengurus.validasi-laporan.show', $laporan->id)
                ->with('error', 'Laporan ini sudah diproses sebelumnya.');
        }

        $laporan->load('usaha.anggota.akun');

        if (
            $laporan->usaha->status_usaha !== 'aktif'
            || $laporan->usaha->status_validasi_syariah !== 'disetujui'
            || ! $laporan->usaha->tanggal_aktivasi_monitoring
        ) {
            return redirect()
                ->route('pengurus.validasi-laporan.show', $laporan->id)
                ->with(
                    'error',
                    'Laporan tidak dapat disetujui karena usaha belum aktif, akad belum disetujui DPS, atau tanggal aktivasi monitoring belum tersedia.'
                );
        }

        try {
            $hasilEvaluasi = DB::transaction(function () use (
                $laporan,
                $data
            ) {
                $laporanTerkunci = Laporan::query()
                    ->whereKey($laporan->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($laporanTerkunci->status_laporan !== 'dikirim') {
                    throw new \RuntimeException(
                        'Laporan ini sudah diproses sebelumnya.'
                    );
                }

                $laporanSebelumnya = Laporan::query()
                    ->where('usaha_id', $laporanTerkunci->usaha_id)
                    ->where(
                        'periode_ke',
                        $laporanTerkunci->periode_ke - 1
                    )
                    ->where('status_laporan', 'divalidasi')
                    ->first();

                $omzetSebelumnya = $laporanSebelumnya
                    ? (float) $laporanSebelumnya->omzet
                    : 0;

                Validasi::updateOrCreate(
                    ['laporan_id' => $laporanTerkunci->id],
                    [
                        'validator_id'              => Auth::id(),
                        'omzet_sebelumnya'          => $omzetSebelumnya,
                        'tanggal_validasi_pengurus' => now(),
                        'status_validasi_pengurus'  => 'disetujui',
                        'catatan_pengurus'          =>
                        $data['catatan_pengurus'] ?? null,
                        // Kolom DPS dipertahankan untuk kompatibilitas skema lama.
                        // DPS tidak melakukan validasi laporan monitoring.
                        // Kolom DPS dipertahankan untuk kompatibilitas skema lama.
                        // DPS tidak melakukan validasi laporan monitoring.
                        'status_validasi_dps'       => 'pending',
                        'dps_id'                    => null,
                        'tanggal_validasi_dps'      => null,
                        'catatan_dps'               => null,
                        'persentase_kenaikan_omzet' => null,
                        'rasio_biaya'               => null,
                        'indikator_omzet'           => null,
                        'indikator_biaya'           => null,
                        'hasil_evaluasi'            => null,
                    ]
                );

                $laporanTerkunci->update([
                    'status_laporan'   => 'divalidasi',
                    'catatan_pengurus' =>
                    $data['catatan_pengurus'] ?? null,
                ]);

                return $this->evaluasiService->jalankan(
                    $laporanTerkunci->fresh(['usaha', 'validasi']),
                    Auth::id()
                );
            });

            $laporan->refresh()->load('usaha.anggota.akun');

            $pesanAnggota = 'Laporan usaha Anda "'
                . $laporan->usaha->nama_usaha
                . '" periode ke-'
                . $laporan->periode_ke
                . ' telah DISETUJUI oleh Pengurus dan evaluasi otomatis 14 hari telah dijalankan.';

            if (! empty($data['catatan_pengurus'])) {
                $pesanAnggota .= ' Catatan Pengurus: '
                    . $data['catatan_pengurus'];
            }

            $this->notification->notifyAnggota(
                $laporan->usaha->anggota->akun_id,
                'Laporan Disetujui Pengurus',
                $pesanAnggota,
                'sukses'
            );

            AktivitasLog::create([
                'akun_id'    => Auth::id(),
                'aktivitas'  => 'Setujui Laporan',
                'keterangan' => 'Menyetujui laporan periode ke-'
                    . $laporan->periode_ke
                    . ' usaha: '
                    . $laporan->usaha->nama_usaha
                    . ' dan menjalankan evaluasi otomatis.',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()
                ->route('pengurus.validasi-laporan.index')
                ->with(
                    'success',
                    $hasilEvaluasi
                        ? 'Laporan berhasil disetujui dan evaluasi otomatis 14 hari berhasil dijalankan.'
                        : 'Laporan berhasil disetujui. Evaluasi tidak menghasilkan perubahan baru.'
                );
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('pengurus.validasi-laporan.show', $laporan->id)
                ->withInput()
                ->with(
                    'error',
                    $exception instanceof \RuntimeException
                        ? $exception->getMessage()
                        : 'Laporan gagal disetujui. Tidak ada perubahan data yang disimpan. Silakan coba kembali.'
                );
        }
    }

    /**
     * Menolak laporan dan meminta anggota melakukan revisi.
     */
    public function tolak(
        Request $request,
        Laporan $laporan
    ): RedirectResponse {
        $data = $request->validateWithBag('tolak', [
            'catatan_pengurus' => [
                'required',
                'string',
                'min:5',
                'max:1000',
            ],
        ], [
            'catatan_pengurus.required' =>
            'Catatan penolakan wajib diisi.',
            'catatan_pengurus.min' =>
            'Catatan penolakan minimal 5 karakter.',
            'catatan_pengurus.max' =>
            'Catatan penolakan maksimal 1000 karakter.',
        ]);

        if ($laporan->status_laporan !== 'dikirim') {
            return redirect()
                ->route('pengurus.validasi-laporan.show', $laporan->id)
                ->with('error', 'Laporan ini sudah diproses sebelumnya.');
        }

        $laporan->load('usaha.anggota.akun');

        try {
            DB::transaction(function () use ($laporan, $data) {
                $laporanTerkunci = Laporan::query()
                    ->whereKey($laporan->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($laporanTerkunci->status_laporan !== 'dikirim') {
                    throw new \RuntimeException(
                        'Laporan ini sudah diproses sebelumnya.'
                    );
                }

                Validasi::updateOrCreate(
                    ['laporan_id' => $laporanTerkunci->id],
                    [
                        'validator_id'              => Auth::id(),
                        'tanggal_validasi_pengurus' => now(),
                        'status_validasi_pengurus'  => 'ditolak',
                        'catatan_pengurus'          =>
                        $data['catatan_pengurus'],
                        'status_validasi_dps'       => 'pending',
                        'dps_id'                    => null,
                        'tanggal_validasi_dps'      => null,
                        'catatan_dps'               => null,
                        'persentase_kenaikan_omzet' => null,
                        'rasio_biaya'               => null,
                        'indikator_omzet'           => null,
                        'indikator_biaya'           => null,
                        'hasil_evaluasi'            => null,
                    ]
                );

                $laporanTerkunci->update([
                    'status_laporan'   => 'ditolak',
                    'catatan_pengurus' =>
                    $data['catatan_pengurus'],
                ]);
            });

            $laporan->refresh()->load('usaha.anggota.akun');

            $this->notification->notifyAnggota(
                $laporan->usaha->anggota->akun_id,
                'Laporan Ditolak',
                'Laporan usaha Anda "'
                    . $laporan->usaha->nama_usaha
                    . '" periode ke-'
                    . $laporan->periode_ke
                    . ' DITOLAK oleh Pengurus. Alasan: '
                    . $data['catatan_pengurus']
                    . '. Silakan perbaiki dan kirim ulang laporan Anda.',
                'bahaya'
            );

            AktivitasLog::create([
                'akun_id'    => Auth::id(),
                'aktivitas'  => 'Tolak Laporan',
                'keterangan' => 'Menolak laporan periode ke-'
                    . $laporan->periode_ke
                    . ' usaha: '
                    . $laporan->usaha->nama_usaha
                    . ' — alasan: '
                    . $data['catatan_pengurus'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()
                ->route('pengurus.validasi-laporan.index')
                ->with(
                    'success',
                    'Laporan telah ditolak. Anggota akan menerima notifikasi untuk melakukan revisi.'
                );
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('pengurus.validasi-laporan.show', $laporan->id)
                ->withInput()
                ->with(
                    'error',
                    $exception instanceof \RuntimeException
                        ? $exception->getMessage()
                        : 'Laporan gagal ditolak. Tidak ada perubahan data yang disimpan. Silakan coba kembali.'
                );
        }
    }
}
