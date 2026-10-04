<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Validasi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RekapMonitoringController extends Controller
{
    /**
     * Daftar kolom yang diperbolehkan untuk sorting.
     *
     * @var array<int, string>
     */
    private array $allowedSorts = [
        'nama_anggota',
        'nama_usaha',
        'periode_ke',
        'omzet_sekarang',
        'biaya_operasional',
        'laba_bersih',
        'persentase_kenaikan_omzet',
        'rasio_biaya',
        'hasil_evaluasi',
        'tanggal_evaluasi',
    ];

    /**
     * Pemetaan parameter sorting ke kolom database.
     *
     * @var array<string, string>
     */
    private array $sortColumnMap = [
        'nama_anggota'              => 'akun.nama',
        'nama_usaha'                => 'usaha.nama_usaha',
        'periode_ke'                => 'laporan.periode_ke',
        'omzet_sekarang'            => 'laporan.omzet',
        'biaya_operasional'         => 'laporan.biaya_operasional',
        'laba_bersih'               => 'laporan.laba_bersih',
        'persentase_kenaikan_omzet' => 'validasi.persentase_kenaikan_omzet',
        'rasio_biaya'               => 'validasi.rasio_biaya',
        'hasil_evaluasi'            => 'validasi.hasil_evaluasi',
        'tanggal_evaluasi'          => 'validasi.updated_at',
    ];

    /**
     * Menampilkan halaman rekap monitoring.
     *
     * Digunakan bersama oleh Admin, Pengurus, dan Ketua.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        abort_if($user === null, 401);

        $role = $user->role;

        $query = $this->buildQuery($request);

        $sortRaw = (string) $request->input(
            'sort',
            'tanggal_evaluasi'
        );

        $sort = in_array($sortRaw, $this->allowedSorts, true)
            ? $sortRaw
            : 'tanggal_evaluasi';

        $dir = $request->input('dir') === 'asc'
            ? 'asc'
            : 'desc';

        $query->orderBy(
            $this->sortColumnMap[$sort],
            $dir
        );

        $rekapList = $query
            ->paginate(10)
            ->withQueryString();

        $periodeList = DB::table('laporan')
            ->selectRaw(
                "DATE_FORMAT(tanggal_mulai, '%Y-%m') AS periode,
                 DATE_FORMAT(tanggal_mulai, '%M %Y') AS label"
            )
            ->whereNotNull('tanggal_mulai')
            ->groupBy('periode', 'label')
            ->orderByDesc('periode')
            ->pluck('label', 'periode');

        $stats = $this->buildStats($request);

        return view("{$role}.rekap-monitoring.index", [
            'rekapList'              => $rekapList,
            'periodeList'            => $periodeList,
            'stats'                  => $stats,
            'search'                 => $request->input('search'),
            'filterHasil'            => $request->input('hasil'),
            'filterStatus'           => $request->input('status_anggota'),
            'filterJenis'            => $request->input('jenis_usaha'),
            'filterAkad'             => $request->input('jenis_akad'),
            'filterPeriode'          => $request->input('periode'),
            'filterTglMulaiDari'     => $request->input('tgl_mulai_dari'),
            'filterTglMulaiSampai'   => $request->input('tgl_mulai_sampai'),
            'filterTglSelesaiDari'   => $request->input('tgl_selesai_dari'),
            'filterTglSelesaiSampai' => $request->input('tgl_selesai_sampai'),
            'sort'                   => $sort,
            'dir'                    => $dir,
        ]);
    }

    /**
     * Mengunduh PDF rekap monitoring berdasarkan filter aktif.
     */
    public function monitoringPdf(
        Request $request
    ): RedirectResponse|Response {
        $user = $request->user();

        abort_if($user === null, 401);

        $query = $this->buildQuery($request);

        $sortRaw = (string) $request->input(
            'sort',
            'tanggal_evaluasi'
        );

        $sort = in_array($sortRaw, $this->allowedSorts, true)
            ? $sortRaw
            : 'tanggal_evaluasi';

        $dir = $request->input('dir') === 'asc'
            ? 'asc'
            : 'desc';

        $query->orderBy(
            $this->sortColumnMap[$sort],
            $dir
        );

        $rows = $query->get();

        if ($rows->isEmpty()) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Tidak ada data yang dapat dicetak berdasarkan filter yang dipilih.'
                );
        }

        $stats = $this->buildStatsFromRows($rows);

        $pdf = Pdf::loadView('pdf.rekap-monitoring', [
            'rows'        => $rows,
            'stats'       => $stats,
            'filterLabel' => $this->buildFilterLabel($request),
            'printedBy'   => $user->nama,
            'printedRole' => ucfirst((string) $user->role),
            'printedAt'   => now()
                ->translatedFormat('d F Y, H:i') . ' WIB',
        ])->setPaper('a4', 'landscape');

        $namaFile = 'rekap-monitoring-'
            . now()->format('Y-m-d')
            . '.pdf';

        return $pdf->download($namaFile);
    }

    /**
     * Mengunduh PDF detail satu hasil monitoring.
     */
    public function detailPdf(
        Laporan $laporan
    ): RedirectResponse|Response {
        $user = Auth::user();

        abort_if($user === null, 401);

        $laporan->load([
            'usaha.anggota.akun',
            'usaha.validasiAkadTerakhir.dps',
            'validasi.validator',
            'validasi.dps',
        ]);

        if (
            $laporan->validasi === null
            || empty($laporan->validasi->hasil_evaluasi)
        ) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Laporan ini belum memiliki hasil evaluasi lengkap.'
                );
        }

        $kesimpulan = match ($laporan->validasi->hasil_evaluasi) {
            'aktif' =>
            'Usaha memenuhi indikator perkembangan omzet dan efisiensi biaya operasional pada periode monitoring ini.',

            'peringatan' =>
            'Usaha hanya memenuhi salah satu indikator evaluasi dan memerlukan peningkatan pada periode berikutnya.',

            'tidak_aktif' =>
            'Usaha belum memenuhi indikator perkembangan omzet maupun efisiensi biaya operasional dan memerlukan perhatian lebih lanjut.',

            default => '-',
        };

        $pdf = Pdf::loadView('pdf.detail-monitoring', [
            'laporan'     => $laporan,
            'validasi'    => $laporan->validasi,
            'usaha'       => $laporan->usaha,
            'anggota'     => $laporan->usaha->anggota,
            'kesimpulan'  => $kesimpulan,
            'printedBy'   => $user->nama,
            'printedRole' => ucfirst((string) $user->role),
            'printedAt'   => now()
                ->translatedFormat('d F Y, H:i') . ' WIB',
        ])->setPaper('a4', 'portrait');

        $nomorAnggota = (string) (
            $laporan->usaha
            ->anggota
            ->nomor_anggota
            ?? 'anggota'
        );

        $namaFile = 'hasil-monitoring-'
            . $this->sanitizeFilename($nomorAnggota)
            . '-periode-'
            . $laporan->periode_ke
            . '.pdf';

        return $pdf->download($namaFile);
    }

    /**
     * Membentuk query utama rekap monitoring.
     */
    private function buildQuery(Request $request): Builder
    {
        $query = Validasi::query()
            ->join(
                'laporan',
                'validasi.laporan_id',
                '=',
                'laporan.id'
            )
            ->join(
                'usaha',
                'laporan.usaha_id',
                '=',
                'usaha.id'
            )
            ->join(
                'anggota',
                'usaha.anggota_id',
                '=',
                'anggota.id'
            )
            ->join(
                'akun',
                'anggota.akun_id',
                '=',
                'akun.id'
            )
            ->select([
                'validasi.*',
                'laporan.periode_ke',
                'laporan.tanggal_mulai',
                'laporan.tanggal_selesai',
                'laporan.omzet as omzet_sekarang',
                'laporan.biaya_operasional',
                'laporan.laba_bersih',
                'validasi.updated_at as tanggal_evaluasi',
                'usaha.nama_usaha',
                'usaha.jenis_usaha',
                'usaha.jenis_akad',
                'usaha.status_validasi_syariah',
                'anggota.nomor_anggota',
                'anggota.status_anggota',
                'akun.nama as nama_anggota',
            ])
            ->where(
                'validasi.status_validasi_pengurus',
                'disetujui'
            )
            ->where(
                'laporan.status_laporan',
                'divalidasi'
            )
            ->where(
                'usaha.status_validasi_syariah',
                'disetujui'
            )
            ->whereNotNull('validasi.hasil_evaluasi');

        if ($request->filled('search')) {
            $search = trim(
                (string) $request->input('search')
            );

            $query->where(
                function (Builder $subQuery) use ($search): void {
                    $subQuery
                        ->where(
                            'akun.nama',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'anggota.nomor_anggota',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'usaha.nama_usaha',
                            'like',
                            "%{$search}%"
                        );
                }
            );
        }

        if ($request->filled('hasil')) {
            $query->where(
                'validasi.hasil_evaluasi',
                $request->input('hasil')
            );
        }

        if ($request->filled('status_anggota')) {
            $query->where(
                'anggota.status_anggota',
                $request->input('status_anggota')
            );
        }

        if ($request->filled('jenis_usaha')) {
            $query->where(
                'usaha.jenis_usaha',
                $request->input('jenis_usaha')
            );
        }

        if ($request->filled('jenis_akad')) {
            $query->where(
                'usaha.jenis_akad',
                $request->input('jenis_akad')
            );
        }

        if ($request->filled('periode')) {
            $periode = (string) $request->input('periode');

            if (
                preg_match(
                    '/^\d{4}-(0[1-9]|1[0-2])$/',
                    $periode
                ) === 1
            ) {
                [$tahun, $bulan] = explode(
                    '-',
                    $periode,
                    2
                );

                $query
                    ->whereYear(
                        'laporan.tanggal_mulai',
                        (int) $tahun
                    )
                    ->whereMonth(
                        'laporan.tanggal_mulai',
                        (int) $bulan
                    );
            }
        }

        if ($request->filled('tgl_mulai_dari')) {
            $query->whereDate(
                'laporan.tanggal_mulai',
                '>=',
                $request->input('tgl_mulai_dari')
            );
        }

        if ($request->filled('tgl_mulai_sampai')) {
            $query->whereDate(
                'laporan.tanggal_mulai',
                '<=',
                $request->input('tgl_mulai_sampai')
            );
        }

        if ($request->filled('tgl_selesai_dari')) {
            $query->whereDate(
                'laporan.tanggal_selesai',
                '>=',
                $request->input('tgl_selesai_dari')
            );
        }

        if ($request->filled('tgl_selesai_sampai')) {
            $query->whereDate(
                'laporan.tanggal_selesai',
                '<=',
                $request->input('tgl_selesai_sampai')
            );
        }

        return $query->with([
            'laporan.usaha.anggota.akun',
            'validator',
            'dps',
        ]);
    }

    /**
     * Menghitung statistik sesuai filter aktif.
     */
    private function buildStats(Request $request): object
    {
        $query = $this->buildQuery($request);

        $aggregateQuery = (clone $query)
            ->toBase()
            ->reorder()
            ->select([]);

        $aggregate = $aggregateQuery
            ->selectRaw(
                "
                COUNT(*) AS total_laporan,
                COUNT(DISTINCT anggota.id) AS total_anggota,
                COUNT(DISTINCT usaha.id) AS total_usaha,
                SUM(
                    CASE
                        WHEN validasi.hasil_evaluasi = 'aktif'
                        THEN 1
                        ELSE 0
                    END
                ) AS jumlah_aktif,
                SUM(
                    CASE
                        WHEN validasi.hasil_evaluasi = 'peringatan'
                        THEN 1
                        ELSE 0
                    END
                ) AS jumlah_peringatan,
                SUM(
                    CASE
                        WHEN validasi.hasil_evaluasi = 'tidak_aktif'
                        THEN 1
                        ELSE 0
                    END
                ) AS jumlah_tidak_aktif,
                COALESCE(SUM(laporan.omzet), 0) AS total_omzet,
                COALESCE(
                    SUM(laporan.biaya_operasional),
                    0
                ) AS total_biaya,
                COALESCE(
                    SUM(laporan.laba_bersih),
                    0
                ) AS total_laba
                "
            )
            ->first();

        return (object) [
            'total_laporan' => (int) (
                $aggregate->total_laporan ?? 0
            ),

            'total_anggota' => (int) (
                $aggregate->total_anggota ?? 0
            ),

            'total_usaha' => (int) (
                $aggregate->total_usaha ?? 0
            ),

            'jumlah_aktif' => (int) (
                $aggregate->jumlah_aktif ?? 0
            ),

            'jumlah_peringatan' => (int) (
                $aggregate->jumlah_peringatan ?? 0
            ),

            'jumlah_tidak_aktif' => (int) (
                $aggregate->jumlah_tidak_aktif ?? 0
            ),

            'total_omzet' => (float) (
                $aggregate->total_omzet ?? 0
            ),

            'total_biaya' => (float) (
                $aggregate->total_biaya ?? 0
            ),

            'total_laba' => (float) (
                $aggregate->total_laba ?? 0
            ),
        ];
    }

    /**
     * Menghitung statistik berdasarkan collection hasil query.
     *
     * @param Collection<int, mixed> $rows
     */
    private function buildStatsFromRows(
        Collection $rows
    ): object {
        return (object) [
            'total_laporan' => $rows->count(),

            'total_anggota' => $rows
                ->pluck('nomor_anggota')
                ->filter()
                ->unique()
                ->count(),

            'total_usaha' => $rows
                ->pluck('nama_usaha')
                ->filter()
                ->unique()
                ->count(),

            'jumlah_aktif' => $rows
                ->where('hasil_evaluasi', 'aktif')
                ->count(),

            'jumlah_peringatan' => $rows
                ->where('hasil_evaluasi', 'peringatan')
                ->count(),

            'jumlah_tidak_aktif' => $rows
                ->where('hasil_evaluasi', 'tidak_aktif')
                ->count(),

            'total_omzet' => (float) $rows->sum(
                'omzet_sekarang'
            ),

            'total_biaya' => (float) $rows->sum(
                'biaya_operasional'
            ),

            'total_laba' => (float) $rows->sum(
                'laba_bersih'
            ),
        ];
    }

    /**
     * Membentuk keterangan filter untuk PDF.
     */
    private function buildFilterLabel(
        Request $request
    ): string {
        $parts = [];

        if ($request->filled('search')) {
            $parts[] = 'Pencarian: "'
                . $request->input('search')
                . '"';
        }

        if ($request->filled('hasil')) {
            $parts[] = 'Hasil: '
                . ucfirst(
                    str_replace(
                        '_',
                        ' ',
                        (string) $request->input('hasil')
                    )
                );
        }

        if ($request->filled('status_anggota')) {
            $parts[] = 'Status Anggota: '
                . ucfirst(
                    str_replace(
                        '_',
                        ' ',
                        (string) $request->input(
                            'status_anggota'
                        )
                    )
                );
        }

        if ($request->filled('jenis_usaha')) {
            $parts[] = 'Jenis Usaha: '
                . ucfirst(
                    (string) $request->input(
                        'jenis_usaha'
                    )
                );
        }

        if ($request->filled('jenis_akad')) {
            $parts[] = 'Jenis Akad: '
                . $request->input('jenis_akad');
        }

        if ($request->filled('periode')) {
            $parts[] = 'Periode: '
                . $request->input('periode');
        }

        if ($request->filled('tgl_mulai_dari')) {
            $parts[] = 'Mulai dari: '
                . $request->input('tgl_mulai_dari');
        }

        if ($request->filled('tgl_mulai_sampai')) {
            $parts[] = 'Mulai sampai: '
                . $request->input('tgl_mulai_sampai');
        }

        if ($request->filled('tgl_selesai_dari')) {
            $parts[] = 'Selesai dari: '
                . $request->input('tgl_selesai_dari');
        }

        if ($request->filled('tgl_selesai_sampai')) {
            $parts[] = 'Selesai sampai: '
                . $request->input('tgl_selesai_sampai');
        }

        return $parts !== []
            ? implode(' | ', $parts)
            : 'Seluruh Data (Tanpa Filter)';
    }

    /**
     * Membersihkan nilai agar aman digunakan sebagai nama file.
     */
    private function sanitizeFilename(string $value): string
    {
        $filename = preg_replace(
            '/[^A-Za-z0-9\-]/',
            '-',
            $value
        );

        $filename = trim(
            (string) $filename,
            '-'
        );

        return $filename !== ''
            ? $filename
            : 'anggota';
    }
}
