<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAnggotaRequest;
use App\Http\Requests\Admin\UpdateAnggotaRequest;
use App\Models\Akun;
use App\Models\Anggota;
use App\Models\AktivitasLog;
use App\Models\Laporan;
use App\Models\Validasi;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AnggotaController extends Controller
{
    /**
     * Daftar anggota dengan search, filter, sort, pagination.
     */
    public function index(Request $request): View
    {
        $query = Anggota::query();

        // ── Search: nama (via akun), nomor_anggota, NIK, telepon ─
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nomor_anggota', 'like', "%{$s}%")
                    ->orWhere('nik', 'like', "%{$s}%")
                    ->orWhere('telepon', 'like', "%{$s}%")
                    ->orWhereHas('akun', fn($qa) => $qa->where('nama', 'like', "%{$s}%"));
            });
        }

        // ── Filter Status ──────────────────────────────────────
        if ($request->filled('status')) {
            $query->where('status_anggota', $request->status);
        }

        // ── Filter Jenis Kelamin ───────────────────────────────
        if ($request->filled('jk')) {
            $query->where('jenis_kelamin', $request->jk);
        }

        // ── Sorting ────────────────────────────────────────────
        $allowedSorts = ['nama', 'nomor_anggota', 'tanggal_bergabung', 'status_anggota'];
        $sort = in_array($request->sort, $allowedSorts) ? $request->sort : 'created_at';
        $dir  = $request->dir === 'asc' ? 'asc' : 'desc';

        if ($sort === 'nama') {
            // Join ke tabel akun untuk sort berdasarkan nama
            $query->join('akun', 'anggota.akun_id', '=', 'akun.id')
                ->select('anggota.*')
                ->orderBy('akun.nama', $dir);
        } else {
            $query->orderBy($sort, $dir);
        }

        $anggotaList = $query->with('akun')->paginate(10)->withQueryString();

        return view('admin.anggota.index', [
            'anggotaList'  => $anggotaList,
            'search'       => $request->search,
            'filterStatus' => $request->status,
            'filterJk'     => $request->jk,
            'sort'         => $sort,
            'dir'          => $dir,
        ]);
    }

    /**
     * Form tambah anggota.
     */
    public function create(): View
    {
        // Hanya akun role anggota yang belum memiliki data anggota
        $akunAvailable = Akun::where('role', 'anggota')
            ->whereDoesntHave('anggota')
            ->orderBy('nama')
            ->get();

        $nomorBerikutnya = $this->generateNomorAnggota();

        return view('admin.anggota.create', compact('akunAvailable', 'nomorBerikutnya'));
    }

    /**
     * Simpan data anggota baru.
     */
    public function store(StoreAnggotaRequest $request): RedirectResponse
    {
        $data                 = $request->validated();
        $data['nomor_anggota'] = $this->generateNomorAnggota();

        $anggota = Anggota::create($data);
        $anggota->load('akun');

        AktivitasLog::create([
            'akun_id'    => Auth::id(),
            'aktivitas'  => 'Tambah Anggota',
            'keterangan' => 'Menambahkan data anggota: '
                . $anggota->akun->nama
                . ' (' . $anggota->nomor_anggota . ')',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.anggota.index')
            ->with('success', 'Data anggota "' . $anggota->akun->nama . '" berhasil ditambahkan dengan nomor ' . $anggota->nomor_anggota . '.');
    }

    /**
     * Detail anggota.
     */
    public function show(Anggota $anggota): View
    {
        $anggota->load(['akun', 'usaha']);

        $usahaIds = $anggota->usaha->pluck('id');

        $jumlahLaporan = Laporan::whereIn('usaha_id', $usahaIds)->count();

        $laporanIds = Laporan::whereIn('usaha_id', $usahaIds)->pluck('id');

        $evaluasiTerakhir = Validasi::whereIn('laporan_id', $laporanIds)
            ->with(['laporan.usaha', 'validator'])
            ->latest()
            ->first();

        return view('admin.anggota.show', compact(
            'anggota',
            'jumlahLaporan',
            'evaluasiTerakhir'
        ));
    }

    /**
     * Update data anggota.
     */
    public function edit(Anggota $anggota): View
    {
        $anggota->load('akun');

        $akunAvailable = Akun::where('role', 'anggota')
            ->where(function ($q) use ($anggota) {
                $q->whereDoesntHave('anggota')
                    ->orWhere('id', $anggota->akun_id);
            })
            ->orderBy('nama')
            ->get();

        return view('admin.anggota.edit', compact('anggota', 'akunAvailable'));
    }
    /**
     * Update data anggota.
     */
    public function update(UpdateAnggotaRequest $request, Anggota $anggota): RedirectResponse
    {
        $anggota->update($request->validated());
        $anggota->load('akun');

        AktivitasLog::create([
            'akun_id'    => Auth::id(),
            'aktivitas'  => 'Edit Anggota',
            'keterangan' => 'Memperbarui data anggota: ' . $anggota->akun->nama . ' (' . $anggota->nomor_anggota . ')',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.anggota.index')
            ->with('success', 'Data anggota "' . $anggota->akun->nama . '" berhasil diperbarui.');
    }

    /**
     * Hapus data anggota.
     *
     * CATATAN BEHAVIOR CASCADE:
     * - Menghapus anggota AKAN menghapus: seluruh data usaha, laporan, dan validasi terkait (CASCADE).
     * - Akun login (email/password) di tabel `akun` TIDAK ikut terhapus.
     * - Jika diperlukan, admin dapat menghapus akun secara terpisah melalui Manajemen Akun.
     */
    public function destroy(Request $request, Anggota $anggota): RedirectResponse
    {
        $anggota->load('akun');
        $namaAnggota  = $anggota->akun->nama;
        $nomorAnggota = $anggota->nomor_anggota;

        $anggota->delete();

        AktivitasLog::create([
            'akun_id'    => Auth::id(),
            'aktivitas'  => 'Hapus Anggota',
            'keterangan' => 'Menghapus data anggota: ' . $namaAnggota . ' (' . $nomorAnggota . ')',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.anggota.index')
            ->with('success', 'Data anggota "' . $namaAnggota . '" berhasil dihapus. Akun login tidak ikut terhapus.');
    }

    /**
     * Generate nomor anggota otomatis: PKM-0001, PKM-0002, dst.
     */
    private function generateNomorAnggota(): string
    {
        $last = Anggota::orderBy('id', 'desc')->first();

        if (! $last) {
            return 'PKM-0001';
        }

        $lastNumber = (int) substr($last->nomor_anggota, 4);

        return 'PKM-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }
}
