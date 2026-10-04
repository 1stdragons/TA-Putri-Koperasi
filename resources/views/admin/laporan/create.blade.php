@extends('layouts.app')

@section('title', 'Tambah Laporan')
@section('page-title', 'Tambah Laporan Usaha')
@section('page-subtitle', 'Input laporan perkembangan usaha anggota')

@section('sidebar-menu')
    @include('admin.partials._sidebar')
@endsection

@section('content')

    <div class="mb-3">
        <a href="{{ route('admin.laporan.index') }}" class="btn-theme-light" style="padding:.4rem .9rem; font-size:.83rem;">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Laporan
        </a>
    </div>

    <form action="{{ route('admin.laporan.store') }}" method="POST" enctype="multipart/form-data" id="formLaporan">
        @csrf

        {{-- ══ SEKSI 1: Pilih Usaha ════════════════════════════════ --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center gap-2">
                <div
                    style="width:34px; height:34px; border-radius:10px; flex-shrink:0;
                    background:linear-gradient(135deg,#eef2ff,#e0e7ff);
                    display:flex; align-items:center; justify-content:center;
                    font-size:.95rem; color:#4338ca;">
                    <i class="bi bi-shop-window"></i>
                </div>
                <span>Pilih Usaha</span>
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
                                <option value="{{ $usaha->id }}" {{ old('usaha_id') == $usaha->id ? 'selected' : '' }}>
                                    {{ $usaha->nama_usaha }}
                                    — {{ $usaha->anggota->akun->nama }}
                                    ({{ $usaha->anggota->nomor_anggota }})
                                </option>
                            @endforeach
                        </select>
                        @error('usaha_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if ($usahaList->isEmpty())
                            <div class="mt-2 d-flex align-items-center gap-2"
                                style="font-size:.78rem; color:var(--warning);">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                Belum ada usaha aktif. Tambahkan data usaha terlebih dahulu.
                            </div>
                        @endif
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="status_laporan" class="form-label">
                            Status Laporan <span class="text-danger">*</span>
                        </label>
                        <select name="status_laporan" id="status_laporan"
                            class="form-select @error('status_laporan') is-invalid @enderror" required>
                            <option value="draft" {{ old('status_laporan', 'draft') === 'draft' ? 'selected' : '' }}>Draft
                            </option>
                            <option value="dikirim" {{ old('status_laporan') === 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                            <option value="divalidasi" {{ old('status_laporan') === 'divalidasi' ? 'selected' : '' }}>
                                Divalidasi</option>
                            <option value="ditolak" {{ old('status_laporan') === 'ditolak' ? 'selected' : '' }}>Ditolak
                            </option>
                        </select>
                        @error('status_laporan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ SEKSI 2: Periode 14 Hari ═══════════════════════════ --}}
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
                <span class="badge-soft tint-emerald ms-auto" style="font-size:.72rem;">
                    <i class="bi bi-clock me-1"></i>Setiap 14 hari sekali
                </span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="tanggal_mulai" class="form-label">
                            Tanggal Mulai Periode <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                            class="form-control @error('tanggal_mulai') is-invalid @enderror"
                            value="{{ old('tanggal_mulai') }}" required>
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
                            value="{{ old('tanggal_selesai') }}" required>
                        @error('tanggal_selesai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <div id="periodeInfo" class="d-none d-flex align-items-center gap-2"
                            style="background:var(--primary-light); border-radius:var(--radius-sm);
                            padding:.65rem .9rem; font-size:.8rem; color:var(--primary-dark);">
                            <i class="bi bi-info-circle-fill" style="flex-shrink:0;"></i>
                            <span id="periodeInfoText"></span>
                        </div>
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

                    {{-- Omzet --}}
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
                                value="{{ old('omzet', 0) }}" min="0" step="1000" required
                                oninput="hitungLaba()">
                        </div>
                        @error('omzet')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Biaya Operasional --}}
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
                                style="padding-left:2.4rem;" value="{{ old('biaya_operasional', 0) }}" min="0"
                                step="1000" required oninput="hitungLaba()">
                        </div>
                        @error('biaya_operasional')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Laba Bersih (read-only, dihitung otomatis) --}}
                    <div class="col-12 col-md-4">
                        <label class="form-label">Laba Bersih (Otomatis)</label>
                        <div id="labaBersihDisplay"
                            style="background:var(--bg-soft); border-radius:var(--radius-sm);
                            border:1.5px solid var(--border-soft); padding:.68rem 1rem;
                            font-size:.95rem; font-weight:800; color:var(--text-dark);">
                            Rp 0
                        </div>
                        <div style="font-size:.73rem; color:var(--text-muted); margin-top:.35rem;">
                            <i class="bi bi-calculator me-1"></i>Dihitung otomatis: Omzet − Biaya Operasional
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

                    {{-- Upload Bukti --}}
                    <div class="col-12 col-md-6">
                        <label for="file_bukti" class="form-label">Upload Bukti Laporan</label>

                        <div id="uploadArea"
                            style="border:2px dashed #c7d2fe; border-radius:var(--radius-md);
                            background:var(--primary-light); padding:1.2rem 1rem;
                            text-align:center; cursor:pointer; transition:.2s;">
                            <i class="bi bi-cloud-upload"
                                style="font-size:1.5rem; color:var(--primary); display:block; margin-bottom:.35rem;"></i>
                            <div id="uploadText" style="font-size:.82rem; font-weight:600; color:var(--primary-dark);">
                                Klik atau seret file ke sini
                            </div>
                            <div style="font-size:.72rem; color:var(--text-muted); margin-top:.2rem;">
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

                    {{-- Catatan --}}
                    <div class="col-12 col-md-6">
                        <label for="keterangan" class="form-label">Catatan / Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="5"
                            class="form-control @error('keterangan') is-invalid @enderror"
                            placeholder="Catatan tambahan dari anggota mengenai periode ini...">{{ old('keterangan') }}</textarea>
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
                    <i class="bi bi-check-lg"></i> Simpan Laporan
                </span>
                <span id="btnSimpanLoading" class="d-none align-items-center gap-2">
                    <span class="spinner-theme"></span> Menyimpan...
                </span>
            </button>
            <a href="{{ route('admin.laporan.index') }}" class="btn-theme-light">Batal</a>
        </div>

    </form>

    <div class="theme-loading-overlay" id="loadingOverlay">
        <div class="theme-spinner"></div>
        <span>Menyimpan laporan...</span>
    </div>

@endsection

@push('scripts')
    <script>
        // ── Hitung laba bersih otomatis ───────────────────────────
        function hitungLaba() {
            const omzet = parseFloat(document.getElementById('omzet').value) || 0;
            const biaya = parseFloat(document.getElementById('biaya_operasional').value) || 0;
            const laba = omzet - biaya;

            const display = document.getElementById('labaBersihDisplay');
            const formatted = 'Rp ' + Math.abs(laba).toLocaleString('id-ID');

            display.textContent = (laba < 0 ? '-' : '') + formatted;
            display.style.color = laba >= 0 ? '#047857' : '#be123c';
        }

        // ── Auto-isi tanggal selesai (+13 hari) ─────────────────
        document.getElementById('tanggal_mulai').addEventListener('change', function() {
            if (!this.value) return;

            const mulai = new Date(this.value);
            const selesai = new Date(mulai);
            selesai.setDate(mulai.getDate() + 13);

            const selesaiStr = selesai.toISOString().split('T')[0];
            document.getElementById('tanggal_selesai').value = selesaiStr;

            const opts = {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            };
            document.getElementById('periodeInfoText').textContent =
                'Periode: ' + mulai.toLocaleDateString('id-ID', opts) +
                ' s.d. ' + selesai.toLocaleDateString('id-ID', opts) +
                ' (14 hari)';
            document.getElementById('periodeInfo').classList.remove('d-none');
        });

        // ── Upload area ───────────────────────────────────────────
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('file_bukti');
        const filePreview = document.getElementById('filePreview');

        uploadArea.addEventListener('click', () => fileInput.click());

        uploadArea.addEventListener('dragover', e => {
            e.preventDefault();
            uploadArea.style.borderColor = 'var(--primary)';
            uploadArea.style.background = '#e0e7ff';
        });
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.style.borderColor = '#c7d2fe';
            uploadArea.style.background = 'var(--primary-light)';
        });
        uploadArea.addEventListener('drop', e => {
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
            const icon = document.getElementById('fileIcon');

            if (ext === 'pdf') {
                icon.className = 'bi bi-file-earmark-pdf-fill';
                icon.style.color = '#dc2626';
            } else if (['jpg', 'jpeg', 'png'].includes(ext)) {
                icon.className = 'bi bi-file-earmark-image-fill';
                icon.style.color = '#7c3aed';
            }

            document.getElementById('fileName').textContent = file.name;
            document.getElementById('fileSize').textContent = size + ' MB';
            filePreview.classList.remove('d-none');
            filePreview.classList.add('d-flex');
            document.getElementById('uploadText').textContent = 'File dipilih. Klik untuk mengganti.';
        }

        document.getElementById('btnClearFile').addEventListener('click', () => {
            fileInput.value = '';
            filePreview.classList.add('d-none');
            filePreview.classList.remove('d-flex');
            document.getElementById('uploadText').textContent = 'Klik atau seret file ke sini';
        });

        // ── Loading submit ────────────────────────────────────────
        document.getElementById('formLaporan').addEventListener('submit', function() {
            document.getElementById('btnSimpanText').classList.add('d-none');
            document.getElementById('btnSimpanLoading').classList.remove('d-none');
            document.getElementById('btnSimpanLoading').classList.add('d-flex');
            document.getElementById('btnSimpan').disabled = true;
            document.getElementById('loadingOverlay').classList.add('show');
        });

        // Init laba bersih
        hitungLaba();
    </script>
@endpush
