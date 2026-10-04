<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\AktivitasLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route($this->getDashboardRoute(Auth::user()->role));
        }

        return view('auth.login');
    }

    /**
     * Proses login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email', 'max:100'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
        ]);

        // Cari akun berdasarkan email
        $akun = Akun::where('email', $request->email)->first();

        // Validasi akun
        if (! $akun || ! Hash::check($request->password, $akun->password)) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password yang Anda masukkan salah.',
            ]);
        }

        // Cek status akun
        if ($akun->status === 'nonaktif') {
            throw ValidationException::withMessages([
                'email' => 'Akun Anda telah dinonaktifkan. Hubungi administrator.',
            ]);
        }

        // Login
        Auth::login($akun, $request->boolean('remember'));

        // Update last login
        $akun->update(['last_login' => now()]);

        // Catat log aktivitas
        AktivitasLog::create([
            'akun_id'    => $akun->id,
            'aktivitas'  => 'Login',
            'keterangan' => 'Login berhasil sebagai ' . $akun->role_label,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $request->session()->regenerate();

        return redirect()->intended(route($this->getDashboardRoute($akun->role)))
            ->with('success', 'Selamat datang, ' . $akun->nama . '!');
    }

    /**
     * Proses logout.
     */
    public function logout(Request $request)
    {
        $akun = Auth::user();

        // Catat log aktivitas
        if ($akun) {
            AktivitasLog::create([
                'akun_id'    => $akun->id,
                'aktivitas'  => 'Logout',
                'keterangan' => 'Logout dari sistem',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil logout.');
    }

    /**
     * Tentukan route dashboard sesuai role.
     */
    private function getDashboardRoute(string $role): string
    {
        return match ($role) {
            'admin'    => 'admin.dashboard',
            'anggota'  => 'anggota.dashboard',
            'pengurus' => 'pengurus.dashboard',
            'dps'      => 'dps.dashboard',
            'ketua'    => 'ketua.dashboard',
            default    => 'login',
        };
    }
}