<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Usaha;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RekapLaporanController extends Controller
{
    /**
     * Menampilkan rekap laporan pribadi milik anggota yang sedang login.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        abort_if($user === null, 401);

        $anggota = $user->anggota;

        if ($anggota === null) {
            $rekapList = new LengthAwarePaginator(
                items: [],
                total: 0,
                perPage: 10,
                currentPage: LengthAwarePaginator::resolveCurrentPage(),
                options: [
                    'path'  => $request->url(),
                    'query' => $request->query(),
                ]
            );

            return view('anggota.rekap.index', [
                'rekapList'     => $rekapList,
                'search'        => '',
                'filterStatus'  => '',
                'filterPeriode' => '',
                'periodeList'   => collect(),
            ]);
        }

        $rekapList = $this
            ->buildQuery($request, (int) $anggota->id)
            ->paginate(10)
            ->withQueryString();

        $usahaIds = Usaha::query()
            ->where('anggota_id', $anggota->id)
            ->pluck('id');

        $periodeList = DB::table('laporan')
            ->whereIn('usaha_id', $usahaIds)
            ->whereNotNull('tanggal_mulai')
            ->selectRaw(
                "DATE_FORMAT(tanggal_mulai, '%Y-%m') AS periode,
                 DATE_FORMAT(tanggal_mulai, '%M %Y') AS label"
            )
            ->groupBy('periode', 'label')
            ->orderByDesc('periode')
            ->pluck('label', 'periode');

        return view('anggota.rekap.index', [
            'rekapList'     => $rekapList,
            'search'        => $request->input('search'),
            'filterStatus'  => $request->input('status'),
            'filterPeriode' => $request->input('periode'),
            'periodeList'   => $periodeList,
        ]);
    }

    /**
     * Mengunduh PDF rekap seluruh laporan milik anggota.
     */
    public function pdf(Request $request): RedirectResponse|Response
    {
        $user = $request->user();

        abort_if($user === null, 401);

        $anggota = $user->anggota;

        if ($anggota === null) {
            return redirect()
                ->route('anggota.dashboard')
                ->with(
                    'error',
                    'Data anggota Anda belum terdaftar.'
                );
        }

        $rows = $this
            ->buildQuery($request, (int) $anggota->id)
            ->whereNotNull('validasi.hasil_evaluasi')
            ->where(
                'validasi.status_validasi_pengurus',
                'disetujui'
            )
            ->where(
                'usaha.status_validasi_syariah',
                'disetujui'
            )
            ->get();

        if ($rows->isEmpty()) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Tidak ada data yang dapat dicetak berdasarkan filter yang dipilih.'
                );
        }

        $stats = (object) [
            'total_laporan' => $rows->count(),

            'total_anggota' => 1,

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

        $pdf = Pdf::loadView('pdf.rekap-monitoring', [
            'rows'        => $rows,
            'stats'       => $stats,
            'filterLabel' => 'Rekap Pribadi Anggota: '
                . $user->nama,
            'printedBy'   => $user->nama,
            'printedRole' => 'Anggota',
            'printedAt'   => now()
                ->translatedFormat('d F Y, H:i')
                . ' WIB',
        ])->setPaper('a4', 'landscape');

        $namaFile = 'rekap-laporan-saya-'
            . now()->format('Y-m-d')
            . '.pdf';

        return $pdf->download($namaFile);
    }

    /**
     * Mengunduh PDF detail satu laporan milik anggota login.
     */
    public function detailPdf(
        Laporan $laporan
    ): RedirectResponse|Response {
        $user = Auth::user();

        abort_if($user === null, 401);

        $anggota = $user->anggota;

        if ($anggota === null) {
            abort(403, 'Data anggota tidak ditemukan.');
        }

        $laporan->load([
            'usaha.anggota.akun',
            'usaha.validasiAkadTerakhir.dps',
            'validasi.validator',
            'validasi.dps',
        ]);

        if (
            $laporan->usaha === null
            || (int) $laporan->usaha->anggota_id
            !== (int) $anggota->id
        ) {
            abort(
                403,
                'Anda tidak memiliki akses ke laporan ini.'
            );
        }

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
            'printedRole' => 'Anggota',
            'printedAt'   => now()
                ->translatedFormat('d F Y, H:i')
                . ' WIB',
        ])->setPaper('a4', 'portrait');

        $nomorAnggota = (string) (
            $anggota->nomor_anggota
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
     * Membentuk query laporan berdasarkan anggota login.
     *
     * @return Builder<Laporan>
     */
    private function buildQuery(
        Request $request,
        int $anggotaId
    ): Builder {
        $query = Laporan::query();

        $query
            ->join(
                'usaha',
                'laporan.usaha_id',
                '=',
                'usaha.id'
            )
            ->leftJoin(
                'validasi',
                'laporan.id',
                '=',
                'validasi.laporan_id'
            )
            ->where(
                'usaha.anggota_id',
                $anggotaId
            )
            ->select([
                'laporan.*',
                'laporan.omzet as omzet_sekarang',
                'validasi.hasil_evaluasi',
                'validasi.persentase_kenaikan_omzet',
                'validasi.rasio_biaya',
                'validasi.updated_at as tanggal_evaluasi',
                'usaha.nama_usaha',
                'usaha.jenis_usaha',
                'usaha.status_validasi_syariah',
            ]);

        if ($request->filled('search')) {
            $search = trim(
                (string) $request->input('search')
            );

            $query->where(
                'usaha.nama_usaha',
                'like',
                "%{$search}%"
            );
        }

        if ($request->filled('status')) {
            $query->where(
                'laporan.status_laporan',
                $request->input('status')
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

        $query->with([
            'usaha',
            'validasi',
        ]);

        $query->orderByDesc(
            'laporan.periode_ke'
        );

        return $query;
    }

    /**
     * Membersihkan nilai untuk digunakan sebagai nama file.
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
