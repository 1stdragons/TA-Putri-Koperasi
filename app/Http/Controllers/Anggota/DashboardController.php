<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $akun    = Auth::user();
        $anggota = $akun->anggota;

        // Jika belum punya data anggota
        if (! $anggota) {
            return view('anggota.dashboard', [
                'anggota'          => null,
                'usahaAktif'       => null,
                'laporanTerakhir'  => null,
                'evaluasiTerakhir' => null,
                'totalLaporan'     => 0,
                'totalUsaha'       => 0,
                'periodeBerikutnya' => 1,
                'notifBelumDibaca' => 0,
            ]);
        }

        $anggota->load(['usaha.laporan.validasi', 'usaha.validasiAkadTerakhir']);

        // Usaha aktif pertama milik anggota
        $usahaAktif = $anggota->usaha->where('status_usaha', 'aktif')->first()
            ?? $anggota->usaha->first();

        // Laporan terakhir dari usaha aktif
        $laporanTerakhir = null;
        $evaluasiTerakhir = null;
        $periodeBerikutnya = 1;

        if ($usahaAktif) {
            $laporanTerakhir = Laporan::where('usaha_id', $usahaAktif->id)
                ->orderBy('periode_ke', 'desc')
                ->first();

            if ($laporanTerakhir && $laporanTerakhir->validasi) {
                $evaluasiTerakhir = $laporanTerakhir->validasi;
            }

            $periodeBerikutnya = $usahaAktif->periode_berikutnya;
        }

        $totalLaporan = Laporan::whereIn(
            'usaha_id',
            $anggota->usaha->pluck('id')
        )->count();

        $notifBelumDibaca = Notifikasi::where('akun_id', $akun->id)
            ->where('dibaca', false)
            ->count();

        return view('anggota.dashboard', compact(
            'anggota',
            'usahaAktif',
            'laporanTerakhir',
            'evaluasiTerakhir',
            'totalLaporan',
            'periodeBerikutnya',
            'notifBelumDibaca'
        ));
    }
}
