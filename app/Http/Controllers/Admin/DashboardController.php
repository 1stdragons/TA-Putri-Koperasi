<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\AktivitasLog;
use App\Models\Anggota;
use App\Models\Laporan;
use App\Models\Notifikasi;
use App\Models\Usaha;
use App\Models\Validasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $statistikAnggota = Anggota::query()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status_anggota = 'aktif' THEN 1 ELSE 0 END) as aktif")
            ->selectRaw("SUM(CASE WHEN status_anggota = 'peringatan' THEN 1 ELSE 0 END) as peringatan")
            ->selectRaw("SUM(CASE WHEN status_anggota = 'tidak_aktif' THEN 1 ELSE 0 END) as tidak_aktif")
            ->first();

        $statistikLaporan = Laporan::query()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status_laporan = 'draft' THEN 1 ELSE 0 END) as draft")
            ->selectRaw("SUM(CASE WHEN status_laporan = 'dikirim' THEN 1 ELSE 0 END) as dikirim")
            ->selectRaw("SUM(CASE WHEN status_laporan = 'divalidasi' THEN 1 ELSE 0 END) as divalidasi")
            ->selectRaw("SUM(CASE WHEN status_laporan = 'ditolak' THEN 1 ELSE 0 END) as ditolak")
            ->first();

        $statistikEvaluasi = Validasi::query()
            ->selectRaw("SUM(CASE WHEN hasil_evaluasi = 'aktif' THEN 1 ELSE 0 END) as aktif")
            ->selectRaw("SUM(CASE WHEN hasil_evaluasi = 'peringatan' THEN 1 ELSE 0 END) as peringatan")
            ->selectRaw("SUM(CASE WHEN hasil_evaluasi = 'tidak_aktif' THEN 1 ELSE 0 END) as tidak_aktif")
            ->first();

        $totalAkun = Akun::query()->count();
        $totalUsahaAktif = Usaha::query()
            ->where('status_usaha', 'aktif')
            ->count();

        $totalAnggota = (int) ($statistikAnggota->total ?? 0);
        $totalAnggotaAktif = (int) ($statistikAnggota->aktif ?? 0);
        $totalAnggotaPeringatan = (int) ($statistikAnggota->peringatan ?? 0);
        $totalAnggotaTidakAktif = (int) ($statistikAnggota->tidak_aktif ?? 0);

        $totalLaporan = (int) ($statistikLaporan->total ?? 0);
        $totalLaporanDraft = (int) ($statistikLaporan->draft ?? 0);
        $totalLaporanPending = (int) ($statistikLaporan->dikirim ?? 0);
        $totalLaporanDivalidasi = (int) ($statistikLaporan->divalidasi ?? 0);
        $totalLaporanDitolak = (int) ($statistikLaporan->ditolak ?? 0);

        $totalEvaluasiAktif = (int) ($statistikEvaluasi->aktif ?? 0);
        $totalEvaluasiPeringatan = (int) ($statistikEvaluasi->peringatan ?? 0);
        $totalEvaluasiTidakAktif = (int) ($statistikEvaluasi->tidak_aktif ?? 0);

        $grafikStatusAnggota = [
            'labels' => ['Aktif', 'Peringatan', 'Tidak Aktif'],
            'data' => [
                $totalAnggotaAktif,
                $totalAnggotaPeringatan,
                $totalAnggotaTidakAktif,
            ],
            'colors' => ['#1e8449', '#f39c12', '#c0392b'],
        ];

        $laporanPerPeriode = Laporan::query()
            ->select('periode_ke')
            ->selectRaw('COUNT(*) as jumlah')
            ->selectRaw("SUM(CASE WHEN status_laporan = 'divalidasi' THEN 1 ELSE 0 END) as divalidasi")
            ->selectRaw("SUM(CASE WHEN status_laporan = 'dikirim' THEN 1 ELSE 0 END) as pending")
            ->selectRaw("SUM(CASE WHEN status_laporan = 'ditolak' THEN 1 ELSE 0 END) as ditolak")
            ->groupBy('periode_ke')
            ->orderByDesc('periode_ke')
            ->limit(12)
            ->get()
            ->sortBy('periode_ke')
            ->values();

        $grafikLaporanPeriode = [
            'labels' => $laporanPerPeriode
                ->pluck('periode_ke')
                ->map(fn($periode) => 'Periode ' . $periode)
                ->values()
                ->all(),
            'divalidasi' => $laporanPerPeriode
                ->pluck('divalidasi')
                ->map(fn($nilai) => (int) $nilai)
                ->values()
                ->all(),
            'pending' => $laporanPerPeriode
                ->pluck('pending')
                ->map(fn($nilai) => (int) $nilai)
                ->values()
                ->all(),
            'ditolak' => $laporanPerPeriode
                ->pluck('ditolak')
                ->map(fn($nilai) => (int) $nilai)
                ->values()
                ->all(),
        ];

        $laporanTerbaru = Laporan::query()
            ->select([
                'id',
                'usaha_id',
                'periode_ke',
                'tanggal_mulai',
                'tanggal_selesai',
                'omzet',
                'biaya_operasional',
                'laba_bersih',
                'status_laporan',
                'created_at',
            ])
            ->with([
                'usaha:id,anggota_id,nama_usaha,jenis_usaha',
                'usaha.anggota:id,akun_id,nomor_anggota',
                'usaha.anggota.akun:id,nama',
                'validasi:id,laporan_id,hasil_evaluasi',
            ])
            ->latest('created_at')
            ->limit(10)
            ->get();

        $aktivitasTerbaru = AktivitasLog::query()
            ->select([
                'id',
                'akun_id',
                'aktivitas',
                'keterangan',
                'created_at',
            ])
            ->with('akun:id,nama,role')
            ->whereIn('aktivitas', ['Login', 'Logout'])
            ->latest('created_at')
            ->limit(10)
            ->get();

        $notifikasiTerbaru = Notifikasi::query()
            ->select([
                'id',
                'akun_id',
                'judul',
                'pesan',
                'tipe',
                'dibaca',
                'created_at',
            ])
            ->where('akun_id', Auth::id())
            ->latest('created_at')
            ->limit(5)
            ->get();

        $jumlahNotifikasiBelumDibaca = Notifikasi::query()
            ->where('akun_id', Auth::id())
            ->where('dibaca', false)
            ->count();

        return view('admin.dashboard', compact(
            'totalAkun',
            'totalAnggota',
            'totalAnggotaAktif',
            'totalAnggotaPeringatan',
            'totalAnggotaTidakAktif',
            'totalUsahaAktif',
            'totalLaporan',
            'totalLaporanPending',
            'totalLaporanDivalidasi',
            'totalLaporanDitolak',
            'totalLaporanDraft',
            'grafikStatusAnggota',
            'grafikLaporanPeriode',
            'laporanTerbaru',
            'aktivitasTerbaru',
            'notifikasiTerbaru',
            'jumlahNotifikasiBelumDibaca',
            'totalEvaluasiAktif',
            'totalEvaluasiPeringatan',
            'totalEvaluasiTidakAktif',
        ));
    }
}
