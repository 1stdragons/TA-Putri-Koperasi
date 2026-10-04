@extends('layouts.app')

@section('title', 'Edit Usaha')
@section('page-title', 'Edit Data Usaha')
@section('page-subtitle', 'Perbarui data usaha ' . $usaha->nama_usaha)

@section('sidebar-menu')
    @include('admin.partials._sidebar')
@endsection

@section('content')

    {{-- Tombol kembali --}}
    <div class="mb-3">
        <a href="{{ route('admin.usaha.show', $usaha->id) }}" class="btn-theme-light"
            style="padding:.4rem .9rem; font-size:.83rem;">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Detail Usaha
        </a>
    </div>

    <form action="{{ route('admin.usaha.update', $usaha->id) }}" method="POST" enctype="multipart/form-data" id="formUsaha">
        @csrf
        @method('PUT')

        {{-- ══════════════════════════════════════════════════════════
     SEKSI 1: Identitas Usaha
══════════════════════════════════════════════════════════ --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center gap-2">
                <div
                    style="width:34px; height:34px; border-radius:10px; flex-shrink:0;
                    background:linear-gradient(135deg,#eef2ff,#e0e7ff);
                    display:flex; align-items:center; justify-content:center;
                    font-size:.95rem; color:#4338ca;">
                    <i class="bi bi-shop-window"></i>
                </div>
                <span>Identitas Usaha</span>
            </div>
            <div class="card-body">
                <div class="row g-3">

                    {{-- Anggota --}}
                    <div class="col-12 col-md-6">
                        <label for="anggota_id" class="form-label">
                            Pemilik Usaha (Anggota) <span class="text-danger">*</span>
                        </label>
                        <select name="anggota_id" id="anggota_id"
                            class="form-select @error('anggota_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Anggota --</option>
                            @foreach ($anggotaList as $anggota)
                                <option value="{{ $anggota->id }}"
                                    {{ old('anggota_id', $usaha->anggota_id) == $anggota->id ? 'selected' : '' }}>
                                    {{ $anggota->nomor_anggota }} — {{ $anggota->akun->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('anggota_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nama Usaha --}}
                    <div class="col-12 col-md-6">
                        <label for="nama_usaha" class="form-label">
                            Nama Usaha <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama_usaha" id="nama_usaha"
                            class="form-control @error('nama_usaha') is-invalid @enderror"
                            value="{{ old('nama_usaha', $usaha->nama_usaha) }}" required>
                        @error('nama_usaha')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Jenis Usaha --}}
                    <div class="col-12 col-md-6">
                        <label for="jenis_usaha" class="form-label">
                            Jenis Usaha <span class="text-danger">*</span>
                        </label>
                        <select name="jenis_usaha" id="jenis_usaha"
                            class="form-select @error('jenis_usaha') is-invalid @enderror" required>
                            <option value="">-- Pilih Jenis Usaha --</option>
                            @foreach (['perdagangan' => 'Perdagangan', 'jasa' => 'Jasa', 'produksi' => 'Produksi', 'pertanian' => 'Pertanian', 'lainnya' => 'Lainnya'] as $val => $label)
                                <option value="{{ $val }}"
                                    {{ old('jenis_usaha', $usaha->jenis_usaha) === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('jenis_usaha')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Status Usaha --}}
                    <div class="col-12 col-md-6">
                        <label class="form-label">
                            Status Usaha <span class="text-danger">*</span>
                        </label>

                        @if ($usaha->status_validasi_syariah === 'disetujui')
                            <select name="status_usaha" id="status_usaha"
                                class="form-select @error('status_usaha') is-invalid @enderror" required>
                                <option value="aktif"
                                    {{ old('status_usaha', $usaha->status_usaha) === 'aktif' ? 'selected' : '' }}>
                                    Aktif
                                </option>
                                <option value="vakum"
                                    {{ old('status_usaha', $usaha->status_usaha) === 'vakum' ? 'selected' : '' }}>
                                    Vakum
                                </option>
                                <option value="tutup"
                                    {{ old('status_usaha', $usaha->status_usaha) === 'tutup' ? 'selected' : '' }}>
                                    Tutup
                                </option>
                            </select>

                            <div class="mt-2 d-flex align-items-start gap-2"
                                style="background:#ecfdf5; border:1px solid #a7f3d0;
                                border-radius:var(--radius-sm); padding:.6rem .8rem;">
                                <i class="bi bi-check-circle-fill"
                                    style="color:#047857; flex-shrink:0; margin-top:2px;"></i>
                                <div style="font-size:.73rem; color:#065f46; line-height:1.5;">
                                    Akad sudah disetujui DPS. Admin dapat mengubah status operasional
                                    menjadi <strong>Aktif</strong>, <strong>Vakum</strong>, atau <strong>Tutup</strong>.
                                </div>
                            </div>
                        @else
                            <input type="hidden" name="status_usaha" value="belum_aktif">

                            <div class="d-flex align-items-start gap-2"
                                style="background:#f8fafc; border:1px solid #e2e8f0;
                                border-radius:var(--radius-md); padding:.75rem .9rem;">
                                <span class="badge-soft tint-slate"
                                    style="font-size:.78rem; padding:.42rem .8rem; flex-shrink:0;">
                                    <i class="bi bi-clock-history me-1"></i>Belum Aktif
                                </span>
                                <div style="font-size:.76rem; color:var(--text-muted); line-height:1.5;">
                                    Status belum dapat diubah karena akad belum disetujui DPS.
                                    Setelah DPS menyetujui akad, usaha akan diaktifkan oleh sistem.
                                </div>
                            </div>
                        @endif

                        @error('status_usaha')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="col-12">
                        <label for="deskripsi" class="form-label">Deskripsi Usaha</label>
                        <textarea name="deskripsi" id="deskripsi" rows="3" class="form-control @error('deskripsi') is-invalid @enderror"
                            placeholder="Jelaskan secara singkat tentang usaha ini...">{{ old('deskripsi', $usaha->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
     SEKSI 2: Informasi Syariah & Akad
══════════════════════════════════════════════════════════ --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center gap-2">
                <div
                    style="width:34px; height:34px; border-radius:10px; flex-shrink:0;
                    background:linear-gradient(135deg,#ecfdf5,#d1fae5);
                    display:flex; align-items:center; justify-content:center;
                    font-size:.95rem; color:#047857;">
                    <i class="bi bi-patch-check-fill"></i>
                </div>
                <span>Informasi Syariah &amp; Akad</span>
                <span class="badge-soft tint-amber ms-auto" style="font-size:.72rem;">
                    <i class="bi bi-info-circle me-1"></i>Tidak mempengaruhi evaluasi 14 hari
                </span>
            </div>
            <div class="card-body">
                <div class="row g-3">

                    {{-- Jenis Akad --}}
                    <div class="col-12 col-md-6">
                        <label for="jenis_akad" class="form-label">
                            Jenis Akad Syariah <span class="text-danger">*</span>
                        </label>
                        <select name="jenis_akad" id="jenis_akad"
                            class="form-select @error('jenis_akad') is-invalid @enderror" required>
                            <option value="">-- Pilih Jenis Akad --</option>
                            @foreach (['Mudharabah', 'Musyarakah', 'Murabahah', 'Ijarah', 'Qardh', 'Lainnya'] as $akad)
                                <option value="{{ $akad }}"
                                    {{ old('jenis_akad', $usaha->jenis_akad) === $akad ? 'selected' : '' }}>
                                    {{ $akad }}
                                </option>
                            @endforeach
                        </select>
                        @error('jenis_akad')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Status Validasi Syariah Saat Ini --}}
                    <div class="col-12 col-md-6">
                        <label class="form-label">Status Validasi Syariah Saat Ini</label>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span class="badge-soft tint-{{ $usaha->status_validasi_syariah_tint }}"
                                style="font-size:.82rem; padding:.45rem 1rem;">
                                <i class="bi {{ $usaha->status_validasi_syariah_icon }} me-1"></i>
                                {{ $usaha->status_validasi_syariah_label }}
                            </span>
                        </div>
                        <div style="font-size:.73rem; color:var(--text-muted); margin-top:.45rem;">
                            <i class="bi bi-info-circle me-1"></i>
                            Upload dokumen baru akan mengubah status menjadi
                            <strong style="color:var(--warning);">"Menunggu Validasi DPS"</strong>.
                        </div>
                    </div>

                    {{-- Dokumen Akad Saat Ini --}}
                    @if ($usaha->has_file_akad)
                        <div class="col-12">
                            <label class="form-label">Dokumen Akad Saat Ini</label>
                            <div class="d-flex align-items-center gap-3"
                                style="background:var(--bg-soft); border-radius:var(--radius-md);
                                padding:.75rem 1rem;">
                                <i class="bi {{ $usaha->file_akad_icon }}"
                                    style="font-size:1.6rem; color:{{ $usaha->file_akad_extension === 'pdf' ? '#dc2626' : '#2563eb' }}; flex-shrink:0;"></i>
                                <div style="flex:1; min-width:0;">
                                    <div
                                        style="font-size:.82rem; font-weight:700; color:var(--text-dark);
                                        overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                        {{ $usaha->file_akad }}
                                    </div>
                                    <div style="font-size:.72rem; color:var(--text-muted);">
                                        File akad yang sudah tersimpan
                                    </div>
                                </div>
                                <a href="{{ $usaha->file_akad_url }}" target="_blank" class="btn-theme-light"
                                    style="padding:.35rem .85rem; font-size:.78rem; flex-shrink:0;">
                                    <i class="bi bi-eye me-1"></i> Lihat
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Upload Dokumen Akad Baru --}}
                    <div class="col-12">
                        <label for="file_akad" class="form-label">
                            {{ $usaha->has_file_akad ? 'Ganti Dokumen Akad (Opsional)' : 'Upload Dokumen Ketentuan Akad' }}
                        </label>

                        <div id="uploadArea"
                            style="border:2px dashed #c7d2fe; border-radius:var(--radius-md);
                            background:var(--primary-light); padding:1.2rem 1rem;
                            text-align:center; cursor:pointer;
                            transition:border-color .2s, background .2s;">
                            <i class="bi bi-cloud-upload"
                                style="font-size:1.5rem; color:var(--primary); display:block; margin-bottom:.35rem;"></i>
                            <div id="uploadText" style="font-size:.82rem; font-weight:600; color:var(--primary-dark);">
                                {{ $usaha->has_file_akad ? 'Klik untuk mengganti dokumen akad' : 'Klik atau seret file ke sini' }}
                            </div>
                            <div style="font-size:.72rem; color:var(--text-muted); margin-top:.2rem;">
                                PDF, DOC, DOCX &mdash; Maks. 5 MB
                            </div>
                            <input type="file" name="file_akad" id="file_akad" accept=".pdf,.doc,.docx"
                                class="@error('file_akad') is-invalid @enderror"
                                style="position:absolute; width:0; height:0; opacity:0;">
                        </div>

                        {{-- Preview file baru yang dipilih --}}
                        <div id="filePreview" class="d-none mt-2 d-flex align-items-center gap-2"
                            style="background:var(--bg-soft); border-radius:var(--radius-sm); padding:.6rem .9rem;">
                            <i id="fileIcon" class="bi bi-file-earmark-pdf-fill"
                                style="font-size:1.4rem; color:#dc2626; flex-shrink:0;"></i>
                            <div style="min-width:0; flex:1;">
                                <div id="fileName"
                                    style="font-size:.8rem; font-weight:700; color:var(--text-dark);
                                    overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                </div>
                                <div id="fileSize" style="font-size:.71rem; color:var(--text-muted);"></div>
                            </div>
                            <button type="button" id="btnClearFile"
                                style="background:none; border:none; color:var(--danger);
                                   font-size:1.1rem; cursor:pointer; flex-shrink:0;"
                                title="Batalkan pilihan file">
                                <i class="bi bi-x-circle-fill"></i>
                            </button>
                        </div>

                        @error('file_akad')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror

                        {{-- Peringatan perubahan status --}}
                        @if ($usaha->has_file_akad)
                            <div class="mt-2 d-flex align-items-start gap-2"
                                style="background:#fffbeb; border:1px solid #fde68a;
                                border-radius:var(--radius-sm); padding:.6rem .8rem;">
                                <i class="bi bi-exclamation-triangle-fill"
                                    style="color:#b45309; font-size:.9rem; flex-shrink:0; margin-top:2px;"></i>
                                <div style="font-size:.73rem; color:#92400e; line-height:1.5;">
                                    Jika Anda mengunggah dokumen baru, file akad lama akan digantikan dan
                                    status validasi syariah akan kembali menjadi
                                    <strong>"Menunggu Validasi DPS"</strong>.
                                    Status usaha akan kembali menjadi <strong>Belum Aktif</strong>,
                                    tanggal aktivasi monitoring akan dikosongkan, dan pengiriman laporan
                                    dihentikan sampai dokumen baru disetujui DPS.
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
     SEKSI 3: Lokasi & Modal
══════════════════════════════════════════════════════════ --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center gap-2">
                <div
                    style="width:34px; height:34px; border-radius:10px; flex-shrink:0;
                    background:linear-gradient(135deg,#fffbeb,#fef3c7);
                    display:flex; align-items:center; justify-content:center;
                    font-size:.95rem; color:#b45309;">
                    <i class="bi bi-geo-alt-fill"></i>
                </div>
                <span>Lokasi &amp; Modal Usaha</span>
            </div>
            <div class="card-body">
                <div class="row g-3">

                    {{-- Alamat --}}
                    <div class="col-12">
                        <label for="alamat_usaha" class="form-label">Alamat Usaha</label>
                        <textarea name="alamat_usaha" id="alamat_usaha" rows="2"
                            class="form-control @error('alamat_usaha') is-invalid @enderror" placeholder="Alamat lengkap lokasi usaha">{{ old('alamat_usaha', $usaha->alamat_usaha) }}</textarea>
                        @error('alamat_usaha')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Modal Awal --}}
                    <div class="col-12 col-md-6">
                        <label for="modal_awal" class="form-label">
                            Modal Awal (Rp) <span class="text-danger">*</span>
                        </label>
                        <div style="position:relative;">
                            <span
                                style="position:absolute; left:.9rem; top:50%; transform:translateY(-50%);
                                 font-size:.85rem; font-weight:600; color:var(--text-muted); z-index:1;">
                                Rp
                            </span>
                            <input type="number" name="modal_awal" id="modal_awal"
                                class="form-control @error('modal_awal') is-invalid @enderror"
                                style="padding-left:2.4rem;" value="{{ old('modal_awal', $usaha->modal_awal) }}"
                                min="0" step="1000" required>
                        </div>
                        @error('modal_awal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tanggal Mulai --}}
                    <div class="col-12 col-md-6">
                        <label for="tanggal_mulai" class="form-label">Tanggal Mulai Usaha</label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                            class="form-control @error('tanggal_mulai') is-invalid @enderror"
                            value="{{ old('tanggal_mulai', $usaha->tanggal_mulai?->format('Y-m-d')) }}">
                        @error('tanggal_mulai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
     SEKSI 4: Catatan
══════════════════════════════════════════════════════════ --}}
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <div
                    style="width:34px; height:34px; border-radius:10px; flex-shrink:0;
                    background:linear-gradient(135deg,#fdf2f8,#fce7f3);
                    display:flex; align-items:center; justify-content:center;
                    font-size:.95rem; color:#be185d;">
                    <i class="bi bi-sticky-fill"></i>
                </div>
                <span>Catatan Tambahan</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label for="catatan" class="form-label">Catatan</label>
                        <textarea name="catatan" id="catatan" rows="3" class="form-control @error('catatan') is-invalid @enderror"
                            placeholder="Catatan tambahan (opsional)">{{ old('catatan', $usaha->catatan) }}</textarea>
                        @error('catatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div style="font-size:.73rem; color:var(--text-muted); margin-top:.35rem;">
                            Maksimal 1000 karakter.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Tombol ───────────────────────────────────────────────── --}}
        <div class="d-flex gap-2 align-items-center">
            <button type="submit" class="btn-theme-primary" id="btnSimpan">
                <span id="btnSimpanText" class="d-flex align-items-center gap-2">
                    <i class="bi bi-check-lg"></i> Simpan Perubahan
                </span>
                <span id="btnSimpanLoading" class="d-none align-items-center gap-2">
                    <span class="spinner-theme"></span> Menyimpan...
                </span>
            </button>
            <a href="{{ route('admin.usaha.show', $usaha->id) }}" class="btn-theme-light">
                Batal
            </a>
        </div>

    </form>

    {{-- Loading Overlay --}}
    <div class="theme-loading-overlay" id="loadingOverlay">
        <div class="theme-spinner"></div>
        <span>Menyimpan perubahan...</span>
    </div>

@endsection

@push('scripts')
    <script>
        // ── Upload area ───────────────────────────────────────────
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('file_akad');
        const filePreview = document.getElementById('filePreview');
        const uploadText = document.getElementById('uploadText');

        uploadArea.addEventListener('click', () => fileInput.click());

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.style.borderColor = 'var(--primary)';
            uploadArea.style.background = '#e0e7ff';
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.style.borderColor = '#c7d2fe';
            uploadArea.style.background = 'var(--primary-light)';
        });

        uploadArea.addEventListener('drop', (e) => {
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
            if (this.files.length > 0) tampilkanPreview(this.files[0]);
        });

        function tampilkanPreview(file) {
            const ext = file.name.split('.').pop().toLowerCase();
            const size = (file.size / 1024 / 1024).toFixed(2);
            const iconEl = document.getElementById('fileIcon');

            if (ext === 'pdf') {
                iconEl.className = 'bi bi-file-earmark-pdf-fill';
                iconEl.style.color = '#dc2626';
            } else if (['doc', 'docx'].includes(ext)) {
                iconEl.className = 'bi bi-file-earmark-word-fill';
                iconEl.style.color = '#2563eb';
            } else {
                iconEl.className = 'bi bi-file-earmark-fill';
                iconEl.style.color = 'var(--text-muted)';
            }

            document.getElementById('fileName').textContent = file.name;
            document.getElementById('fileSize').textContent = size + ' MB';
            filePreview.classList.remove('d-none');
            filePreview.classList.add('d-flex');
            uploadText.textContent = 'File dipilih. Klik untuk mengganti.';
        }

        document.getElementById('btnClearFile').addEventListener('click', () => {
            fileInput.value = '';
            filePreview.classList.add('d-none');
            filePreview.classList.remove('d-flex');
            uploadText.textContent =
                '{{ $usaha->has_file_akad ? 'Klik untuk mengganti dokumen akad' : 'Klik atau seret file ke sini' }}';
        });

        // ── Loading saat submit ───────────────────────────────────
        document.getElementById('formUsaha').addEventListener('submit', function(event) {
            if (fileInput.files.length > 0) {
                const lanjut = window.confirm(
                    'Dokumen akad baru akan membuat status usaha kembali Belum Aktif dan monitoring dihentikan sampai disetujui DPS. Lanjutkan?'
                );

                if (!lanjut) {
                    event.preventDefault();
                    return;
                }
            }

            document.getElementById('btnSimpanText').classList.add('d-none');
            document.getElementById('btnSimpanLoading').classList.remove('d-none');
            document.getElementById('btnSimpanLoading').classList.add('d-flex');
            document.getElementById('btnSimpan').disabled = true;
            document.getElementById('loadingOverlay').classList.add('show');
        });
    </script>
@endpush
