<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUsahaRequest;
use App\Http\Requests\Admin\UpdateUsahaRequest;
use App\Models\Akun;
use App\Models\Anggota;
use App\Models\AktivitasLog;
use App\Models\Laporan;
use App\Models\Usaha;
use App\Models\ValidasiAkad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UsahaController extends Controller
{
    // ── Kolom yang diizinkan untuk sorting ───────────────────────
    private array $allowedSorts = [
        'nama_usaha',
        'nama_anggota',
        'modal_awal',
        'tanggal_mulai',
    ];

    /**
     * Daftar usaha dengan search, filter, sort, pagination.
     */
    public function index(Request $request): View
    {
        $query = Usaha::query()
            ->join('anggota', 'usaha.anggota_id', '=', 'anggota.id')
            ->join('akun', 'anggota.akun_id', '=', 'akun.id')
            ->select('usaha.*', 'akun.nama as nama_anggota', 'anggota.nomor_anggota');

        // ── Search ────────────────────────────────────────────────
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('usaha.nama_usaha', 'like', "%{$s}%")
                    ->orWhere('akun.nama', 'like', "%{$s}%")
                    ->orWhere('anggota.nomor_anggota', 'like', "%{$s}%");
            });
        }

        // ── Filter Jenis Usaha ────────────────────────────────────
        if ($request->filled('jenis_usaha')) {
            $query->where('usaha.jenis_usaha', $request->jenis_usaha);
        }

        // ── Filter Jenis Akad ─────────────────────────────────────
        if ($request->filled('jenis_akad')) {
            $query->where('usaha.jenis_akad', $request->jenis_akad);
        }

        // ── Filter Status Usaha ───────────────────────────────────
        if ($request->filled('status_usaha')) {
            $query->where('usaha.status_usaha', $request->status_usaha);
        }

        // ── Filter Status Validasi Syariah ────────────────────────
        if ($request->filled('status_syariah')) {
            $query->where('usaha.status_validasi_syariah', $request->status_syariah);
        }

        // ── Sorting ───────────────────────────────────────────────
        $sort = in_array($request->sort, $this->allowedSorts)
            ? $request->sort
            : 'usaha.created_at';

        $dir = $request->dir === 'asc' ? 'asc' : 'desc';

        if ($sort === 'nama_anggota') {
            $query->orderBy('akun.nama', $dir);
        } else {
            $column = in_array($sort, ['modal_awal', 'tanggal_mulai', 'nama_usaha'])
                ? 'usaha.' . $sort
                : $sort;
            $query->orderBy($column, $dir);
        }

        // ── Eager load & Paginate ─────────────────────────────────
        $usahaList = $query
            ->with(['anggota.akun', 'laporan'])
            ->withCount('laporan')
            ->paginate(10)
            ->withQueryString();

        return view('admin.usaha.index', [
            'usahaList'     => $usahaList,
            'search'        => $request->search,
            'filterJenis'   => $request->jenis_usaha,
            'filterAkad'    => $request->jenis_akad,
            'filterStatus'  => $request->status_usaha,
            'filterSyariah' => $request->status_syariah,
            'sort'          => $sort,
            'dir'           => $dir,
        ]);
    }

    /**
     * Form tambah usaha.
     */
    public function create(): View
    {
        // Hanya anggota aktif yang bisa mendaftarkan usaha
        $anggotaList = Anggota::where('status_anggota', 'aktif')
            ->with('akun')
            ->orderBy('nomor_anggota')
            ->get();

        return view('admin.usaha.create', compact('anggotaList'));
    }

    /**
     * Simpan usaha baru.
     */
    public function store(StoreUsahaRequest $request): RedirectResponse
    {
        $data = $request->validated();

        /*
         * Usaha baru belum boleh langsung aktif.
         * Status aktif dan tanggal awal monitoring ditetapkan setelah
         * akad disetujui DPS.
         */
        $data['status_usaha'] = 'belum_aktif';
        $data['tanggal_aktivasi_monitoring'] = null;

        // ── Upload dokumen akad ───────────────────────────────────
        if ($request->hasFile('file_akad')) {
            $data['file_akad']              = $this->uploadFileAkad($request->file('file_akad'));
            $data['status_validasi_syariah'] = 'menunggu';
        } else {
            $data['status_validasi_syariah'] = 'belum_diupload';
        }

        $usaha = Usaha::create($data);
        $usaha->load('anggota.akun');

        // ── Jika ada file akad, buat entri riwayat validasi ──────
        if ($usaha->file_akad) {
            ValidasiAkad::buatIterasiBaru(
                $usaha->id,
                $usaha->file_akad,
                $usaha->jenis_akad
            );
        }

        // ── Log aktivitas ─────────────────────────────────────────
        AktivitasLog::create([
            'akun_id'    => Auth::id(),
            'aktivitas'  => 'Tambah Usaha',
            'keterangan' => 'Menambahkan data usaha: '
                . $usaha->nama_usaha
                . ' — anggota: ' . $usaha->anggota->akun->nama
                . ' (' . $usaha->anggota->nomor_anggota . ')',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.usaha.index')
            ->with('success', 'Data usaha "' . $usaha->nama_usaha . '" berhasil ditambahkan.');
    }

    /**
     * Detail usaha.
     */
    public function show(Usaha $usaha): View
    {
        $usaha->load([
            'anggota.akun',
            'laporan.validasi',
            'validasiAkad.dps',
            'validasiAkadTerakhir.dps',
        ]);

        $jumlahLaporan = $usaha->laporan->count();

        $totalOmzet = $usaha->laporan->sum('omzet');

        $totalBiaya = $usaha->laporan->sum('biaya_operasional');

        $laporanTerakhir = $usaha->laporan->sortByDesc('periode_ke')->first();

        $evaluasiTerakhir = null;
        if ($laporanTerakhir && $laporanTerakhir->validasi) {
            $evaluasiTerakhir = $laporanTerakhir->validasi;
        }

        return view('admin.usaha.show', compact(
            'usaha',
            'jumlahLaporan',
            'totalOmzet',
            'totalBiaya',
            'laporanTerakhir',
            'evaluasiTerakhir'
        ));
    }

    /**
     * Form edit usaha.
     */
    public function edit(Usaha $usaha): View
    {
        $usaha->load('anggota.akun');

        // Anggota aktif + anggota milik usaha ini (meskipun statusnya bukan aktif)
        $anggotaList = Anggota::where(function ($q) use ($usaha) {
            $q->where('status_anggota', 'aktif')
                ->orWhere('id', $usaha->anggota_id);
        })
            ->with('akun')
            ->orderBy('nomor_anggota')
            ->get();

        return view('admin.usaha.edit', compact('usaha', 'anggotaList'));
    }

    /**
     * Update data usaha.
     */
    public function update(UpdateUsahaRequest $request, Usaha $usaha): RedirectResponse
    {
        $data = $request->validated();

        $statusLama = $usaha->status_usaha;
        $statusBaru = $data['status_usaha'] ?? $statusLama;
        $akadDiganti = $request->hasFile('file_akad');

        /*
         * Aturan status operasional:
         * - Belum disetujui DPS: tetap Belum Aktif.
         * - Aktif dapat menjadi Vakum atau Tutup.
         * - Vakum dapat menjadi Aktif atau Tutup.
         * - Tutup bersifat final dan tidak dapat diaktifkan kembali.
         * - Aktif/Vakum tidak mengubah tanggal aktivasi monitoring pertama.
         */
        if (! $akadDiganti) {
            if ($usaha->status_validasi_syariah !== 'disetujui') {
                $data['status_usaha'] = 'belum_aktif';
                $data['tanggal_aktivasi_monitoring'] = null;
                $statusBaru = 'belum_aktif';
            }

            if (
                $statusLama === 'tutup'
                && in_array($statusBaru, ['aktif', 'vakum'], true)
            ) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Usaha yang sudah berstatus Tutup tidak dapat diaktifkan kembali. Silakan daftarkan sebagai usaha baru.'
                    );
            }

            if (
                in_array($statusLama, ['aktif', 'vakum'], true)
                && in_array($statusBaru, ['aktif', 'vakum', 'tutup'], true)
            ) {
                $data['tanggal_aktivasi_monitoring'] = $usaha->tanggal_aktivasi_monitoring;
            }
        }

        DB::beginTransaction();

        try {
            if ($akadDiganti) {
                $fileLama = $usaha->file_akad;

                $data['file_akad'] = $this->uploadFileAkad($request->file('file_akad'));
                $data['status_validasi_syariah'] = 'menunggu';
                $data['status_usaha'] = 'belum_aktif';
                $data['tanggal_aktivasi_monitoring'] = null;
                $statusBaru = 'belum_aktif';

                ValidasiAkad::buatIterasiBaru(
                    $usaha->id,
                    $data['file_akad'],
                    $data['jenis_akad']
                );

                $this->hapusFileAkad($fileLama);
            }

            $usaha->update($data);
            $usaha->load('anggota.akun');

            $perubahanStatus = $statusLama !== $usaha->status_usaha
                ? ' | Status: '
                . ucfirst(str_replace('_', ' ', $statusLama))
                . ' → '
                . ucfirst(str_replace('_', ' ', $usaha->status_usaha))
                : '';

            $perubahanAkad = $akadDiganti
                ? ' | Dokumen akad diperbarui; status kembali Belum Aktif dan menunggu validasi DPS.'
                : '';

            AktivitasLog::create([
                'akun_id'    => Auth::id(),
                'aktivitas'  => $statusLama !== $usaha->status_usaha
                    ? 'Ubah Status Usaha'
                    : 'Edit Usaha',
                'keterangan' => 'Memperbarui data usaha: '
                    . $usaha->nama_usaha
                    . ' — anggota: '
                    . $usaha->anggota->akun->nama
                    . $perubahanStatus
                    . $perubahanAkad,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('admin.usaha.index')
                ->with('success', 'Data usaha "' . $usaha->nama_usaha . '" berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();

            if ($akadDiganti && isset($data['file_akad'])) {
                $this->hapusFileAkad($data['file_akad']);
            }

            report($e);

            return back()
                ->withInput()
                ->with('error', 'Data usaha gagal diperbarui. Silakan coba kembali.');
        }
    }

    /**
     * Hapus usaha.
     *
     * CASCADE DELETE:
     * - Laporan milik usaha ini → ikut terhapus (cascade FK)
     * - Validasi laporan (tabel validasi) → ikut terhapus (cascade FK)
     * - Riwayat validasi akad (tabel validasi_akad) → ikut terhapus (cascade FK)
     * - Data anggota → TIDAK terhapus
     * - Akun login anggota → TIDAK terhapus
     */
    public function destroy(Request $request, Usaha $usaha): RedirectResponse
    {
        $usaha->load('anggota.akun');
        $namaUsaha   = $usaha->nama_usaha;
        $namaAnggota = $usaha->anggota->akun->nama;

        // Hapus file akad dari disk jika ada
        $this->hapusFileAkad($usaha->file_akad);

        $usaha->delete();

        AktivitasLog::create([
            'akun_id'    => Auth::id(),
            'aktivitas'  => 'Hapus Usaha',
            'keterangan' => 'Menghapus data usaha: ' . $namaUsaha
                . ' — anggota: ' . $namaAnggota,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.usaha.index')
            ->with('success', 'Data usaha "' . $namaUsaha . '" berhasil dihapus. Seluruh laporan terkait juga ikut terhapus.');
    }

    // ── Helper: Upload File Akad ─────────────────────────────────

    private function uploadFileAkad($file): string
    {
        $folder = public_path('uploads/akad');

        if (! file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        $filename = 'akad_'
            . Str::random(12)
            . '_'
            . time()
            . '.'
            . $file->getClientOriginalExtension();

        $file->move($folder, $filename);

        return $filename;
    }

    private function hapusFileAkad(?string $filename): void
    {
        if (! $filename) return;

        $path = public_path('uploads/akad/' . $filename);

        if (file_exists($path)) {
            @unlink($path);
        }
    }
}
