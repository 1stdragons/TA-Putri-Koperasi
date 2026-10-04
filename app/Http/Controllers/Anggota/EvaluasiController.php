<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Usaha;
use App\Models\Validasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EvaluasiController extends Controller
{
    /**
     * Riwayat evaluasi milik anggota yang sedang login.
     */
    public function index(Request $request): View
    {
        $anggota = Auth::user()->anggota;

        if (! $anggota) {
            return view('anggota.evaluasi.index', [
                'evaluasiList' => collect()->paginate(10),
                'stats'        => null,
                'search'       => '',
                'filterHasil'  => '',
                'filterPeriode' => '',
            ]);
        }

        $usahaIds = Usaha::where('anggota_id', $anggota->id)->pluck('id');

        $query = Validasi::query()
            ->join('laporan', 'validasi.laporan_id', '=', 'laporan.id')
            ->join('usaha', 'laporan.usaha_id', '=', 'usaha.id')
            ->select(
                'validasi.*',
                'laporan.periode_ke',
                'laporan.omzet as omzet_sekarang',
                'laporan.biaya_operasional',
                'laporan.tanggal_mulai',
                'laporan.tanggal_selesai',
                'usaha.nama_usaha',
                'usaha.jenis_usaha'
            )
            ->whereIn('laporan.usaha_id', $usahaIds)
            ->where('validasi.status_validasi_pengurus', 'disetujui')
            ->whereNotNull('validasi.hasil_evaluasi');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('usaha.nama_usaha', 'like', "%{$s}%");
        }

        if ($request->filled('hasil')) {
            $query->where('validasi.hasil_evaluasi', $request->hasil);
        }

        if ($request->filled('periode')) {
            [$tahun, $bulan] = explode('-', $request->periode);
            $query->whereYear('laporan.tanggal_mulai', $tahun)
                ->whereMonth('laporan.tanggal_mulai', $bulan);
        }

        $query->orderBy('validasi.tanggal_validasi_pengurus', 'desc');

        $evaluasiList = $query
            ->with(['laporan.usaha', 'validator'])
            ->paginate(10)
            ->withQueryString();

        // Statistik milik anggota ini
        $stats = Validasi::whereIn(
            'laporan_id',
            DB::table('laporan')
                ->whereIn('usaha_id', $usahaIds)
                ->pluck('id')
        )
            ->where('status_validasi_pengurus', 'disetujui')
            ->whereNotNull('hasil_evaluasi')
            ->selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN hasil_evaluasi = 'aktif' THEN 1 ELSE 0 END) as aktif,
            SUM(CASE WHEN hasil_evaluasi = 'peringatan' THEN 1 ELSE 0 END) as peringatan,
            SUM(CASE WHEN hasil_evaluasi = 'tidak_aktif' THEN 1 ELSE 0 END) as tidak_aktif
        ")
            ->first();

        $periodeList = DB::table('laporan')
            ->whereIn('usaha_id', $usahaIds)
            ->selectRaw("DATE_FORMAT(tanggal_mulai, '%Y-%m') as periode, DATE_FORMAT(tanggal_mulai, '%M %Y') as label")
            ->groupBy('periode', 'label')
            ->orderBy('periode', 'desc')
            ->pluck('label', 'periode');

        return view('anggota.evaluasi.index', [
            'evaluasiList'  => $evaluasiList,
            'stats'         => $stats,
            'periodeList'   => $periodeList,
            'search'        => $request->search,
            'filterHasil'   => $request->hasil,
            'filterPeriode' => $request->periode,
        ]);
    }

    /**
     * Detail evaluasi milik anggota (keamanan: cek kepemilikan).
     */
    public function show(Validasi $validasi): View
    {
        $anggota = Auth::user()->anggota;

        $validasi->load(['laporan.usaha.anggota', 'validator', 'dps']);

        // Keamanan
        if (! $anggota || $validasi->laporan->usaha->anggota_id !== $anggota->id) {
            abort(403, 'Anda tidak memiliki akses ke evaluasi ini.');
        }

        $validasi->load([
            'laporan.usaha.anggota.akun',
            'laporan.usaha.validasiAkadTerakhir.dps',
            'validator',
            'dps',
        ]);

        return view('anggota.evaluasi.show', compact('validasi'));
    }
}
