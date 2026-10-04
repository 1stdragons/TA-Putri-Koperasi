<?php

namespace App\Http\Controllers\Dps;

use App\Http\Controllers\Controller;
use App\Models\Usaha;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DataUsahaController extends Controller
{
    /**
     * Kolom yang diperbolehkan untuk sorting.
     */
    private array $allowedSorts = [
        'nama_usaha',
        'nama_anggota',
        'tanggal_mulai',
        'tanggal_aktivasi_monitoring',
        'created_at',
    ];

    /**
     * Menampilkan daftar seluruh usaha anggota untuk DPS.
     *
     * Halaman ini bersifat read-only. DPS hanya dapat melihat
     * data usaha, status akad, status monitoring, dan perkembangan
     * laporan tanpa mengubah data.
     */
    public function index(Request $request): View
    {
        $query = Usaha::query()
            ->join('anggota', 'usaha.anggota_id', '=', 'anggota.id')
            ->join('akun', 'anggota.akun_id', '=', 'akun.id')
            ->select(
                'usaha.*',
                'akun.nama as nama_anggota',
                'anggota.nomor_anggota'
            )
            ->with([
                'anggota.akun',
                'laporan',
                'validasiAkadTerakhir.dps',
            ])
            ->withCount('laporan');

        /*
         * Pencarian berdasarkan nama usaha, nama anggota,
         * atau nomor anggota.
         */
        if ($request->filled('search')) {
            $search = trim((string) $request->search);

            $query->where(function ($q) use ($search) {
                $q->where('usaha.nama_usaha', 'like', "%{$search}%")
                    ->orWhere('akun.nama', 'like', "%{$search}%")
                    ->orWhere('anggota.nomor_anggota', 'like', "%{$search}%");
            });
        }

        /*
         * Filter status operasional usaha.
         */
        if ($request->filled('status_usaha')) {
            $query->where(
                'usaha.status_usaha',
                $request->status_usaha
            );
        }

        /*
         * Filter status validasi akad syariah.
         */
        if ($request->filled('status_syariah')) {
            $query->where(
                'usaha.status_validasi_syariah',
                $request->status_syariah
            );
        }

        /*
         * Filter kondisi monitoring usaha.
         *
         * Monitoring Aktif:
         * - akad telah disetujui DPS;
         * - status usaha aktif atau vakum;
         * - tanggal aktivasi monitoring sudah tersedia.
         *
         * Belum Aktif:
         * - akad belum disetujui;
         * - status usaha masih belum aktif;
         * - atau tanggal aktivasi belum tersedia.
         *
         * Selesai:
         * - usaha telah berstatus tutup.
         */
        if ($request->filled('status_monitoring')) {
            if ($request->status_monitoring === 'aktif') {
                $query
                    ->where('usaha.status_validasi_syariah', 'disetujui')
                    ->whereIn('usaha.status_usaha', ['aktif', 'vakum'])
                    ->whereNotNull('usaha.tanggal_aktivasi_monitoring');
            }

            if ($request->status_monitoring === 'belum_aktif') {
                $query->where(function ($q) {
                    $q->where(
                        'usaha.status_validasi_syariah',
                        '!=',
                        'disetujui'
                    )
                        ->orWhere('usaha.status_usaha', 'belum_aktif')
                        ->orWhereNull(
                            'usaha.tanggal_aktivasi_monitoring'
                        );
                });
            }

            if ($request->status_monitoring === 'selesai') {
                $query->where('usaha.status_usaha', 'tutup');
            }
        }

        /*
         * Sorting menggunakan daftar kolom yang diizinkan
         * agar nama kolom dari request tidak dipakai secara bebas.
         */
        $sort = in_array(
            $request->sort,
            $this->allowedSorts,
            true
        )
            ? $request->sort
            : 'created_at';

        $direction = $request->dir === 'asc'
            ? 'asc'
            : 'desc';

        if ($sort === 'nama_anggota') {
            $query->orderBy('akun.nama', $direction);
        } else {
            $query->orderBy(
                'usaha.' . $sort,
                $direction
            );
        }

        /*
         * Pagination daftar usaha.
         */
        $usahaList = $query
            ->paginate(10)
            ->withQueryString();

        /*
         * Statistik seluruh usaha.
         *
         * Query statistik dipisahkan dari query tabel agar jumlahnya
         * tetap mencerminkan seluruh data, bukan hanya satu halaman.
         */
        $totalUsaha = Usaha::count();

        $monitoringAktif = Usaha::query()
            ->where(
                'status_validasi_syariah',
                'disetujui'
            )
            ->whereIn(
                'status_usaha',
                ['aktif', 'vakum']
            )
            ->whereNotNull(
                'tanggal_aktivasi_monitoring'
            )
            ->count();

        $belumAktif = Usaha::query()
            ->where(function ($query) {
                $query
                    ->where(
                        'status_validasi_syariah',
                        '!=',
                        'disetujui'
                    )
                    ->orWhere(
                        'status_usaha',
                        'belum_aktif'
                    )
                    ->orWhereNull(
                        'tanggal_aktivasi_monitoring'
                    );
            })
            ->count();

        $usahaTutup = Usaha::query()
            ->where('status_usaha', 'tutup')
            ->count();

        return view('dps.data-usaha.index', [
            'usahaList'        => $usahaList,
            'search'           => $request->search,
            'filterStatus'     => $request->status_usaha,
            'filterSyariah'    => $request->status_syariah,
            'filterMonitoring' => $request->status_monitoring,
            'sort'             => $sort,
            'dir'              => $direction,
            'totalUsaha'       => $totalUsaha,
            'monitoringAktif'  => $monitoringAktif,
            'belumAktif'       => $belumAktif,
            'usahaTutup'       => $usahaTutup,
        ]);
    }

    /**
     * Menampilkan detail usaha dan perkembangan monitoring.
     *
     * Seluruh informasi pada halaman detail hanya dapat dilihat.
     * DPS tidak memiliki aksi edit, hapus, atau validasi laporan.
     */
    public function show(Usaha $usaha): View
    {
        $usaha->load([
            'anggota.akun',
            'laporan.validasi',
            'validasiAkad.dps',
            'validasiAkadTerakhir.dps',
        ]);

        /*
         * Jumlah seluruh laporan monitoring milik usaha.
         */
        $jumlahLaporan = $usaha->laporan->count();

        /*
         * Akumulasi omzet seluruh laporan monitoring.
         */
        $totalOmzet = $usaha->laporan->sum('omzet');

        /*
         * Akumulasi biaya operasional seluruh laporan.
         */
        $totalBiaya = $usaha->laporan->sum(
            'biaya_operasional'
        );

        /*
         * Mengambil laporan dengan periode terbaru.
         */
        $laporanTerakhir = $usaha->laporan
            ->sortByDesc('periode_ke')
            ->first();

        /*
         * Mengambil hasil evaluasi terakhir apabila laporan terbaru
         * sudah mempunyai relasi validasi/evaluasi.
         */
        $evaluasiTerakhir = null;

        if (
            $laporanTerakhir
            && $laporanTerakhir->validasi
        ) {
            $evaluasiTerakhir = $laporanTerakhir->validasi;
        }

        return view('dps.data-usaha.show', [
            'usaha'             => $usaha,
            'jumlahLaporan'     => $jumlahLaporan,
            'totalOmzet'        => $totalOmzet,
            'totalBiaya'        => $totalBiaya,
            'laporanTerakhir'   => $laporanTerakhir,
            'evaluasiTerakhir'  => $evaluasiTerakhir,
        ]);
    }
}
