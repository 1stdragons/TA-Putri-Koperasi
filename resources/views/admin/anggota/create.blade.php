@extends('layouts.app')

@section('title', 'Tambah Anggota')
@section('page-title', 'Tambah Data Anggota')
@section('page-subtitle', 'Daftarkan anggota baru koperasi')

@section('sidebar-menu')
    @include('admin.partials._sidebar')
@endsection

@section('content')

    <div class="mb-3">
        <a href="{{ route('admin.anggota.index') }}" class="btn-theme-light" style="padding:.4rem .9rem; font-size:.83rem;">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Anggota
        </a>
    </div>

    <form action="{{ route('admin.anggota.store') }}" method="POST" id="formAnggota">
        @csrf

        {{-- ══ SEKSI 1: Akun & Nomor Anggota ════════════════════════ --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center gap-2">
                <div
                    style="width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,#eef2ff,#e0e7ff);
                    display:flex;align-items:center;justify-content:center;font-size:.95rem;color:#4338ca;">
                    <i class="bi bi-person-check-fill"></i>
                </div>
                <span>Akun &amp; Nomor Keanggotaan</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-8">
                        <label for="akun_id" class="form-label">
                            Akun Terkait <span class="text-danger">*</span>
                        </label>
                        <select name="akun_id" id="akun_id" class="form-select @error('akun_id') is-invalid @enderror"
                            required>
                            <option value="">-- Pilih akun dengan role Anggota --</option>
                            @foreach ($akunAvailable as $akun)
                                <option value="{{ $akun->id }}" {{ old('akun_id') == $akun->id ? 'selected' : '' }}>
                                    {{ $akun->nama }} — {{ $akun->email }}
                                </option>
                            @endforeach
                        </select>
                        @error('akun_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if ($akunAvailable->isEmpty())
                            <div class="mt-2 d-flex align-items-center gap-2"
                                style="font-size:.78rem; color:var(--warning);">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                Semua akun dengan role Anggota sudah terdaftar.
                                <a href="{{ route('admin.akun.create') }}" style="color:var(--primary); font-weight:700;">
                                    Buat akun baru
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Nomor Anggota (Otomatis)</label>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge-soft tint-indigo"
                                style="font-size:.95rem; padding:.55rem 1.1rem; letter-spacing:.5px; font-family:monospace;">
                                <i class="bi bi-hash me-1"></i>{{ $nomorBerikutnya }}
                            </span>
                            <span style="font-size:.74rem; color:var(--text-muted);">
                                <i class="bi bi-info-circle me-1"></i>Digenerate otomatis
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ SEKSI 2: Data Diri ════════════════════════════════════ --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center gap-2">
                <div
                    style="width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,#ecfdf5,#d1fae5);
                    display:flex;align-items:center;justify-content:center;font-size:.95rem;color:#047857;">
                    <i class="bi bi-person-lines-fill"></i>
                </div>
                <span>Data Diri</span>
            </div>
            <div class="card-body">
                <div class="row g-3">

                    <div class="col-12 col-md-4">
                        <label for="nik" class="form-label">NIK <span class="text-danger">*</span></label>
                        <input type="text" name="nik" id="nik"
                            class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik') }}"
                            placeholder="16 digit NIK" maxlength="16" required>
                        @error('nik')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="tempat_lahir" class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                        <input type="text" name="tempat_lahir" id="tempat_lahir"
                            class="form-control @error('tempat_lahir') is-invalid @enderror"
                            value="{{ old('tempat_lahir') }}" placeholder="Kota/kabupaten tempat lahir" required>
                        @error('tempat_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span
                                class="text-danger">*</span></label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                            class="form-control @error('tanggal_lahir') is-invalid @enderror"
                            value="{{ old('tanggal_lahir') }}" required>
                        @error('tanggal_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span
                                class="text-danger">*</span></label>
                        <select name="jenis_kelamin" id="jenis_kelamin"
                            class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                            <option value="">-- Pilih --</option>
                            <option value="L" {{ old('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="telepon" class="form-label">Telepon <span class="text-danger">*</span></label>
                        <input type="text" name="telepon" id="telepon"
                            class="form-control @error('telepon') is-invalid @enderror" value="{{ old('telepon') }}"
                            placeholder="08xxxxxxxxxx" required>
                        @error('telepon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="pendidikan_terakhir" class="form-label">Pendidikan Terakhir</label>
                        <select name="pendidikan_terakhir" id="pendidikan_terakhir"
                            class="form-select @error('pendidikan_terakhir') is-invalid @enderror">
                            <option value="">-- Pilih --</option>
                            @foreach (['SD', 'SMP', 'SMA/SMK', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'] as $pend)
                                <option value="{{ $pend }}"
                                    {{ old('pendidikan_terakhir') === $pend ? 'selected' : '' }}>
                                    {{ $pend }}
                                </option>
                            @endforeach
                        </select>
                        @error('pendidikan_terakhir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="pekerjaan_sampingan" class="form-label">Pekerjaan Sampingan</label>
                        <input type="text" name="pekerjaan_sampingan" id="pekerjaan_sampingan"
                            class="form-control @error('pekerjaan_sampingan') is-invalid @enderror"
                            value="{{ old('pekerjaan_sampingan') }}"
                            placeholder="Jika ada pekerjaan sampingan selain usaha utama">
                        @error('pekerjaan_sampingan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ SEKSI 3: Alamat ════════════════════════════════════════ --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center gap-2">
                <div
                    style="width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,#fffbeb,#fef3c7);
                    display:flex;align-items:center;justify-content:center;font-size:.95rem;color:#b45309;">
                    <i class="bi bi-geo-alt-fill"></i>
                </div>
                <span>Alamat Domisili</span>
            </div>
            <div class="card-body">
                <div class="row g-3">

                    <div class="col-12">
                        <label for="alamat" class="form-label">Alamat Lengkap <span
                                class="text-danger">*</span></label>
                        <textarea name="alamat" id="alamat" rows="2" class="form-control @error('alamat') is-invalid @enderror"
                            placeholder="Nama jalan, nomor rumah..." required>{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-4 col-md-2">
                        <label for="rt" class="form-label">RT</label>
                        <input type="text" name="rt" id="rt" class="form-control"
                            value="{{ old('rt') }}" placeholder="001" maxlength="5">
                    </div>

                    <div class="col-4 col-md-2">
                        <label for="rw" class="form-label">RW</label>
                        <input type="text" name="rw" id="rw" class="form-control"
                            value="{{ old('rw') }}" placeholder="002" maxlength="5">
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="kelurahan" class="form-label">Kelurahan/Desa</label>
                        <input type="text" name="kelurahan" id="kelurahan" class="form-control"
                            value="{{ old('kelurahan') }}" placeholder="Nama kelurahan atau desa">
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="kecamatan" class="form-label">Kecamatan</label>
                        <input type="text" name="kecamatan" id="kecamatan" class="form-control"
                            value="{{ old('kecamatan') }}" placeholder="Nama kecamatan">
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="kabupaten" class="form-label">Kabupaten/Kota</label>
                        <input type="text" name="kabupaten" id="kabupaten" class="form-control"
                            value="{{ old('kabupaten') }}" placeholder="Nama kabupaten atau kota">
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="provinsi" class="form-label">Provinsi</label>
                        <input type="text" name="provinsi" id="provinsi" class="form-control"
                            value="{{ old('provinsi') }}" placeholder="Nama provinsi">
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ SEKSI 4: Keanggotaan ═══════════════════════════════════ --}}
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <div
                    style="width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,#fdf2f8,#fce7f3);
                    display:flex;align-items:center;justify-content:center;font-size:.95rem;color:#be185d;">
                    <i class="bi bi-award-fill"></i>
                </div>
                <span>Status Keanggotaan</span>
            </div>
            <div class="card-body">
                <div class="row g-3">

                    <div class="col-12 col-md-4">
                        <label for="tanggal_bergabung" class="form-label">
                            Tanggal Bergabung <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="tanggal_bergabung" id="tanggal_bergabung"
                            class="form-control @error('tanggal_bergabung') is-invalid @enderror"
                            value="{{ old('tanggal_bergabung', date('Y-m-d')) }}" required>
                        @error('tanggal_bergabung')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="status_anggota" class="form-label">
                            Status Anggota <span class="text-danger">*</span>
                        </label>
                        <select name="status_anggota" id="status_anggota"
                            class="form-select @error('status_anggota') is-invalid @enderror" required>
                            <option value="aktif" {{ old('status_anggota', 'aktif') === 'aktif' ? 'selected' : '' }}>
                                Aktif</option>
                            <option value="peringatan" {{ old('status_anggota') === 'peringatan' ? 'selected' : '' }}>
                                Peringatan</option>
                            <option value="tidak_aktif" {{ old('status_anggota') === 'tidak_aktif' ? 'selected' : '' }}>
                                Tidak Aktif</option>
                        </select>
                        @error('status_anggota')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="catatan" class="form-label">Catatan</label>
                        <textarea name="catatan" id="catatan" rows="3" class="form-control @error('catatan') is-invalid @enderror"
                            placeholder="Catatan tambahan mengenai anggota (opsional)">{{ old('catatan') }}</textarea>
                        @error('catatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Tombol ───────────────────────────────────────────────── --}}
        <div class="d-flex gap-2">
            <button type="submit" class="btn-theme-primary">
                <i class="bi bi-check-lg me-1"></i> Simpan Data Anggota
            </button>
            <a href="{{ route('admin.anggota.index') }}" class="btn-theme-light">
                Batal
            </a>
        </div>

    </form>

@endsection
