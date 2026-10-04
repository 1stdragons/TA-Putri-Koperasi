<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Validasi;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class MonitoringController extends Controller
{
    private array $allowedSorts = [
        'nama_anggota',
        'nama_usaha',
        'omzet_sekarang',
        'persentase_kenaikan_omzet',
        'rasio_biaya',
        'tanggal_validasi_pengurus',
    ];

    /**
     * Index monitoring — hanya evaluasi yang sudah lengkap:
     * laporan divalidasi + pengurus disetujui + DPS disetujui + hasil_evaluasi ada.
     */
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
                'laporan.status_laporan',
                'usaha.nama_usaha',
                'usaha.jenis_usaha',
                'usaha.jenis_akad',
                'usaha.status_validasi_syariah',
                'anggota.nomor_anggota',
                'anggota.status_anggota',
                'akun.nama as nama_anggota'
            )
            // Syarat lengkap: validasi pengurus disetujui + ada hasil evaluasi
            ->where('validasi.status_validasi_pengurus', 'disetujui')
            ->whereNotNull('validasi.hasil_evaluasi')
            // Usaha harus sudah disetujui DPS
            ->where('usaha.status_validasi_syariah', 'disetujui');

        // ── Search ──────────────────────────────────────────────
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('akun.nama', 'like', "%{$s}%")
                    ->orWhere('anggota.nomor_anggota', 'like', "%{$s}%")
                    ->orWhere('usaha.nama_usaha', 'like', "%{$s}%");
            });
        }

        // ── Filter: Hasil Evaluasi ───────────────────────────────
        if ($request->filled('hasil')) {
            $query->where('validasi.hasil_evaluasi', $request->hasil);
        }

        // ── Filter: Status Anggota ───────────────────────────────
        if ($request->filled('status_anggota')) {
            $query->where('anggota.status_anggota', $request->status_anggota);
        }

        // ── Filter: Jenis Usaha ──────────────────────────────────
        if ($request->filled('jenis_usaha')) {
            $query->where('usaha.jenis_usaha', $request->jenis_usaha);
        }

        // ── Filter: Jenis Akad ───────────────────────────────────
        if ($request->filled('jenis_akad')) {
            $query->where('usaha.jenis_akad', $request->jenis_akad);
        }

        // ── Filter: Periode ──────────────────────────────────────
        if ($request->filled('periode')) {
            [$tahun, $bulan] = explode('-', $request->periode);
            $query->whereYear('laporan.tanggal_mulai', $tahun)
                ->whereMonth('laporan.tanggal_mulai', $bulan);
        }

        // ── Sorting ──────────────────────────────────────────────
        $sortRaw = $request->sort ?? 'tanggal_validasi_pengurus';
        $sort    = in_array($sortRaw, $this->allowedSorts) ? $sortRaw : 'tanggal_validasi_pengurus';
        $dir     = $request->dir === 'asc' ? 'asc' : 'desc';

        $sortMap = [
            'nama_anggota'               => 'akun.nama',
            'nama_usaha'                 => 'usaha.nama_usaha',
            'omzet_sekarang'             => 'laporan.omzet',
            'persentase_kenaikan_omzet'  => 'validasi.persentase_kenaikan_omzet',
            'rasio_biaya'                => 'validasi.rasio_biaya',
            'tanggal_validasi_pengurus'  => 'validasi.tanggal_validasi_pengurus',
        ];

        $query->orderBy($sortMap[$sort] ?? 'validasi.tanggal_validasi_pengurus', $dir);

        $monitoringList = $query
            ->with(['laporan.usaha', 'validator', 'dps'])
            ->paginate(10)
            ->withQueryString();

        // Dropdown Periode
        $periodeList = DB::table('laporan')
            ->selectRaw("DATE_FORMAT(tanggal_mulai,'%Y-%m') as periode, DATE_FORMAT(tanggal_mulai,'%M %Y') as label")
            ->groupBy('periode', 'label')
            ->orderBy('periode', 'desc')
            ->pluck('label', 'periode');

        // Statistik ringkas halaman monitoring
        $statsMonitoring = Validasi::where('status_validasi_pengurus', 'disetujui')
            ->whereNotNull('hasil_evaluasi')
            ->whereHas('laporan.usaha', fn($q) => $q->where('status_validasi_syariah', 'disetujui'))
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN hasil_evaluasi = 'aktif' THEN 1 ELSE 0 END) as aktif,
                SUM(CASE WHEN hasil_evaluasi = 'peringatan' THEN 1 ELSE 0 END) as peringatan,
                SUM(CASE WHEN hasil_evaluasi = 'tidak_aktif' THEN 1 ELSE 0 END) as tidak_aktif
            ")
            ->first();

        return view('ketua.monitoring.index', [
            'monitoringList'  => $monitoringList,
            'periodeList'     => $periodeList,
            'statsMonitoring' => $statsMonitoring,
            'search'          => $request->search,
            'filterHasil'     => $request->hasil,
            'filterStatus'    => $request->status_anggota,
            'filterJenis'     => $request->jenis_usaha,
            'filterAkad'      => $request->jenis_akad,
            'filterPeriode'   => $request->periode,
            'sort'            => $sort,
            'dir'             => $dir,
        ]);
    }

    /**
     * Detail monitoring — tampilan lengkap A-F.
     */
    public function show(Validasi $validasi): View|RedirectResponse
    {
        // Pastikan hanya evaluasi yang sudah lengkap yang bisa diakses
        if ($validasi->status_validasi_pengurus !== 'disetujui' || ! $validasi->hasil_evaluasi) {
            return redirect()->route('ketua.monitoring.index')
                ->with('error', 'Data monitoring belum lengkap.');
        }

        $validasi->load([
            'laporan.usaha.anggota.akun',
            'laporan.usaha.validasiAkadTerakhir.dps',
            'validator',
            'dps',
        ]);

        return view('ketua.monitoring.show', compact('validasi'));
    }
}
