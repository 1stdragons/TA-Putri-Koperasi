<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Laporan;
use App\Models\Usaha;
use App\Models\ValidasiAkad;
use App\Models\Validasi;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // ── Statistik Anggota ────────────────────────────────────
        $totalAnggota    = Anggota::count();
        $anggotaAktif    = Anggota::where('status_anggota', 'aktif')->count();
        $anggotaPeringatan = Anggota::where('status_anggota', 'peringatan')->count();
        $anggotaTidakAktif = Anggota::where('status_anggota', 'tidak_aktif')->count();

        // ── Statistik Usaha & Laporan ────────────────────────────
        $totalUsahaAktif  = Usaha::where('status_usaha', 'aktif')->count();
        $totalLaporan     = Laporan::count();
        $laporanDievaluasi = Validasi::where('status_validasi_pengurus', 'disetujui')
            ->whereNotNull('hasil_evaluasi')
            ->count();
        $usahaMenungguDps = Usaha::where('status_validasi_syariah', 'menunggu')
            ->whereHas('laporan', fn($q) => $q->where('status_laporan', 'divalidasi'))
            ->count();

        // ── Grafik A: Perkembangan Laporan per Periode (12 terakhir) ──
        $grafikLaporan = DB::table('laporan')
            ->selectRaw("
                periode_ke,
                MIN(DATE_FORMAT(tanggal_mulai, '%b %Y')) as label,
                SUM(CASE WHEN status_laporan = 'dikirim' THEN 1 ELSE 0 END) as dikirim,
                SUM(CASE WHEN status_laporan = 'divalidasi' THEN 1 ELSE 0 END) as divalidasi,
                SUM(CASE WHEN status_laporan = 'ditolak' THEN 1 ELSE 0 END) as ditolak
            ")
            ->groupBy('periode_ke')
            ->orderBy('periode_ke', 'desc')
            ->limit(12)
            ->get()
            ->reverse()
            ->values();

        // Jumlah laporan sudah dievaluasi per periode
        $grafikEvaluasiPerPeriode = DB::table('validasi')
            ->join('laporan', 'validasi.laporan_id', '=', 'laporan.id')
            ->selectRaw("laporan.periode_ke, COUNT(*) as total")
            ->where('validasi.status_validasi_pengurus', 'disetujui')
            ->whereNotNull('validasi.hasil_evaluasi')
            ->groupBy('laporan.periode_ke')
            ->orderBy('laporan.periode_ke', 'desc')
            ->limit(12)
            ->get()
            ->keyBy('periode_ke');

        // Gabungkan data evaluasi ke grafik laporan
        $grafikLaporan = $grafikLaporan->map(function ($item) use ($grafikEvaluasiPerPeriode) {
            $item->dievaluasi = $grafikEvaluasiPerPeriode->get($item->periode_ke)?->total ?? 0;
            return $item;
        });

        // ── Grafik B: Status Anggota (doughnut) ─────────────────
        $grafikStatusAnggota = [
            'aktif'       => $anggotaAktif,
            'peringatan'  => $anggotaPeringatan,
            'tidak_aktif' => $anggotaTidakAktif,
        ];

        // ── Grafik C: Distribusi Jenis Akad ─────────────────────
        $grafikJenisAkad = Usaha::whereNotNull('jenis_akad')
            ->selectRaw("jenis_akad, COUNT(*) as total")
            ->groupBy('jenis_akad')
            ->pluck('total', 'jenis_akad');

        // ── Grafik D: Hasil Evaluasi (doughnut) ─────────────────
        $grafikHasilEvaluasi = Validasi::where('status_validasi_pengurus', 'disetujui')
            ->whereNotNull('hasil_evaluasi')
            ->selectRaw("hasil_evaluasi, COUNT(*) as total")
            ->groupBy('hasil_evaluasi')
            ->pluck('total', 'hasil_evaluasi');

        // ── Grafik E: Perkembangan Omzet per Periode ────────────
        $grafikOmzet = DB::table('laporan')
            ->join('validasi', 'laporan.id', '=', 'validasi.laporan_id')
            ->selectRaw("
                laporan.periode_ke,
                MIN(DATE_FORMAT(laporan.tanggal_mulai, '%b %Y')) as label,
                SUM(laporan.omzet) as total_omzet
            ")
            ->where('validasi.status_validasi_pengurus', 'disetujui')
            ->whereNotNull('validasi.hasil_evaluasi')
            ->groupBy('laporan.periode_ke')
            ->orderBy('laporan.periode_ke', 'desc')
            ->limit(12)
            ->get()
            ->reverse()
            ->values();

        // ── Fitur 5: Anggota Perlu Perhatian ────────────────────
        $anggotaPerhatian = Anggota::whereIn('status_anggota', ['peringatan', 'tidak_aktif'])
            ->with([
                'akun',
                'usaha' => fn($q) => $q->with([
                    'laporan' => fn($lq) => $lq
                        ->join('validasi', 'laporan.id', '=', 'validasi.laporan_id')
                        ->where('validasi.status_validasi_pengurus', 'disetujui')
                        ->whereNotNull('validasi.hasil_evaluasi')
                        ->select('laporan.*')
                        ->orderBy('laporan.periode_ke', 'desc')
                ]),
            ])
            ->orderByRaw("CASE status_anggota WHEN 'tidak_aktif' THEN 1 WHEN 'peringatan' THEN 2 ELSE 3 END")
            ->limit(10)
            ->get();

        // Ambil evaluasi terakhir untuk setiap anggota dalam perhatian
        $anggotaPerhatianData = $anggotaPerhatian->map(function ($anggota) {
            $usahaData = $anggota->usaha->map(function ($usaha) {
                $laporanTerbaru = $usaha->laporan->first();
                $evaluasiTerbaru = null;
                if ($laporanTerbaru) {
                    $evaluasiTerbaru = Validasi::where('laporan_id', $laporanTerbaru->id)
                        ->where('status_validasi_pengurus', 'disetujui')
                        ->whereNotNull('hasil_evaluasi')
                        ->first();
                    $evaluasiTerbaru?->setRelation('laporan', $laporanTerbaru);
                }
                return ['usaha' => $usaha, 'laporan' => $laporanTerbaru, 'evaluasi' => $evaluasiTerbaru];
            });
            return ['anggota' => $anggota, 'usaha_data' => $usahaData];
        });

        // ── Fitur 6A: Evaluasi Terbaru ───────────────────────────
        $evaluasiTerbaru = Validasi::where('status_validasi_pengurus', 'disetujui')
            ->whereNotNull('hasil_evaluasi')
            ->with(['laporan.usaha.anggota.akun', 'validator'])
            ->orderBy('tanggal_validasi_pengurus', 'desc')
            ->limit(5)
            ->get();

        // ── Fitur 6B: Validasi Syariah Terbaru ──────────────────
        $validasiSyariahTerbaru = ValidasiAkad::with(['usaha.anggota.akun', 'dps'])
            ->orderBy('divalidasi_pada', 'desc')
            ->limit(5)
            ->get();

        // ── Fitur 6C: Laporan Terbaru ────────────────────────────
        $laporanTerbaru = Laporan::with(['usaha.anggota.akun'])
            ->where('status_laporan', '!=', 'draft')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('ketua.dashboard', compact(
            'totalAnggota',
            'anggotaAktif',
            'anggotaPeringatan',
            'anggotaTidakAktif',
            'totalUsahaAktif',
            'totalLaporan',
            'laporanDievaluasi',
            'usahaMenungguDps',
            'grafikLaporan',
            'grafikStatusAnggota',
            'grafikJenisAkad',
            'grafikHasilEvaluasi',
            'grafikOmzet',
            'anggotaPerhatianData',
            'evaluasiTerbaru',
            'validasiSyariahTerbaru',
            'laporanTerbaru'
        ));
    }
}
