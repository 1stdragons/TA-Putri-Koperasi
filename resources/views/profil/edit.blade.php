@extends('layouts.app')

@section('title', 'Edit Profil')
@section('page-title', 'Edit Profil')
@section('page-subtitle', 'Perbarui informasi dasar akun Anda')

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
                Edit Profil
            </li>
        </ol>
    </nav>

    <div class="mb-3">
        <a href="{{ route('profil.index') }}" class="btn-theme-light" style="padding:.4rem .9rem; font-size:.83rem;">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Profil
        </a>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-8">

            <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data" id="formEditProfil">
                @csrf
                @method('PUT')

                {{-- ══ SEKSI 1: Foto Profil ═══════════════════════════════ --}}
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center gap-2">
                        <div
                            style="width:34px; height:34px; border-radius:10px; flex-shrink:0;
                            background:linear-gradient(135deg,#eef2ff,#e0e7ff);
                            display:flex; align-items:center; justify-content:center;
                            font-size:.95rem; color:#4338ca;">
                            <i class="bi bi-camera-fill"></i>
                        </div>
                        <span>Foto Profil</span>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-4 flex-wrap">

                            {{-- Preview foto --}}
                            <div style="flex-shrink:0;">
                                <img id="previewFoto" src="{{ $akun->foto_url }}" alt="Preview Foto"
                                    style="width:100px; height:100px; border-radius:50%; object-fit:cover;
                                    border:3px solid var(--border-soft); background:#fff;">
                            </div>

                            {{-- Upload area --}}
                            <div style="flex:1; min-width:220px;">
                                <div id="uploadArea"
                                    style="border:2px dashed #c7d2fe; border-radius:var(--radius-md);
                                    background:var(--primary-light); padding:1rem;
                                    text-align:center; cursor:pointer; transition:.2s;">
                                    <i class="bi bi-cloud-upload"
                                        style="font-size:1.4rem; color:var(--primary); display:block; margin-bottom:.3rem;"></i>
                                    <div id="uploadText"
                                        style="font-size:.8rem; font-weight:600; color:var(--primary-dark);">
                                        Klik atau seret foto ke sini
                                    </div>
                                    <div style="font-size:.7rem; color:var(--text-muted); margin-top:.2rem;">
                                        JPG, JPEG, PNG, WEBP &mdash; Maks. 2 MB
                                    </div>
                                    <input type="file" name="foto" id="foto" accept=".jpg,.jpeg,.png,.webp"
                                        class="@error('foto') is-invalid @enderror"
                                        style="position:absolute; width:0; height:0; opacity:0;">
                                </div>
                                @error('foto')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror

                                <div id="fileInfo" class="d-none mt-2 d-flex align-items-center gap-2"
                                    style="background:var(--bg-soft); border-radius:var(--radius-sm); padding:.5rem .8rem;">
                                    <i class="bi bi-file-earmark-image-fill"
                                        style="color:#7c3aed; font-size:1.1rem; flex-shrink:0;"></i>
                                    <div style="flex:1; min-width:0;">
                                        <div id="fileName"
                                            style="font-size:.78rem; font-weight:700; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                        </div>
                                        <div id="fileSize" style="font-size:.7rem; color:var(--text-muted);"></div>
                                    </div>
                                    <button type="button" id="btnClearFile"
                                        style="background:none; border:none; color:var(--danger); font-size:1rem; cursor:pointer;">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button>
                                </div>

                                @if ($akun->foto)
                                    <button type="button" id="btnHapusFoto" class="btn-theme-danger mt-2"
                                        style="padding:.35rem .8rem; font-size:.76rem;">
                                        <i class="bi bi-trash me-1"></i> Hapus Foto Profil
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══ SEKSI 2: Informasi Dasar ════════════════════════════ --}}
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center gap-2">
                        <div
                            style="width:34px; height:34px; border-radius:10px; flex-shrink:0;
                            background:linear-gradient(135deg,#ecfdf5,#d1fae5);
                            display:flex; align-items:center; justify-content:center;
                            font-size:.95rem; color:#047857;">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <span>Informasi Dasar</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="nama" class="form-label">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nama" id="nama"
                                    class="form-control @error('nama') is-invalid @enderror"
                                    value="{{ old('nama', $akun->nama) }}" required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="email" class="form-label">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" name="email" id="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $akun->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div style="font-size:.73rem; color:var(--text-muted); margin-top:.35rem;">
                                    <i class="bi bi-info-circle me-1"></i>Email ini digunakan untuk login ke sistem.
                                </div>
                            </div>

                            {{-- Info field yang tidak bisa diubah --}}
                            <div class="col-12">
                                <div class="d-flex align-items-start gap-2"
                                    style="background:var(--bg-soft); border-radius:var(--radius-sm); padding:.7rem .9rem;">
                                    <i class="bi bi-lock-fill"
                                        style="color:var(--text-muted); font-size:.85rem; flex-shrink:0; margin-top:2px;"></i>
                                    <div style="font-size:.76rem; color:var(--text-muted); line-height:1.55;">
                                        Role, status akun, dan data keanggotaan administratif tidak dapat diubah melalui
                                        halaman ini.
                                        Hubungi Admin apabila diperlukan perubahan pada data tersebut.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tombol --}}
                <div class="d-flex gap-2">
                    <button type="submit" class="btn-theme-primary" id="btnSimpan">
                        <span id="btnSimpanText" class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-lg"></i> Simpan Perubahan
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

    {{-- Form terpisah untuk hapus foto (agar tidak bentrok dengan form utama) --}}
    <form id="formHapusFoto" action="{{ route('profil.foto.destroy') }}" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    <div class="theme-loading-overlay" id="loadingOverlay">
        <div class="theme-spinner"></div>
        <span>Menyimpan perubahan...</span>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // ── Upload area & preview foto (JavaScript murni) ─────────
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('foto');
        const fileInfo = document.getElementById('fileInfo');
        const previewFoto = document.getElementById('previewFoto');

        uploadArea.addEventListener('click', () => fileInput.click());

        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadArea.style.borderColor = 'var(--primary)';
            uploadArea.style.background = '#e0e7ff';
        });

        uploadArea.addEventListener('dragleave', function() {
            uploadArea.style.borderColor = '#c7d2fe';
            uploadArea.style.background = 'var(--primary-light)';
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadArea.style.borderColor = '#c7d2fe';
            uploadArea.style.background = 'var(--primary-light)';
            if (e.dataTransfer.files.length > 0) {
                const dt = new DataTransfer();
                dt.items.add(e.dataTransfer.files[0]);
                fileInput.files = dt.files;
                tampilkanPreview(e.dataTransfer.files[0]);
            }
        });

        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                tampilkanPreview(this.files[0]);
            }
        });

        function tampilkanPreview(file) {
            // Validasi tipe file di sisi client (validasi utama tetap di server)
            const tipeDiizinkan = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (!tipeDiizinkan.includes(file.type)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Format Tidak Didukung',
                    text: 'Gunakan format JPG, JPEG, PNG, atau WEBP.',
                    buttonsStyling: false,
                    customClass: {
                        popup: 'swal-theme-popup',
                        title: 'swal-theme-title',
                        htmlContainer: 'swal-theme-html',
                        confirmButton: 'swal-theme-confirm'
                    }
                });
                fileInput.value = '';
                return;
            }

            const maksUkuran = 2 * 1024 * 1024; // 2 MB
            if (file.size > maksUkuran) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Ukuran Terlalu Besar',
                    text: 'Ukuran foto maksimal 2 MB.',
                    buttonsStyling: false,
                    customClass: {
                        popup: 'swal-theme-popup',
                        title: 'swal-theme-title',
                        htmlContainer: 'swal-theme-html',
                        confirmButton: 'swal-theme-confirm'
                    }
                });
                fileInput.value = '';
                return;
            }

            // Tampilkan info file
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('fileSize').textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
            fileInfo.classList.remove('d-none');
            fileInfo.classList.add('d-flex');
            document.getElementById('uploadText').textContent = 'Foto dipilih. Klik untuk mengganti.';

            // Preview gambar langsung ke elemen foto profil bulat
            const reader = new FileReader();
            reader.onload = function(e) {
                previewFoto.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }

        document.getElementById('btnClearFile').addEventListener('click', function() {
            fileInput.value = '';
            fileInfo.classList.add('d-none');
            fileInfo.classList.remove('d-flex');
            document.getElementById('uploadText').textContent = 'Klik atau seret foto ke sini';
            previewFoto.src = '{{ $akun->foto_url }}';
        });

        // ── Hapus foto profil (konfirmasi SweetAlert) ─────────────
        const btnHapusFoto = document.getElementById('btnHapusFoto');
        if (btnHapusFoto) {
            btnHapusFoto.addEventListener('click', function() {
                Swal.fire({
                    title: 'Hapus Foto Profil?',
                    html: '<p style="font-size:.87rem; color:var(--text-muted);">Foto profil Anda akan dihapus dan digantikan dengan avatar default.</p>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    buttonsStyling: false,
                    customClass: {
                        popup: 'swal-theme-popup',
                        title: 'swal-theme-title',
                        htmlContainer: 'swal-theme-html',
                        confirmButton: 'swal-theme-deny',
                        cancelButton: 'swal-theme-cancel',
                    }
                }).then(function(result) {
                    if (result.isConfirmed) {
                        document.getElementById('loadingOverlay').classList.add('show');
                        document.getElementById('formHapusFoto').submit();
                    }
                });
            });
        }

        // ── Loading saat submit form utama ────────────────────────
        document.getElementById('formEditProfil').addEventListener('submit', function() {
            document.getElementById('btnSimpanText').classList.add('d-none');
            document.getElementById('btnSimpanLoading').classList.remove('d-none');
            document.getElementById('btnSimpanLoading').classList.add('d-flex');
            document.getElementById('btnSimpan').disabled = true;
            document.getElementById('loadingOverlay').classList.add('show');
        });

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
