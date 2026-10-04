@extends('layouts.app')

@section('title', 'Edit Laporan')
@section('page-title', 'Edit Laporan Usaha')
@section('page-subtitle', 'Perbarui laporan periode ke-' . $laporan->periode_ke . ' — ' . $laporan->usaha->nama_usaha)

@section('sidebar-menu')
    @include('admin.partials._sidebar')
@endsection

@section('content')

    <div class="mb-3">
        <a href="{{ route('admin.laporan.show', $laporan->id) }}" class="btn-theme-light"
            style="padding:.4rem .9rem; font-size:.83rem;">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Detail Laporan
        </a>
    </div>

    <form action="{{ route('admin.laporan.update', $laporan->id) }}" method="POST" enctype="multipart/form-data"
        id="formLaporan">
        @csrf
        @method('PUT')

        {{-- ══ SEKSI 1: Pilih Usaha & Status ══════════════════════ --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center gap-2">
                <div
                    style="width:34px; height:34px; border-radius:10px; flex-shrink:0;
                    background:linear-gradient(135deg,#eef2ff,#e0e7ff);
                    display:flex; align-items:center; justify-content:center;
                    font-size:.95rem; color:#4338ca;">
                    <i class="bi bi-shop-window"></i>
                </div>
                <span>Usaha &amp; Status Laporan</span>
                <span class="badge-soft tint-indigo ms-auto" style="font-size:.72rem;">
                    Periode ke-{{ $laporan->periode_ke }}
                </span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-8">
                        <label for="usaha_id" class="form-label">
                            Usaha Anggota <span class="text-danger">*</span>
                        </label>
                        <select name="usaha_id" id="usaha_id" class="form-select @error('usaha_id') is-invalid @enderror"
                            required>
                            <option value="">-- Pilih Usaha --</option>
                            @foreach ($usahaList as $usaha)
                                <option value="{{ $usaha->id }}"
                                    {{ old('usaha_id', $laporan->usaha_id) == $usaha->id ? 'selected' : '' }}>
                                    {{ $usaha->nama_usaha }}
                                    — {{ $usaha->anggota->akun->nama }}
                                    ({{ $usaha->anggota->nomor_anggota }})
                                </option>
                            @endforeach
                        </select>
                        @error('usaha_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="status_laporan" class="form-label">
                            Status Laporan <span class="text-danger">*</span>
                        </label>
                        <select name="status_laporan" id="status_laporan"
                            class="form-select @error('status_laporan') is-invalid @enderror" required>
                            <option value="draft"
                                {{ old('status_laporan', $laporan->status_laporan) === 'draft' ? 'selected' : '' }}>Draft
                            </option>
                            <option value="dikirim"
                                {{ old('status_laporan', $laporan->status_laporan) === 'dikirim' ? 'selected' : '' }}>Dikirim
                            </option>
                            <option value="divalidasi"
                                {{ old('status_laporan', $laporan->status_laporan) === 'divalidasi' ? 'selected' : '' }}>
                                Divalidasi</option>
                            <option value="ditolak"
                                {{ old('status_laporan', $laporan->status_laporan) === 'ditolak' ? 'selected' : '' }}>Ditolak
                            </option>
                        </select>
                        @error('status_laporan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Catatan Pengurus (khusus edit) --}}
                    <div class="col-12">
                        <label for="catatan_pengurus" class="form-label">Catatan Pengurus</label>
                        <textarea name="catatan_pengurus" id="catatan_pengurus" rows="2"
                            class="form-control @error('catatan_pengurus') is-invalid @enderror"
                            placeholder="Catatan dari pengurus setelah validasi (opsional)">{{ old('catatan_pengurus', $laporan->catatan_pengurus) }}</textarea>
                        @error('catatan_pengurus')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ SEKSI 2: Periode ════════════════════════════════════ --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center gap-2">
                <div
                    style="width:34px; height:34px; border-radius:10px; flex-shrink:0;
                    background:linear-gradient(135deg,#ecfdf5,#d1fae5);
                    display:flex; align-items:center; justify-content:center;
                    font-size:.95rem; color:#047857;">
                    <i class="bi bi-calendar-range-fill"></i>
                </div>
                <span>Periode Pelaporan 14 Hari</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="tanggal_mulai" class="form-label">
                            Tanggal Mulai Periode <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                            class="form-control @error('tanggal_mulai') is-invalid @enderror"
                            value="{{ old('tanggal_mulai', $laporan->tanggal_mulai->format('Y-m-d')) }}" required>
                        @error('tanggal_mulai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="tanggal_selesai" class="form-label">
                            Tanggal Selesai Periode <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                            class="form-control @error('tanggal_selesai') is-invalid @enderror"
                            value="{{ old('tanggal_selesai', $laporan->tanggal_selesai->format('Y-m-d')) }}" required>
                        @error('tanggal_selesai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ SEKSI 3: Data Keuangan ══════════════════════════════ --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center gap-2">
                <div
                    style="width:34px; height:34px; border-radius:10px; flex-shrink:0;
                    background:linear-gradient(135deg,#fffbeb,#fef3c7);
                    display:flex; align-items:center; justify-content:center;
                    font-size:.95rem; color:#b45309;">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <span>Data Keuangan Periode</span>
            </div>
            <div class="card-body">
                <div class="row g-3">

                    <div class="col-12 col-md-4">
                        <label for="omzet" class="form-label">
                            Omzet (Pendapatan Kotor) <span class="text-danger">*</span>
                        </label>
                        <div style="position:relative;">
                            <span
                                style="position:absolute; left:.9rem; top:50%; transform:translateY(-50%);
                                 font-size:.85rem; font-weight:600; color:var(--text-muted); z-index:1;">Rp</span>
                            <input type="number" name="omzet" id="omzet"
                                class="form-control @error('omzet') is-invalid @enderror" style="padding-left:2.4rem;"
                                value="{{ old('omzet', $laporan->omzet) }}" min="0" step="1000" required
                                oninput="hitungLaba()">
                        </div>
                        @error('omzet')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="biaya_operasional" class="form-label">
                            Biaya Operasional <span class="text-danger">*</span>
                        </label>
                        <div style="position:relative;">
                            <span
                                style="position:absolute; left:.9rem; top:50%; transform:translateY(-50%);
                                 font-size:.85rem; font-weight:600; color:var(--text-muted); z-index:1;">Rp</span>
                            <input type="number" name="biaya_operasional" id="biaya_operasional"
                                class="form-control @error('biaya_operasional') is-invalid @enderror"
                                style="padding-left:2.4rem;"
                                value="{{ old('biaya_operasional', $laporan->biaya_operasional) }}" min="0"
                                step="1000" required oninput="hitungLaba()">
                        </div>
                        @error('biaya_operasional')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label">Laba Bersih (Otomatis)</label>
                        <div id="labaBersihDisplay"
                            style="background:var(--bg-soft); border-radius:var(--radius-sm);
                            border:1.5px solid var(--border-soft); padding:.68rem 1rem;
                            font-size:.95rem; font-weight:800; color:var(--text-dark);">
                            Rp {{ number_format($laporan->laba_bersih, 0, ',', '.') }}
                        </div>
                        <div style="font-size:.73rem; color:var(--text-muted); margin-top:.35rem;">
                            <i class="bi bi-calculator me-1"></i>Otomatis: Omzet − Biaya Operasional
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ SEKSI 4: Bukti & Catatan ════════════════════════════ --}}
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <div
                    style="width:34px; height:34px; border-radius:10px; flex-shrink:0;
                    background:linear-gradient(135deg,#fdf2f8,#fce7f3);
                    display:flex; align-items:center; justify-content:center;
                    font-size:.95rem; color:#be185d;">
                    <i class="bi bi-paperclip"></i>
                </div>
                <span>Bukti &amp; Catatan</span>
            </div>
            <div class="card-body">
                <div class="row g-3">

                    <div class="col-12 col-md-6">
                        <label for="file_bukti" class="form-label">
                            {{ $laporan->has_file_bukti ? 'Ganti Bukti Laporan (Opsional)' : 'Upload Bukti Laporan' }}
                        </label>

                        {{-- File saat ini --}}
                        @if ($laporan->has_file_bukti)
                            <div class="d-flex align-items-center gap-2 mb-2"
                                style="background:var(--bg-soft); border-radius:var(--radius-sm); padding:.6rem .9rem;">
                                <i class="bi {{ $laporan->file_bukti_icon }}"
                                    style="font-size:1.3rem; flex-shrink:0;
                                  color:{{ in_array($laporan->file_bukti_extension, ['jpg', 'jpeg', 'png']) ? '#7c3aed' : '#dc2626' }};"></i>
                                <div style="flex:1; min-width:0;">
                                    <div
                                        style="font-size:.8rem; font-weight:700; overflow:hidden;
                                        text-overflow:ellipsis; white-space:nowrap;">
                                        {{ $laporan->file_bukti }}
                                    </div>
                                    <div style="font-size:.71rem; color:var(--text-muted);">File saat ini</div>
                                </div>
                                <a href="{{ $laporan->file_bukti_url }}" target="_blank" class="btn-theme-light"
                                    style="padding:.3rem .7rem; font-size:.76rem; flex-shrink:0;">
                                    <i class="bi bi-eye me-1"></i>Lihat
                                </a>
                            </div>
                        @endif

                        <div id="uploadArea"
                            style="border:2px dashed #c7d2fe; border-radius:var(--radius-md);
                            background:var(--primary-light); padding:1rem;
                            text-align:center; cursor:pointer; transition:.2s;">
                            <i class="bi bi-cloud-upload"
                                style="font-size:1.4rem; color:var(--primary); display:block; margin-bottom:.3rem;"></i>
                            <div id="uploadText" style="font-size:.8rem; font-weight:600; color:var(--primary-dark);">
                                {{ $laporan->has_file_bukti ? 'Klik untuk mengganti' : 'Klik atau seret file' }}
                            </div>
                            <div style="font-size:.7rem; color:var(--text-muted); margin-top:.2rem;">
                                PDF, JPG, JPEG, PNG &mdash; Maks. 5 MB
                            </div>
                            <input type="file" name="file_bukti" id="file_bukti" accept=".pdf,.jpg,.jpeg,.png"
                                class="@error('file_bukti') is-invalid @enderror"
                                style="position:absolute; width:0; height:0; opacity:0;">
                        </div>

                        <div id="filePreview" class="d-none mt-2 d-flex align-items-center gap-2"
                            style="background:var(--bg-soft); border-radius:var(--radius-sm); padding:.6rem .9rem;">
                            <i id="fileIcon" class="bi bi-file-earmark-pdf-fill"
                                style="font-size:1.4rem; color:#dc2626; flex-shrink:0;"></i>
                            <div style="min-width:0; flex:1;">
                                <div id="fileName"
                                    style="font-size:.8rem; font-weight:700;
                                                  overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                </div>
                                <div id="fileSize" style="font-size:.71rem; color:var(--text-muted);"></div>
                            </div>
                            <button type="button" id="btnClearFile"
                                style="background:none; border:none; color:var(--danger); font-size:1.1rem; cursor:pointer;">
                                <i class="bi bi-x-circle-fill"></i>
                            </button>
                        </div>

                        @error('file_bukti')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="keterangan" class="form-label">Catatan / Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="5"
                            class="form-control @error('keterangan') is-invalid @enderror" placeholder="Catatan tambahan...">{{ old('keterangan', $laporan->keterangan) }}</textarea>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
            <a href="{{ route('admin.laporan.show', $laporan->id) }}" class="btn-theme-light">Batal</a>
        </div>

    </form>

    <div class="theme-loading-overlay" id="loadingOverlay">
        <div class="theme-spinner"></div>
        <span>Menyimpan perubahan...</span>
    </div>

@endsection

@push('scripts')
    <script>
        function hitungLaba() {
            const omzet = parseFloat(document.getElementById('omzet').value) || 0;
            const biaya = parseFloat(document.getElementById('biaya_operasional').value) || 0;
            const laba = omzet - biaya;
            const display = document.getElementById('labaBersihDisplay');
            display.textContent = (laba < 0 ? '-' : '') + 'Rp ' + Math.abs(laba).toLocaleString('id-ID');
            display.style.color = laba >= 0 ? '#047857' : '#be123c';
        }

        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('file_bukti');
        const filePreview = document.getElementById('filePreview');

        uploadArea.addEventListener('click', () => fileInput.click());
        uploadArea.addEventListener('dragover', e => {
            e.preventDefault();
            uploadArea.style.borderColor = 'var(--primary)';
        });
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.style.borderColor = '#c7d2fe';
        });
        uploadArea.addEventListener('drop', e => {
            e.preventDefault();
            uploadArea.style.borderColor = '#c7d2fe';
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
            const icon = document.getElementById('fileIcon');
            icon.className = ['jpg', 'jpeg', 'png'].includes(ext) ? 'bi bi-file-earmark-image-fill' :
                'bi bi-file-earmark-pdf-fill';
            icon.style.color = ['jpg', 'jpeg', 'png'].includes(ext) ? '#7c3aed' : '#dc2626';
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('fileSize').textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
            filePreview.classList.remove('d-none');
            filePreview.classList.add('d-flex');
            document.getElementById('uploadText').textContent = 'File dipilih. Klik untuk mengganti.';
        }

        document.getElementById('btnClearFile').addEventListener('click', () => {
            fileInput.value = '';
            filePreview.classList.add('d-none');
            filePreview.classList.remove('d-flex');
            document.getElementById('uploadText').textContent =
                '{{ $laporan->has_file_bukti ? 'Klik untuk mengganti' : 'Klik atau seret file' }}';
        });

        document.getElementById('formLaporan').addEventListener('submit', function() {
            document.getElementById('btnSimpanText').classList.add('d-none');
            document.getElementById('btnSimpanLoading').classList.remove('d-none');
            document.getElementById('btnSimpanLoading').classList.add('d-flex');
            document.getElementById('btnSimpan').disabled = true;
            document.getElementById('loadingOverlay').classList.add('show');
        });

        hitungLaba();
    </script>
@endpush
