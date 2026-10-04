@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="login-wrapper login-fade-in">

    {{-- ════════════════════════════════════════════════════════
         PANEL KIRI — Branding
    ════════════════════════════════════════════════════════ --}}
    <div class="login-left d-none d-lg-flex">

        {{-- Ornamen pattern islami --}}
        <div class="islamic-pattern"></div>

        {{-- Sparkle dekoratif --}}
        <i class="bi bi-stars sparkle s1"></i>
        <i class="bi bi-stars sparkle s2"></i>
        <i class="bi bi-stars sparkle s3"></i>

        {{-- Brand --}}
        <div class="login-brand">
            <div class="brand-icon">
                <i class="bi bi-bank2"></i>
            </div>
            <div class="brand-text">
                <h6>KOPERASI</h6>
                <small>Poltas Kreatif Mandiri</small>
            </div>
        </div>

        {{-- Hero --}}
        <div>
            <div class="login-hero">
                <h1>
                    Sistem Informasi<br>
                    Monitoring<br>
                    <span class="accent-text">Anggota Koperasi Syariah</span>
                </h1>
                <div class="hero-divider"></div>
                <p>
                    Sistem terintegrasi untuk memonitoring usaha anggota koperasi
                    secara transparan, akuntabel, dan sesuai prinsip syariah.
                </p>
            </div>

            {{-- Kartu Prinsip Syariah --}}
            <div class="principle-card">
                <div class="principle-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <div class="principle-title">Prinsip Syariah</div>
                    <div class="principle-list">
                        <i class="bi bi-check-lg text-warning"></i> Amanah &nbsp;
                        <i class="bi bi-check-lg text-warning"></i> Transparan<br>
                        <i class="bi bi-check-lg text-warning"></i> Adil &nbsp;
                        <i class="bi bi-check-lg text-warning"></i> Berkah
                    </div>
                </div>
            </div>
        </div>

        {{-- Quote --}}
        <div class="login-quote">
            <span class="quote-mark">&ldquo;</span>
            <p>Bersama membangun ekonomi umat yang kuat dan berkah.</p>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════
         PANEL KANAN — Form Login
    ════════════════════════════════════════════════════════ --}}
    <div class="login-right">
        <div style="width:100%; max-width:460px;">

            {{-- Brand versi mobile (tampil saat panel kiri disembunyikan) --}}
            <div class="mobile-brand">
                <div class="brand-icon-mobile">
                    <i class="bi bi-bank2"></i>
                </div>
                <h6>Koperasi Poltas Kreatif Mandiri</h6>
                <small>Sistem Monitoring Anggota Syariah</small>
            </div>

            {{-- Card Login --}}
            <div class="login-card">

                {{-- Icon lingkaran --}}
                <div class="login-icon-circle">
                    <i class="bi bi-bank2"></i>
                </div>

                {{-- Heading --}}
                <div class="login-heading">
                    <h4>Selamat Datang Kembali</h4>
                    <p>Masuk untuk melanjutkan ke sistem</p>
                </div>

                {{-- Pesan Sukses --}}
                @if (session('success'))
                    <div class="alert alert-success d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                {{-- Pesan Error --}}
                @if (session('error'))
                    <div class="alert alert-danger d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                {{-- Error Validasi --}}
                @if ($errors->any())
                    <div class="alert alert-danger mb-3">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Form Login --}}
                <form action="{{ route('login.post') }}" method="POST" id="formLogin" novalidate>
                    @csrf

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">Alamat Email</label>
                        <div class="input-modern @error('email') is-invalid @enderror">
                            <span class="input-icon">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                value="{{ old('email') }}"
                                placeholder="Masukkan email Anda"
                                autocomplete="email"
                                autofocus
                                required
                            >
                        </div>
                        @error('email')
                            <span class="invalid-feedback-custom">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-2">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-modern @error('password') is-invalid @enderror">
                            <span class="input-icon">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input
                                type="password"
                                name="password"
                                id="password"
                                placeholder="Masukkan password Anda"
                                autocomplete="current-password"
                                required
                            >
                            <button type="button" class="toggle-password-btn" id="togglePassword" title="Tampilkan/sembunyikan password">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="invalid-feedback-custom">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Remember & Forgot --}}
                    <div class="login-options">
                        <div class="form-check-modern">
                            <input
                                type="checkbox"
                                name="remember"
                                id="remember"
                                {{ old('remember') ? 'checked' : '' }}
                            >
                            <label for="remember">Ingat saya</label>
                        </div>
                        <a href="#" class="link-forgot">Lupa password?</a>
                    </div>

                    {{-- Tombol Login --}}
                    <button type="submit" class="btn-login-modern" id="btnLogin">
                        <span id="btnText" class="d-flex align-items-center gap-2">
                            <i class="bi bi-box-arrow-in-right"></i> Masuk ke Sistem
                        </span>
                        <span id="btnLoading" class="d-none align-items-center gap-2">
                            <span class="spinner-login"></span> Memproses...
                        </span>
                    </button>
                </form>

                {{-- Divider --}}
                <div class="login-divider">
                    <span class="line"></span>
                    <span>atau masuk dengan</span>
                    <span class="line"></span>
                </div>

                {{-- Security Box --}}
                <div class="security-box">
                    <div class="security-icon">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <div>
                        <div class="security-title">Login Aman &amp; Terpercaya</div>
                        <div class="security-sub">Data Anda terlindungi dengan enkripsi tingkat tinggi</div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="login-footer">
                &copy; {{ date('Y') }} Koperasi Poltas Kreatif Mandiri<br>
                Sistem Informasi Monitoring Anggota Syariah
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle tampilkan/sembunyikan password
    document.getElementById('togglePassword').addEventListener('click', function () {
        const pwd  = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            pwd.type = 'password';
            icon.className = 'bi bi-eye';
        }
    });

    // Loading state saat submit (logic & validasi backend tetap sama)
    document.getElementById('formLogin').addEventListener('submit', function () {
        document.getElementById('btnText').classList.add('d-none');
        document.getElementById('btnLoading').classList.remove('d-none');
        document.getElementById('btnLoading').classList.add('d-flex');
        document.getElementById('btnLogin').disabled = true;
    });
</script>
@endpush