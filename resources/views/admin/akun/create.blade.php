@extends('layouts.app')

@section('title', 'Tambah Akun')
@section('page-title', 'Tambah Akun Baru')
@section('page-subtitle', 'Buat akun pengguna baru untuk sistem')

@section('sidebar-menu')
    @include('admin.partials._sidebar')
@endsection

@section('content')

    <div class="mb-3">
        <a href="{{ route('admin.akun.index') }}" class="text-decoration-none"
            style="font-size:.85rem; font-weight:600; color:#6366f1;">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Akun
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="akun-form-card">

                <div class="d-flex align-items-center gap-3 mb-4">
                    <div
                        style="width:46px; height:46px; border-radius:13px; background:linear-gradient(135deg,#eef2ff,#e0e7ff); display:flex; align-items:center; justify-content:center; font-size:1.2rem; color:#4338ca;">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold" style="color:#1e1b4b;">Form Tambah Akun</h6>
                        <small class="text-muted">Lengkapi data di bawah ini dengan benar</small>
                    </div>
                </div>

                <form action="{{ route('admin.akun.store') }}" method="POST" enctype="multipart/form-data" id="formAkun">
                    @csrf

                    {{-- Upload Foto --}}
                    <div class="mb-4">
                        <label class="form-label">Foto Profil</label>
                        <div class="akun-photo-upload">
                            <img src="{{ asset('images/default-avatar.png') }}" alt="Preview" class="akun-photo-preview"
                                id="previewFoto">
                            <div>
                                <label for="foto" class="akun-photo-upload-btn">
                                    <i class="bi bi-cloud-upload"></i> Pilih Foto
                                </label>
                                <input type="file" name="foto" id="foto" accept="image/*"
                                    class="d-none @error('foto') is-invalid @enderror">
                                <div class="text-muted mt-1" style="font-size:.74rem;">JPG, PNG, atau WEBP. Maks 2MB.</div>
                                @error('foto')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        {{-- Nama --}}
                        <div class="col-12 col-md-6">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama" id="nama"
                                class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}"
                                placeholder="Masukkan nama lengkap" required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="col-12 col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email"
                                class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                                placeholder="contoh@email.com" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="col-12 col-md-6">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Minimal 6 karakter" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Konfirmasi Password --}}
                        <div class="col-12 col-md-6">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="form-control" placeholder="Ulangi password" required>
                        </div>

                        {{-- Role --}}
                        <div class="col-12 col-md-6">
                            <label for="role" class="form-label">Role</label>
                            <select name="role" id="role" class="form-select @error('role') is-invalid @enderror"
                                required>
                                <option value="">-- Pilih Role --</option>
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator
                                </option>
                                <option value="anggota" {{ old('role') === 'anggota' ? 'selected' : '' }}>Anggota</option>
                                <option value="pengurus" {{ old('role') === 'pengurus' ? 'selected' : '' }}>Pengurus
                                </option>
                                <option value="dps" {{ old('role') === 'dps' ? 'selected' : '' }}>DPS</option>
                                <option value="ketua" {{ old('role') === 'ketua' ? 'selected' : '' }}>Ketua</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div class="col-12 col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror"
                                required>
                                <option value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif
                                </option>
                                <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn-theme-primary">
                            <i class="bi bi-check-lg me-1"></i> Simpan Akun
                        </button>
                        <a href="{{ route('admin.akun.index') }}" class="btn-theme-light">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // ── Preview foto sebelum upload ──────────────────────────
        document.getElementById('foto').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    document.getElementById('previewFoto').src = ev.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
@endpush
