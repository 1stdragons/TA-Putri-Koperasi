<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Laporan;
use App\Models\Notifikasi;
use App\Models\Validasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama Pengurus.
     */
    public function index(): View
    {
        $totalAnggotaAktif = Anggota::query()
            ->where('status_anggota', 'aktif')
            ->count();

        $totalLaporanMenunggu = Laporan::query()
            ->where('status_laporan', 'dikirim')
            ->count();

        $totalLaporanDisetujui = Laporan::query()
            ->where('status_laporan', 'divalidasi')
            ->count();

        $totalLaporanDitolak = Laporan::query()
            ->where('status_laporan', 'ditolak')
            ->count();

        $laporanMenunggu = Laporan::query()
            ->where('status_laporan', 'dikirim')
            ->with(['usaha.anggota.akun'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $totalEvaluasiAktif = Validasi::query()
            ->where('hasil_evaluasi', 'aktif')
            ->count();

        $totalEvaluasiPeringatan = Validasi::query()
            ->where('hasil_evaluasi', 'peringatan')
            ->count();

        $totalEvaluasiTidakAktif = Validasi::query()
            ->where('hasil_evaluasi', 'tidak_aktif')
            ->count();

        $totalEvaluasi = $totalEvaluasiAktif
            + $totalEvaluasiPeringatan
            + $totalEvaluasiTidakAktif;

        $persentaseEvaluasiAktif = $totalEvaluasi > 0
            ? round(($totalEvaluasiAktif / $totalEvaluasi) * 100, 1)
            : 0;

        $persentaseEvaluasiPeringatan = $totalEvaluasi > 0
            ? round(($totalEvaluasiPeringatan / $totalEvaluasi) * 100, 1)
            : 0;

        $persentaseEvaluasiTidakAktif = $totalEvaluasi > 0
            ? round(($totalEvaluasiTidakAktif / $totalEvaluasi) * 100, 1)
            : 0;

        $grafikOmzet = Laporan::query()
            ->where('status_laporan', 'divalidasi')
            ->selectRaw("DATE_FORMAT(tanggal_selesai, '%Y-%m') AS periode")
            ->selectRaw("DATE_FORMAT(tanggal_selesai, '%b %Y') AS label")
            ->selectRaw('AVG(omzet) AS rata_rata_omzet')
            ->groupBy('periode', 'label')
            ->orderByDesc('periode')
            ->limit(6)
            ->get()
            ->sortBy('periode')
            ->values();

        $grafikOmzetLabels = $grafikOmzet
            ->pluck('label')
            ->values()
            ->all();

        $grafikOmzetData = $grafikOmzet
            ->map(fn($item) => round((float) $item->rata_rata_omzet, 2))
            ->values()
            ->all();

        $notifikasiTerbaru = Notifikasi::query()
            ->where('akun_id', Auth::id())
            ->orderByDesc('created_at')
            ->limit(4)
            ->get();

        return view('pengurus.dashboard', [
            'totalAnggotaAktif' => $totalAnggotaAktif,
            'totalLaporanMenunggu' => $totalLaporanMenunggu,
            'totalLaporanDisetujui' => $totalLaporanDisetujui,
            'totalLaporanDitolak' => $totalLaporanDitolak,
            'laporanMenunggu' => $laporanMenunggu,
            'totalEvaluasi' => $totalEvaluasi,
            'totalEvaluasiAktif' => $totalEvaluasiAktif,
            'totalEvaluasiPeringatan' => $totalEvaluasiPeringatan,
            'totalEvaluasiTidakAktif' => $totalEvaluasiTidakAktif,
            'persentaseEvaluasiAktif' => $persentaseEvaluasiAktif,
            'persentaseEvaluasiPeringatan' => $persentaseEvaluasiPeringatan,
            'persentaseEvaluasiTidakAktif' => $persentaseEvaluasiTidakAktif,
            'grafikOmzetLabels' => $grafikOmzetLabels,
            'grafikOmzetData' => $grafikOmzetData,
            'notifikasiTerbaru' => $notifikasiTerbaru,
        ]);
    }
}
