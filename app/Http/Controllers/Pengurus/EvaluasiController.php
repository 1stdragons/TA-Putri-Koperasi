<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Validasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EvaluasiController extends Controller
{
    public function index(Request $request): View
    {
        $query = Validasi::query()
            ->join('laporan', 'validasi.laporan_id', '=', 'laporan.id')
            ->join('usaha', 'laporan.usaha_id', '=', 'usaha.id')
            ->join('anggota', 'usaha.anggota_id', '=', 'anggota.id')
            ->join('akun', 'anggota.akun_id', '=', 'akun.id')
            ->select(
                'validasi.*',
                'laporan.periode_ke',
                'laporan.omzet as omzet_sekarang',
                'laporan.biaya_operasional',
                'laporan.tanggal_mulai',
                'laporan.tanggal_selesai',
                'usaha.nama_usaha',
                'usaha.jenis_usaha',
                'usaha.jenis_akad',
                'anggota.nomor_anggota',
                'akun.nama as nama_anggota'
            )
            ->where('validasi.status_validasi_pengurus', 'disetujui')
            ->whereNotNull('validasi.hasil_evaluasi');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('akun.nama', 'like', "%{$s}%")
                    ->orWhere('anggota.nomor_anggota', 'like', "%{$s}%")
                    ->orWhere('usaha.nama_usaha', 'like', "%{$s}%");
            });
        }

        if ($request->filled('hasil')) {
            $query->where('validasi.hasil_evaluasi', $request->hasil);
        }

        if ($request->filled('jenis_usaha')) {
            $query->where('usaha.jenis_usaha', $request->jenis_usaha);
        }

        if ($request->filled('periode')) {
            [$tahun, $bulan] = explode('-', $request->periode);
            $query->whereYear('laporan.tanggal_mulai', $tahun)
                ->whereMonth('laporan.tanggal_mulai', $bulan);
        }

        $query->orderBy('validasi.tanggal_validasi_pengurus', 'desc');

        $evaluasiList = $query
            ->with(['laporan.usaha', 'validator', 'dps'])
            ->paginate(10)
            ->withQueryString();

        $periodeList = DB::table('laporan')
            ->selectRaw("DATE_FORMAT(tanggal_mulai, '%Y-%m') as periode, DATE_FORMAT(tanggal_mulai, '%M %Y') as label")
            ->groupBy('periode', 'label')
            ->orderBy('periode', 'desc')
            ->pluck('label', 'periode');

        $stats = Validasi::where('status_validasi_pengurus', 'disetujui')
            ->whereNotNull('hasil_evaluasi')
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN hasil_evaluasi = 'aktif' THEN 1 ELSE 0 END) as aktif,
                SUM(CASE WHEN hasil_evaluasi = 'peringatan' THEN 1 ELSE 0 END) as peringatan,
                SUM(CASE WHEN hasil_evaluasi = 'tidak_aktif' THEN 1 ELSE 0 END) as tidak_aktif
            ")
            ->first();

        return view('pengurus.evaluasi.index', [
            'evaluasiList'  => $evaluasiList,
            'periodeList'   => $periodeList,
            'stats'         => $stats,
            'search'        => $request->search,
            'filterHasil'   => $request->hasil,
            'filterJenis'   => $request->jenis_usaha,
            'filterPeriode' => $request->periode,
        ]);
    }

    public function show(Validasi $validasi): View
    {
        $validasi->load([
            'laporan.usaha.anggota.akun',
            'laporan.usaha.validasiAkadTerakhir.dps',
            'validator',
            'dps',
        ]);

        return view('pengurus.evaluasi.show', compact('validasi'));
    }
}
