<?php

namespace App\Http\Controllers\Dps;

use App\Http\Controllers\Controller;
use App\Models\Usaha;
use App\Models\ValidasiAkad;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama DPS.
     */
    public function index(): View
    {
        /*
        |--------------------------------------------------------------------------
        | Statistik Validasi Syariah
        |--------------------------------------------------------------------------
        |
        | Statistik dihitung dari status validasi syariah pada data usaha.
        | Monitoring usaha baru dapat berjalan setelah akad disetujui DPS.
        |
        */

        $totalMenunggu = Usaha::query()
            ->where('status_validasi_syariah', 'menunggu')
            ->count();

        $totalDisetujui = Usaha::query()
            ->where('status_validasi_syariah', 'disetujui')
            ->count();

        $totalPerluRevisi = Usaha::query()
            ->where('status_validasi_syariah', 'perlu_revisi')
            ->count();

        /*
         * Total diproses berarti sudah memperoleh keputusan DPS.
         */
        $totalDiproses = $totalDisetujui + $totalPerluRevisi;

        /*
         * Total data yang masuk ke ringkasan dashboard.
         */
        $totalRingkasan = (
            $totalMenunggu
            + $totalDisetujui
            + $totalPerluRevisi
        );

        /*
        |--------------------------------------------------------------------------
        | Persentase Ringkasan Validasi
        |--------------------------------------------------------------------------
        */

        $persentaseMenunggu = $totalRingkasan > 0
            ? round(($totalMenunggu / $totalRingkasan) * 100, 1)
            : 0;

        $persentaseDisetujui = $totalRingkasan > 0
            ? round(($totalDisetujui / $totalRingkasan) * 100, 1)
            : 0;

        $persentasePerluRevisi = $totalRingkasan > 0
            ? round(($totalPerluRevisi / $totalRingkasan) * 100, 1)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Daftar Usaha Menunggu Validasi
        |--------------------------------------------------------------------------
        */

        $usahaMenunggu = Usaha::query()
            ->where('status_validasi_syariah', 'menunggu')
            ->with([
                'anggota.akun',
                'validasiAkadTerakhir',
            ])
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Riwayat Validasi Terbaru oleh DPS yang Login
        |--------------------------------------------------------------------------
        */

        $riwayatTerbaru = ValidasiAkad::query()
            ->where('dps_id', Auth::id())
            ->with([
                'usaha.anggota.akun',
            ])
            ->orderByDesc('divalidasi_pada')
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Distribusi Jenis Akad
        |--------------------------------------------------------------------------
        |
        | Hanya usaha yang sudah memiliki jenis akad yang dihitung.
        |
        */

        $distribusiAkad = Usaha::query()
            ->whereNotNull('jenis_akad')
            ->where('jenis_akad', '!=', '')
            ->selectRaw('jenis_akad, COUNT(*) as jumlah')
            ->groupBy('jenis_akad')
            ->pluck('jumlah', 'jenis_akad');

        $jumlahMudharabah = (int) (
            $distribusiAkad['mudharabah'] ?? 0
        );

        $jumlahMusyarakah = (int) (
            $distribusiAkad['musyarakah'] ?? 0
        );

        $jumlahMurabahah = (int) (
            $distribusiAkad['murabahah'] ?? 0
        );

        $jumlahIjarah = (int) (
            $distribusiAkad['ijarah'] ?? 0
        );

        $totalAkad = (
            $jumlahMudharabah
            + $jumlahMusyarakah
            + $jumlahMurabahah
            + $jumlahIjarah
        );

        $persentaseMudharabah = $totalAkad > 0
            ? round(($jumlahMudharabah / $totalAkad) * 100, 1)
            : 0;

        $persentaseMusyarakah = $totalAkad > 0
            ? round(($jumlahMusyarakah / $totalAkad) * 100, 1)
            : 0;

        $persentaseMurabahah = $totalAkad > 0
            ? round(($jumlahMurabahah / $totalAkad) * 100, 1)
            : 0;

        $persentaseIjarah = $totalAkad > 0
            ? round(($jumlahIjarah / $totalAkad) * 100, 1)
            : 0;

        return view('dps.dashboard', [
            'totalMenunggu'           => $totalMenunggu,
            'totalDisetujui'          => $totalDisetujui,
            'totalPerluRevisi'        => $totalPerluRevisi,
            'totalDiproses'           => $totalDiproses,
            'totalRingkasan'          => $totalRingkasan,

            'persentaseMenunggu'      => $persentaseMenunggu,
            'persentaseDisetujui'     => $persentaseDisetujui,
            'persentasePerluRevisi'   => $persentasePerluRevisi,

            'usahaMenunggu'           => $usahaMenunggu,
            'riwayatTerbaru'          => $riwayatTerbaru,

            'jumlahMudharabah'        => $jumlahMudharabah,
            'jumlahMusyarakah'        => $jumlahMusyarakah,
            'jumlahMurabahah'         => $jumlahMurabahah,
            'jumlahIjarah'            => $jumlahIjarah,
            'totalAkad'               => $totalAkad,

            'persentaseMudharabah'    => $persentaseMudharabah,
            'persentaseMusyarakah'    => $persentaseMusyarakah,
            'persentaseMurabahah'     => $persentaseMurabahah,
            'persentaseIjarah'        => $persentaseIjarah,
        ]);
    }
}
