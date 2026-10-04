<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAkunRequest;
use App\Http\Requests\Admin\UpdateAkunRequest;
use App\Models\Akun;
use App\Models\AktivitasLog;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AkunController extends Controller
{
    /**
     * Tampilkan daftar akun dengan search, filter, sort, pagination.
     */
    public function index(Request $request)
    {
        $query = Akun::query();

        // ── Search ────────────────────────────────────────────
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // ── Filter Role ──────────────────────────────────────
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // ── Filter Status ────────────────────────────────────
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // ── Sorting ──────────────────────────────────────────
        $sortableColumns = ['nama', 'email', 'role', 'status', 'last_login', 'created_at'];
        $sort = in_array($request->sort, $sortableColumns) ? $request->sort : 'created_at';
        $dir  = $request->dir === 'asc' ? 'asc' : 'desc';

        $query->orderBy($sort, $dir);

        // ── Pagination (tetap bawa query string) ─────────────
        $akunList = $query->paginate(10)->withQueryString();

        return view('admin.akun.index', [
            'akunList'     => $akunList,
            'search'       => $request->search,
            'filterRole'   => $request->role,
            'filterStatus' => $request->status,
            'sort'         => $sort,
            'dir'          => $dir,
        ]);
    }

    /**
     * Tampilkan form tambah akun.
     */
    public function create()
    {
        return view('admin.akun.create');
    }

    /**
     * Simpan akun baru.
     */
    public function store(StoreAkunRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        // ── Upload foto jika ada ──────────────────────────────
        if ($request->hasFile('foto')) {
            $data['foto'] = $this->uploadFoto($request->file('foto'));
        }

        $akun = Akun::create($data);

        // ── Catat log aktivitas ───────────────────────────────
        AktivitasLog::create([
            'akun_id'    => Auth::id(),
            'aktivitas'  => 'Tambah Akun',
            'keterangan' => 'Menambahkan akun baru: ' . $akun->nama . ' (' . $akun->role_label . ')',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.akun.index')
            ->with('success', 'Akun "' . $akun->nama . '" berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail akun.
     */
    public function show(Akun $akun)
    {
        $akun->loadCount('aktivitasLog');

        return view('admin.akun.show', compact('akun'));
    }

    /**
     * Tampilkan form edit akun.
     */
    public function edit(Akun $akun)
    {
        return view('admin.akun.edit', compact('akun'));
    }

    /**
     * Update data akun.
     */
    public function update(UpdateAkunRequest $request, Akun $akun)
    {
        $data = $request->validated();

        // ── Password opsional ─────────────────────────────────
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // ── Upload foto baru jika ada ─────────────────────────
        if ($request->hasFile('foto')) {
            $this->hapusFotoLama($akun->foto);
            $data['foto'] = $this->uploadFoto($request->file('foto'));
        }

        $akun->update($data);

        // ── Catat log aktivitas ───────────────────────────────
        AktivitasLog::create([
            'akun_id'    => Auth::id(),
            'aktivitas'  => 'Edit Akun',
            'keterangan' => 'Memperbarui data akun: ' . $akun->nama,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.akun.index')
            ->with('success', 'Akun "' . $akun->nama . '" berhasil diperbarui.');
    }

    /**
     * Hapus akun.
     */
    public function destroy(Request $request, Akun $akun)
    {
        // ── Cegah admin menghapus akunnya sendiri ─────────────
        if ($akun->id === Auth::id()) {
            return redirect()->route('admin.akun.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $namaAkun = $akun->nama;

        $this->hapusFotoLama($akun->foto);
        $akun->delete();

        // ── Catat log aktivitas ───────────────────────────────
        AktivitasLog::create([
            'akun_id'    => Auth::id(),
            'aktivitas'  => 'Hapus Akun',
            'keterangan' => 'Menghapus akun: ' . $namaAkun,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.akun.index')
            ->with('success', 'Akun "' . $namaAkun . '" berhasil dihapus.');
    }

    // ── Helper: Upload Foto ───────────────────────────────────

    private function uploadFoto(UploadedFile $file): string
    {
        $folder = public_path('uploads/foto');

        if (! file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        $filename = 'akun_' . Str::random(12) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move($folder, $filename);

        return $filename;
    }

    private function hapusFotoLama(?string $foto): void
    {
        if ($foto) {
            $path = public_path('uploads/foto/' . $foto);
            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }
}