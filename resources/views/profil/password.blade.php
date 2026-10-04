@extends('layouts.app')

@section('title', 'Ganti Password')
@section('page-title', 'Ganti Password')
@section('page-subtitle', 'Perbarui kata sandi akun Anda secara berkala untuk keamanan')

@section('sidebar-menu')
    @php $role = Auth::user()->role; @endphp
    @if ($role === 'admin')
        @include('admin.partials._sidebar')
    @elseif ($role === 'anggota')
        @include('anggota.partials._sidebar')
    @elseif ($role === 'pengurus')
        @include('pengurus.partials._sidebar')
    @elseif ($role === 'dps')
        @include('dps.partials._sidebar')
    @elseif ($role === 'ketua')
        @include('ketua.partials._sidebar')
    @endif
@endsection

@section('content')

    {{-- ── Breadcrumb ──────────────────────────────────────────── --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:.82rem; margin-bottom:0; background:transparent; padding:0;">
            <li class="breadcrumb-item">
                <a href="{{ route(Auth::user()->role . '.dashboard') }}"
                    style="color:var(--text-muted); text-decoration:none;">
                    <i class="bi bi-house-door me-1"></i>Dashboard
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('profil.index') }}" style="color:var(--text-muted); text-decoration:none;">Profil Saya</a>
            </li>
            <li class="breadcrumb-item active" style="color:var(--primary); font-weight:600;" aria-current="page">
                Ganti Password
            </li>
        </ol>
    </nav>

    <div class="mb-3">
        <a href="{{ route('profil.index') }}" class="btn-theme-light" style="padding:.4rem .9rem; font-size:.83rem;">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Profil
        </a>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-6">

            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <div
                        style="width:34px; height:34px; border-radius:10px; flex-shrink:0;
                        background:linear-gradient(135deg,#fdf2f8,#fce7f3);
                        display:flex; align-items:center; justify-content:center;
                        font-size:.95rem; color:#be185d;">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <span>Ganti Password</span>
                </div>
                <div class="card-body">

                    <div class="mb-3 p-3 d-flex align-items-start gap-2"
                        style="background:linear-gradient(135deg,var(--primary-light),#ede9fe);
                        border-radius:var(--radius-md); border-left:4px solid var(--primary);">
                        <i class="bi bi-info-circle-fill"
                            style="color:var(--primary); font-size:1rem; flex-shrink:0; margin-top:2px;"></i>
                        <div style="font-size:.8rem; color:var(--primary-dark); line-height:1.6;">
                            Gunakan kombinasi huruf, angka, dan simbol untuk password yang lebih kuat.
                            Password minimal <strong>8 karakter</strong>.
                        </div>
                    </div>

                    <form action="{{ route('profil.password.update') }}" method="POST" id="formGantiPassword">
                        @csrf
                        @method('PUT')

                        {{-- Password Lama --}}
                        <div class="mb-3">
                            <label for="password_lama" class="form-label">
                                Password Saat Ini <span class="text-danger">*</span>
                            </label>
                            <div style="position:relative;">
                                <input type="password" name="password_lama" id="password_lama"
                                    class="form-control @error('password_lama') is-invalid @enderror"
                                    style="padding-right:2.6rem;" required>
                                <button type="button" class="btn-toggle-password" data-target="password_lama"
                                    style="position:absolute; right:.7rem; top:50%; transform:translateY(-50%);
                                       background:none; border:none; color:var(--text-muted); cursor:pointer; font-size:.95rem;">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            @error('password_lama')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Password Baru --}}
                        <div class="mb-3">
                            <label for="password_baru" class="form-label">
                                Password Baru <span class="text-danger">*</span>
                            </label>
                            <div style="position:relative;">
                                <input type="password" name="password_baru" id="password_baru"
                                    class="form-control @error('password_baru') is-invalid @enderror"
                                    style="padding-right:2.6rem;" minlength="8" required>
                                <button type="button" class="btn-toggle-password" data-target="password_baru"
                                    style="position:absolute; right:.7rem; top:50%; transform:translateY(-50%);
                                       background:none; border:none; color:var(--text-muted); cursor:pointer; font-size:.95rem;">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            @error('password_baru')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div style="font-size:.73rem; color:var(--text-muted); margin-top:.35rem;">
                                <i class="bi bi-info-circle me-1"></i>Minimal 8 karakter.
                            </div>
                        </div>

                        {{-- Konfirmasi Password Baru --}}
                        <div class="mb-4">
                            <label for="password_baru_confirmation" class="form-label">
                                Konfirmasi Password Baru <span class="text-danger">*</span>
                            </label>
                            <div style="position:relative;">
                                <input type="password" name="password_baru_confirmation" id="password_baru_confirmation"
                                    class="form-control @error('password_baru_confirmation') is-invalid @enderror"
                                    style="padding-right:2.6rem;" minlength="8" required>
                                <button type="button" class="btn-toggle-password" data-target="password_baru_confirmation"
                                    style="position:absolute; right:.7rem; top:50%; transform:translateY(-50%);
                                       background:none; border:none; color:var(--text-muted); cursor:pointer; font-size:.95rem;">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            @error('password_baru_confirmation')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div id="matchIndicator" class="d-none mt-2" style="font-size:.76rem; font-weight:600;"></div>
                        </div>

                        {{-- Tombol --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn-theme-primary" id="btnSimpanPassword">
                                <span id="btnSimpanText" class="d-flex align-items-center gap-2">
                                    <i class="bi bi-check-lg"></i> Simpan Password Baru
                                </span>
                                <span id="btnSimpanLoading" class="d-none align-items-center gap-2">
                                    <span class="spinner-theme"></span> Menyimpan...
                                </span>
                            </button>
                            <a href="{{ route('profil.index') }}" class="btn-theme-light">Batal</a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <div class="theme-loading-overlay" id="loadingOverlay">
        <div class="theme-spinner"></div>
        <span>Menyimpan password...</span>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // ── Tombol show/hide password (JavaScript murni) ──────────
        document.querySelectorAll('.btn-toggle-password').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = this.querySelector('i');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            });
        });

        // ── Indikator kecocokan konfirmasi password real-time ─────
        const passwordBaru = document.getElementById('password_baru');
        const konfirmasi = document.getElementById('password_baru_confirmation');
        const matchIndicator = document.getElementById('matchIndicator');

        function cekKecocokan() {
            if (konfirmasi.value.length === 0) {
                matchIndicator.classList.add('d-none');
                return;
            }

            matchIndicator.classList.remove('d-none');

            if (passwordBaru.value === konfirmasi.value) {
                matchIndicator.style.color = '#047857';
                matchIndicator.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Password cocok';
            } else {
                matchIndicator.style.color = '#be123c';
                matchIndicator.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i>Password tidak cocok';
            }
        }

        passwordBaru.addEventListener('input', cekKecocokan);
        konfirmasi.addEventListener('input', cekKecocokan);

        // ── Loading saat submit ────────────────────────────────────
        document.getElementById('formGantiPassword').addEventListener('submit', function() {
            document.getElementById('btnSimpanText').classList.add('d-none');
            document.getElementById('btnSimpanLoading').classList.remove('d-none');
            document.getElementById('btnSimpanLoading').classList.add('d-flex');
            document.getElementById('btnSimpanPassword').disabled = true;
            document.getElementById('loadingOverlay').classList.add('show');
        });

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 4000,
                timerProgressBar: true,
                buttonsStyling: false,
                customClass: {
                    popup: 'swal-theme-popup',
                    title: 'swal-theme-title',
                    htmlContainer: 'swal-theme-html',
                    confirmButton: 'swal-theme-confirm'
                }
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
                buttonsStyling: false,
                customClass: {
                    popup: 'swal-theme-popup',
                    title: 'swal-theme-title',
                    htmlContainer: 'swal-theme-html',
                    confirmButton: 'swal-theme-deny'
                }
            });
        @endif
    </script>
@endpush
