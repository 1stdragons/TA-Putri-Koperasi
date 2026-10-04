@extends('layouts.app')

@section('title', 'Edit Anggota')
@section('page-title', 'Edit Data Anggota')
@section('page-subtitle', 'Perbarui data anggota ' . $anggota->akun->nama)

@section('sidebar-menu')
    @include('admin.partials._sidebar')
@endsection

@section('content')

    <div class="mb-3">
        <a href="{{ route('admin.anggota.index') }}" class="btn-theme-light" style="padding:.4rem .9rem; font-size:.83rem;">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Anggota
        </a>
    </div>

    <form action="{{ route('admin.anggota.update', $anggota->id) }}" method="POST" id="formAnggota">
        @csrf
        @method('PUT')

        {{-- ══ SEKSI 1: Akun & Nomor ══════════════════════════════════ --}}
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
                            <option value="">-- Pilih akun --</option>
                            @foreach ($akunAvailable as $akun)
                                <option value="{{ $akun->id }}"
                                    {{ old('akun_id', $anggota->akun_id) == $akun->id ? 'selected' : '' }}>
                                    {{ $akun->nama }} — {{ $akun->email }}
                                </option>
                            @endforeach
                        </select>
                        @error('akun_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Nomor Anggota</label>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge-soft tint-indigo"
                                style="font-size:.95rem; padding:.55rem 1.1rem; letter-spacing:.5px; font-family:monospace;">
                                <i class="bi bi-hash me-1"></i>{{ $anggota->nomor_anggota }}
                            </span>
                            <span style="font-size:.72rem; color:var(--text-muted);">
                                <i class="bi bi-lock me-1"></i>Tidak dapat diubah
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
                            class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik', $anggota->nik) }}"
                            placeholder="16 digit NIK" maxlength="16" required>
                        @error('nik')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="tempat_lahir" class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                        <input type="text" name="tempat_lahir" id="tempat_lahir"
                            class="form-control @error('tempat_lahir') is-invalid @enderror"
                            value="{{ old('tempat_lahir', $anggota->tempat_lahir) }}" required>
                        @error('tempat_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span
                                class="text-danger">*</span></label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                            class="form-control @error('tanggal_lahir') is-invalid @enderror"
                            value="{{ old('tanggal_lahir', $anggota->tanggal_lahir->format('Y-m-d')) }}" required>
                        @error('tanggal_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span
                                class="text-danger">*</span></label>
                        <select name="jenis_kelamin" id="jenis_kelamin"
                            class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                            <option value="L"
                                {{ old('jenis_kelamin', $anggota->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-laki
                            </option>
                            <option value="P"
                                {{ old('jenis_kelamin', $anggota->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan
                            </option>
                        </select>
                        @error('jenis_kelamin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="telepon" class="form-label">Telepon <span class="text-danger">*</span></label>
                        <input type="text" name="telepon" id="telepon"
                            class="form-control @error('telepon') is-invalid @enderror"
                            value="{{ old('telepon', $anggota->telepon) }}" required>
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
                                    {{ old('pendidikan_terakhir', $anggota->pendidikan_terakhir) === $pend ? 'selected' : '' }}>
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
                        <input type="text" name="pekerjaan_sampingan" id="pekerjaan_sampingan" class="form-control"
                            value="{{ old('pekerjaan_sampingan', $anggota->pekerjaan_sampingan) }}"
                            placeholder="Opsional">
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
                            required>{{ old('alamat', $anggota->alamat) }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-4 col-md-2">
                        <label for="rt" class="form-label">RT</label>
                        <input type="text" name="rt" id="rt" class="form-control"
                            value="{{ old('rt', $anggota->rt) }}" maxlength="5">
                    </div>
                    <div class="col-4 col-md-2">
                        <label for="rw" class="form-label">RW</label>
                        <input type="text" name="rw" id="rw" class="form-control"
                            value="{{ old('rw', $anggota->rw) }}" maxlength="5">
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="kelurahan" class="form-label">Kelurahan/Desa</label>
                        <input type="text" name="kelurahan" id="kelurahan" class="form-control"
                            value="{{ old('kelurahan', $anggota->kelurahan) }}">
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="kecamatan" class="form-label">Kecamatan</label>
                        <input type="text" name="kecamatan" id="kecamatan" class="form-control"
                            value="{{ old('kecamatan', $anggota->kecamatan) }}">
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="kabupaten" class="form-label">Kabupaten/Kota</label>
                        <input type="text" name="kabupaten" id="kabupaten" class="form-control"
                            value="{{ old('kabupaten', $anggota->kabupaten) }}">
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="provinsi" class="form-label">Provinsi</label>
                        <input type="text" name="provinsi" id="provinsi" class="form-control"
                            value="{{ old('provinsi', $anggota->provinsi) }}">
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
                            value="{{ old('tanggal_bergabung', $anggota->tanggal_bergabung->format('Y-m-d')) }}" required>
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
                            <option value="aktif"
                                {{ old('status_anggota', $anggota->status_anggota) === 'aktif' ? 'selected' : '' }}>
                                Aktif</option>
                            <option value="peringatan"
                                {{ old('status_anggota', $anggota->status_anggota) === 'peringatan' ? 'selected' : '' }}>
                                Peringatan</option>
                            <option value="tidak_aktif"
                                {{ old('status_anggota', $anggota->status_anggota) === 'tidak_aktif' ? 'selected' : '' }}>
                                Tidak Aktif</option>
                        </select>
                        @error('status_anggota')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="catatan" class="form-label">Catatan</label>
                        <textarea name="catatan" id="catatan" rows="3" class="form-control">{{ old('catatan', $anggota->catatan) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn-theme-primary">
                <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
            </button>
            <a href="{{ route('admin.anggota.show', $anggota->id) }}" class="btn-theme-light">
                Batal
            </a>
        </div>

    </form>

@endsection
