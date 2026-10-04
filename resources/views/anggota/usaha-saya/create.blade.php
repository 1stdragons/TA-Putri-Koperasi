@extends('layouts.app')

@section('title', 'Tambah Usaha Baru')
@section('page-title', 'Tambah Usaha Baru')
@section('page-subtitle', 'Ajukan usaha tambahan untuk mendapatkan validasi akad dari DPS')

@section('sidebar-menu')
    @include('anggota.partials._sidebar')
@endsection

@section('content')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div>
            <h5 class="mb-1" style="font-weight:800; color:var(--text-dark);">
                Form Pengajuan Usaha
            </h5>
            <p class="mb-0" style="font-size:.84rem; color:var(--text-muted);">
                Usaha baru belum langsung aktif. Dokumen akad akan diperiksa oleh DPS terlebih dahulu.
            </p>
        </div>

        <a href="{{ route('anggota.usaha-saya.index') }}" class="btn-theme-light">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger border-0 mb-3" style="border-radius:var(--radius-md);">
            <div class="d-flex align-items-start gap-2">
                <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                <div>
                    <div class="fw-bold mb-1">Data belum dapat disimpan.</div>
                    <ul class="mb-0 ps-3" style="font-size:.84rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('anggota.usaha-saya.store') }}" method="POST" enctype="multipart/form-data"
        id="formTambahUsaha">
        @csrf

        <div class="row g-3">
            <div class="col-12 col-xl-8">
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="bi bi-shop-window me-2" style="color:var(--primary);"></i>
                        Informasi Usaha
                    </div>

                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="nama_usaha" class="form-label">
                                    Nama Usaha <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nama_usaha" id="nama_usaha"
                                    class="form-control @error('nama_usaha') is-invalid @enderror"
                                    value="{{ old('nama_usaha') }}" maxlength="150" placeholder="Contoh: Warung Kue Siti"
                                    required>
                                @error('nama_usaha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="jenis_usaha" class="form-label">
                                    Jenis Usaha <span class="text-danger">*</span>
                                </label>
                                <select name="jenis_usaha" id="jenis_usaha"
                                    class="form-select @error('jenis_usaha') is-invalid @enderror" required>
                                    <option value="">Pilih jenis usaha</option>
                                    <option value="perdagangan" @selected(old('jenis_usaha') === 'perdagangan')>Perdagangan</option>
                                    <option value="jasa" @selected(old('jenis_usaha') === 'jasa')>Jasa</option>
                                    <option value="produksi" @selected(old('jenis_usaha') === 'produksi')>Produksi</option>
                                    <option value="pertanian" @selected(old('jenis_usaha') === 'pertanian')>Pertanian</option>
                                    <option value="lainnya" @selected(old('jenis_usaha') === 'lainnya')>Lainnya</option>
                                </select>
                                @error('jenis_usaha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="tanggal_mulai" class="form-label">
                                    Tanggal Mulai Usaha <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                                    class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                    value="{{ old('tanggal_mulai') }}" max="{{ now()->format('Y-m-d') }}" required>
                                @error('tanggal_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="alamat_usaha" class="form-label">
                                    Alamat Usaha <span class="text-danger">*</span>
                                </label>
                                <textarea name="alamat_usaha" id="alamat_usaha" rows="3"
                                    class="form-control @error('alamat_usaha') is-invalid @enderror" placeholder="Masukkan alamat lengkap tempat usaha"
                                    maxlength="2000" required>{{ old('alamat_usaha') }}</textarea>
                                @error('alamat_usaha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="deskripsi" class="form-label">Deskripsi Usaha</label>
                                <textarea name="deskripsi" id="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror"
                                    placeholder="Jelaskan kegiatan, produk, atau layanan usaha" maxlength="3000">{{ old('deskripsi') }}</textarea>
                                @error('deskripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="modal_awal" class="form-label">
                                    Modal Awal <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="modal_awal" id="modal_awal"
                                        class="form-control @error('modal_awal') is-invalid @enderror"
                                        value="{{ old('modal_awal') }}" min="0" step="1" placeholder="0"
                                        required>
                                    @error('modal_awal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="catatan" class="form-label">Catatan Tambahan</label>
                                <textarea name="catatan" id="catatan" rows="3" class="form-control @error('catatan') is-invalid @enderror"
                                    placeholder="Tambahkan informasi lain apabila diperlukan" maxlength="2000">{{ old('catatan') }}</textarea>
                                @error('catatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="bi bi-patch-check-fill me-2" style="color:var(--secondary);"></i>
                        Akad Syariah
                    </div>

                    <div class="card-body">
                        <div class="mb-3">
                            <label for="jenis_akad" class="form-label">
                                Jenis Akad <span class="text-danger">*</span>
                            </label>
                            <select name="jenis_akad" id="jenis_akad"
                                class="form-select @error('jenis_akad') is-invalid @enderror" required>
                                <option value="">Pilih jenis akad</option>
                                @foreach (['Mudharabah', 'Musyarakah', 'Murabahah', 'Ijarah', 'Qardh', 'Lainnya'] as $akad)
                                    <option value="{{ $akad }}" @selected(old('jenis_akad') === $akad)>
                                        {{ $akad }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jenis_akad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-2">
                            <label for="file_akad" class="form-label">
                                Dokumen Akad <span class="text-danger">*</span>
                            </label>
                            <input type="file" name="file_akad" id="file_akad"
                                class="form-control @error('file_akad') is-invalid @enderror" accept=".pdf,.doc,.docx"
                                required>
                            @error('file_akad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div style="font-size:.76rem; color:var(--text-muted); line-height:1.55;">
                            Format yang diizinkan: PDF, DOC, atau DOCX. Ukuran maksimal 5 MB.
                        </div>

                        <div class="mt-3 p-3"
                            style="background:linear-gradient(135deg,#fffbeb,#fef3c7);
                                border-radius:var(--radius-md); border-left:4px solid #f59e0b;">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-info-circle-fill" style="color:#b45309;"></i>
                                <div style="font-size:.78rem; color:#92400e; line-height:1.55;">
                                    Setelah disimpan, usaha akan berstatus menunggu validasi DPS.
                                    Laporan monitoring hanya dapat dibuat setelah akad disetujui dan usaha aktif.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn-theme-primary w-100" id="btnSimpan">
                            <span id="simpanText">
                                <i class="bi bi-send-fill me-1"></i>
                                Ajukan Usaha
                            </span>
                            <span id="simpanLoading" class="d-none">
                                <span class="spinner-border spinner-border-sm me-1"></span>
                                Menyimpan...
                            </span>
                        </button>

                        <a href="{{ route('anggota.usaha-saya.index') }}" class="btn-theme-light w-100 mt-2 text-center">
                            Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formTambahUsaha');
            const button = document.getElementById('btnSimpan');
            const text = document.getElementById('simpanText');
            const loading = document.getElementById('simpanLoading');

            if (!form || !button || !text || !loading) {
                return;
            }

            form.addEventListener('submit', function() {
                if (!form.checkValidity()) {
                    return;
                }

                button.disabled = true;
                text.classList.add('d-none');
                loading.classList.remove('d-none');
            });
        });
    </script>
@endpush
