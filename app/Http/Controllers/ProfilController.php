<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfilRequest;
use App\Models\Akun;
use App\Models\AktivitasLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProfilController extends Controller
{
    /**
     * Menampilkan profil akun yang sedang login.
     *
     * Jika pengguna berperan sebagai anggota, data anggota juga
     * dikirimkan ke halaman sebagai informasi read-only.
     */
    public function index(): View
    {
        $akun = $this->getAuthenticatedAkun();

        $anggota = $akun->role === 'anggota'
            ? $akun->anggota
            : null;

        return view('profil.index', [
            'akun'    => $akun,
            'anggota' => $anggota,
        ]);
    }

    /**
     * Menampilkan form edit profil.
     */
    public function edit(): View
    {
        $akun = $this->getAuthenticatedAkun();

        return view('profil.edit', [
            'akun' => $akun,
        ]);
    }

    /**
     * Memperbarui nama, email, dan foto profil pengguna.
     */
    public function update(
        UpdateProfilRequest $request
    ): RedirectResponse {
        $akun = $this->getAuthenticatedAkun();

        $data = [
            'nama'  => (string) $request->input('nama'),
            'email' => (string) $request->input('email'),
        ];

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');

            if ($foto instanceof UploadedFile) {
                $data['foto'] = $this->uploadFoto(
                    $foto,
                    $akun->foto
                );
            }
        }

        $akun->update($data);

        AktivitasLog::create([
            'akun_id' => $akun->id,

            'aktivitas' => 'Perbarui Profil',

            'keterangan' => 'Memperbarui data profil pribadi '
                . '(nama/email'
                . ($request->hasFile('foto') ? '/foto' : '')
                . ').',

            'ip_address' => $request->ip(),

            'user_agent' => $request->userAgent(),
        ]);

        return redirect()
            ->route('profil.index')
            ->with(
                'success',
                'Profil berhasil diperbarui.'
            );
    }

    /**
     * Menampilkan form ganti password.
     */
    public function editPassword(): View
    {
        return view('profil.password');
    }

    /**
     * Memperbarui password akun.
     *
     * Pemeriksaan password lama dilakukan melalui
     * UpdatePasswordRequest.
     */
    public function updatePassword(
        UpdatePasswordRequest $request
    ): RedirectResponse {
        $akun = $this->getAuthenticatedAkun();

        $passwordBaru = (string) $request->input(
            'password_baru'
        );

        $akun->update([
            'password' => Hash::make($passwordBaru),
        ]);

        AktivitasLog::create([
            'akun_id' => $akun->id,

            'aktivitas' => 'Ganti Password',

            'keterangan' => 'Mengganti password akun '
                . 'melalui halaman profil.',

            'ip_address' => $request->ip(),

            'user_agent' => $request->userAgent(),
        ]);

        return redirect()
            ->route('profil.index')
            ->with(
                'success',
                'Password berhasil diperbarui.'
            );
    }

    /**
     * Menghapus foto profil pengguna dan mengembalikannya
     * ke avatar default.
     */
    public function destroyFoto(
        Request $request
    ): RedirectResponse {
        $akun = $this->getAuthenticatedAkun();

        if (empty($akun->foto)) {
            return redirect()
                ->route('profil.index')
                ->with(
                    'error',
                    'Anda belum memiliki foto profil untuk dihapus.'
                );
        }

        $this->hapusFileFoto($akun->foto);

        $akun->update([
            'foto' => null,
        ]);

        AktivitasLog::create([
            'akun_id' => $akun->id,

            'aktivitas' => 'Hapus Foto Profil',

            'keterangan' => 'Menghapus foto profil dan '
                . 'kembali ke avatar default.',

            'ip_address' => $request->ip(),

            'user_agent' => $request->userAgent(),
        ]);

        return redirect()
            ->route('profil.index')
            ->with(
                'success',
                'Foto profil berhasil dihapus. '
                    . 'Avatar default kini digunakan.'
            );
    }

    /**
     * Mengambil akun yang sedang terautentikasi.
     *
     * Method ini juga membantu IDE dan Intelephense memahami
     * bahwa objek yang digunakan merupakan model Akun.
     */
    private function getAuthenticatedAkun(): Akun
    {
        $akun = Auth::user();

        if (! $akun instanceof Akun) {
            abort(
                401,
                'Sesi pengguna tidak ditemukan. Silakan login kembali.'
            );
        }

        return $akun;
    }

    /**
     * Mengunggah foto baru ke public/uploads/foto dan
     * menghapus foto lama setelah upload berhasil.
     */
    private function uploadFoto(
        UploadedFile $file,
        ?string $fotoLama
    ): string {
        $folder = public_path('uploads/foto');

        if (! is_dir($folder)) {
            mkdir($folder, 0755, true);
        }

        $extension = strtolower(
            $file->getClientOriginalExtension()
        );

        $filename = 'akun_'
            . Str::random(10)
            . '_'
            . time()
            . '.'
            . $extension;

        $file->move($folder, $filename);

        $this->hapusFileFoto($fotoLama);

        return $filename;
    }

    /**
     * Menghapus file foto lama dari public/uploads/foto.
     */
    private function hapusFileFoto(
        ?string $filename
    ): void {
        if (empty($filename)) {
            return;
        }

        $path = public_path(
            'uploads/foto/' . basename($filename)
        );

        if (is_file($path)) {
            unlink($path);
        }
    }
}
