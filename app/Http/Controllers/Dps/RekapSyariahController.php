<?php

namespace App\Http\Controllers\Dps;

use App\Http\Controllers\Controller;
use App\Models\Usaha;
use App\Models\ValidasiAkad;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RekapSyariahController extends Controller
{
    /**
     * Halaman index rekap validasi syariah — read-only untuk DPS.
     */
    public function index(Request $request): View
    {
        $query = $this->buildQuery($request);

        $rekapList = $query->paginate(10)->withQueryString();

        return view('dps.rekap-syariah.index', [
            'rekapList'     => $rekapList,
            'search'        => $request->search,
            'filterAkad'    => $request->jenis_akad,
            'filterStatus'  => $request->status_syariah,
            'filterTglDari' => $request->tgl_dari,
            'filterTglSampai' => $request->tgl_sampai,
        ]);
    }

    /**
     * Unduh PDF rekap validasi syariah.
     */
    public function pdf(Request $request): RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {
        $rows = $this->buildQuery($request)->get();

        if ($rows->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Tidak ada data yang dapat dicetak berdasarkan filter yang dipilih.');
        }

        $totalUsaha       = $rows->count();
        $jumlahDisetujui  = $rows->where('status_validasi_syariah', 'disetujui')->count();
        $jumlahPerluRevisi = $rows->where('status_validasi_syariah', 'perlu_revisi')->count();

        $distribusiAkad = $rows->groupBy('jenis_akad')
            ->map(fn($grp) => $grp->count());

        // Riwayat validasi seluruh usaha yang termasuk dalam filter
        $usahaIds = $rows->pluck('id');
        $riwayat = ValidasiAkad::whereIn('usaha_id', $usahaIds)
            ->with(['usaha.anggota.akun', 'dps'])
            ->orderBy('divalidasi_pada', 'desc')
            ->get();

        $pdf = app('dompdf.wrapper')->loadView('pdf.rekap-validasi-syariah', [
            'rows'              => $rows,
            'riwayat'           => $riwayat,
            'totalUsaha'        => $totalUsaha,
            'jumlahDisetujui'   => $jumlahDisetujui,
            'jumlahPerluRevisi' => $jumlahPerluRevisi,
            'distribusiAkad'    => $distribusiAkad,
            'printedBy'         => Auth::user()->nama,
            'printedAt'         => now()->translatedFormat('d F Y, H:i') . ' WIB',
        ])->setPaper('a4', 'landscape');

        return $pdf->download('rekap-validasi-syariah-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Unduh PDF detail validasi akad satu usaha.
     */
    public function detailPdf(Usaha $usaha): RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {
        $usaha->load([
            'anggota.akun',
            'validasiAkad.dps',
            'validasiAkadTerakhir.dps',
        ]);

        if ($usaha->validasiAkad->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Usaha ini belum memiliki riwayat validasi syariah.');
        }

        $pdf = app('dompdf.wrapper')->loadView('pdf.detail-validasi-syariah', [
            'usaha'     => $usaha,
            'anggota'   => $usaha->anggota,
            'riwayat'   => $usaha->validasiAkad->sortByDesc('iterasi'),
            'printedBy' => Auth::user()->nama,
            'printedAt' => now()->translatedFormat('d F Y, H:i') . ' WIB',
        ])->setPaper('a4', 'portrait');

        $namaFile = 'validasi-syariah-' . preg_replace('/[^A-Za-z0-9\-]/', '-', $usaha->anggota->nomor_anggota) . '.pdf';

        return $pdf->download($namaFile);
    }

    // ── Helper ───────────────────────────────────────────────────

    private function buildQuery(Request $request): Builder
    {
        $query = Usaha::query()
            ->join('anggota', 'usaha.anggota_id', '=', 'anggota.id')
            ->join('akun', 'anggota.akun_id', '=', 'akun.id')
            ->select('usaha.*', 'anggota.nomor_anggota', 'akun.nama as nama_anggota')
            ->whereNotNull('usaha.jenis_akad');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('usaha.nama_usaha', 'like', "%{$s}%")
                    ->orWhere('akun.nama', 'like', "%{$s}%")
                    ->orWhere('anggota.nomor_anggota', 'like', "%{$s}%");
            });
        }

        if ($request->filled('jenis_akad')) {
            $query->where('usaha.jenis_akad', $request->jenis_akad);
        }

        if ($request->filled('status_syariah')) {
            $query->where('usaha.status_validasi_syariah', $request->status_syariah);
        }

        if ($request->filled('tgl_dari') || $request->filled('tgl_sampai')) {
            $query->whereHas('validasiAkadTerakhir', function ($q) use ($request) {
                if ($request->filled('tgl_dari')) {
                    $q->whereDate('divalidasi_pada', '>=', $request->tgl_dari);
                }
                if ($request->filled('tgl_sampai')) {
                    $q->whereDate('divalidasi_pada', '<=', $request->tgl_sampai);
                }
            });
        }

        return $query
            ->with(['anggota.akun', 'validasiAkadTerakhir.dps'])
            ->withCount('validasiAkad')
            ->orderBy('usaha.updated_at', 'desc');
    }
}
