@extends('layouts.app')

@section('title', 'Edit Laporan')
@section('page-title', 'Revisi & Kirim Ulang Laporan')
@section('page-subtitle', 'Laporan periode ke-' . $laporan->periode_ke)

@section('sidebar-menu')
    @include('anggota.partials._sidebar')
@endsection

@section('content')

    <div class="mb-3">
        <a href="{{ route('anggota.laporan.show', $laporan->id) }}" class="btn-theme-light"
            style="padding:.4rem .9rem; font-size:.83rem;">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Detail
        </a>
    </div>

    <div class="mb-3 p-3 d-flex align-items-start gap-2"
        style="background:linear-gradient(135deg,#fff1f2,#ffe4e6);
            border-radius:var(--radius-md); border-left:4px solid #f43f5e;">
        <i class="bi bi-exclamation-circle-fill" style="color:#be123c; font-size:1.1rem; flex-shrink:0; margin-top:2px;"></i>

        <div>
            <div style="font-size:.85rem; font-weight:700; color:#be123c; margin-bottom:.3rem;">
                Laporan Ini Ditolak oleh Pengurus
            </div>

            @if ($laporan->catatan_pengurus)
                <div style="font-size:.82rem; color:#9f1239; line-height:1.5;">
                    <strong>Alasan penolakan:</strong>
                    {{ $laporan->catatan_pengurus }}
                </div>
            @endif

            <div style="font-size:.78rem; color:#be123c; margin-top:.4rem; opacity:.8;">
                Perbaiki data keuangan, bukti, atau catatan di bawah lalu klik
                "Revisi &amp; Kirim Ulang". Pengurus akan memvalidasi ulang laporan Anda.
            </div>
        </div>
    </div>

    <form action="{{ route('anggota.laporan.update', $laporan->id) }}" method="POST" enctype="multipart/form-data"
        id="formLaporan">

        @csrf
        @method('PUT')

        {{-- ══ SEKSI 1: INFORMASI USAHA DAN PERIODE ══════════════════ --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center gap-2">
                <div
                    style="width:34px; height:34px; border-radius:10px; flex-shrink:0;
                        background:linear-gradient(135deg,#eef2ff,#e0e7ff);
                        display:flex; align-items:center; justify-content:center;
                        font-size:.95rem; color:#4338ca;">
                    <i class="bi bi-shop-window"></i>
                </div>

                <span>Usaha &amp; Periode</span>

                <span class="badge-soft tint-indigo ms-auto" style="font-size:.72rem;">
                    Periode ke-{{ $laporan->periode_ke }}
                </span>
            </div>

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-lg-5">
                        <label class="form-label">Usaha</label>

                        <div
                            style="background:var(--bg-soft);
                                border:1.5px solid var(--border-soft);
                                border-radius:var(--radius-sm);
                                padding:.75rem .9rem;">
                            <div class="d-flex align-items-center gap-2">
                                <div
                                    style="width:34px; height:34px; border-radius:10px;
                                        background:#ffffff; display:flex; align-items:center;
                                        justify-content:center; color:var(--primary);
                                        flex-shrink:0;">
                                    <i class="bi bi-shop"></i>
                                </div>

                                <div style="min-width:0;">
                                    <div
                                        style="font-size:.88rem; font-weight:800;
                                            color:var(--text-dark); overflow:hidden;
                                            text-overflow:ellipsis; white-space:nowrap;">
                                        {{ $laporan->usaha->nama_usaha }}
                                    </div>

                                    <div style="font-size:.72rem; color:var(--text-muted); margin-top:.1rem;">
                                        Identitas usaha tidak dapat diubah saat revisi.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-7">
                        <label class="form-label">Periode Monitoring 14 Hari</label>

                        <div
                            style="background:linear-gradient(135deg,#f0fdf4,#ecfdf5);
                                border:1.5px solid #a7f3d0;
                                border-radius:var(--radius-sm);
                                padding:.75rem .9rem;">
                            <div class="row g-2 align-items-center">
                                <div class="col-12 col-md-5">
                                    <div style="font-size:.7rem; color:#047857;">
                                        Tanggal Mulai
                                    </div>

                                    <div style="font-size:.86rem; font-weight:800; color:#065f46;">
                                        {{ $laporan->tanggal_mulai->translatedFormat('d F Y') }}
                                    </div>
                                </div>

                                <div class="col-12 col-md-2 text-md-center">
                                    <i class="bi bi-arrow-right" style="color:#059669;"></i>
                                </div>

                                <div class="col-12 col-md-5">
                                    <div style="font-size:.7rem; color:#047857;">
                                        Tanggal Selesai
                                    </div>

                                    <div style="font-size:.86rem; font-weight:800; color:#065f46;">
                                        {{ $laporan->tanggal_selesai->translatedFormat('d F Y') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div style="font-size:.72rem; color:var(--text-muted); margin-top:.35rem;">
                            <i class="bi bi-lock-fill me-1"></i>
                            Nomor periode dan tanggal dikunci oleh sistem.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ SEKSI 2: DATA KEUANGAN ════════════════════════════════ --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center gap-2">
                <div
                    style="width:34px; height:34px; border-radius:10px; flex-shrink:0;
                        background:linear-gradient(135deg,#fffbeb,#fef3c7);
                        display:flex; align-items:center; justify-content:center;
                        font-size:.95rem; color:#b45309;">
                    <i class="bi bi-cash-stack"></i>
                </div>

                <span>Data Keuangan</span>
            </div>

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label for="omzet" class="form-label">
                            Omzet <span class="text-danger">*</span>
                        </label>

                        <div style="position:relative;">
                            <span
                                style="position:absolute; left:.9rem; top:50%;
                                    transform:translateY(-50%); font-size:.85rem;
                                    font-weight:600; color:var(--text-muted); z-index:1;">
                                Rp
                            </span>

                            <input type="number" name="omzet" id="omzet"
                                class="form-control @error('omzet') is-invalid @enderror" style="padding-left:2.4rem;"
                                value="{{ old('omzet', $laporan->omzet) }}" min="0" max="9999999999999.99"
                                step="1000" required oninput="hitungLaba()">
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
                                style="position:absolute; left:.9rem; top:50%;
                                    transform:translateY(-50%); font-size:.85rem;
                                    font-weight:600; color:var(--text-muted); z-index:1;">
                                Rp
                            </span>

                            <input type="number" name="biaya_operasional" id="biaya_operasional"
                                class="form-control @error('biaya_operasional') is-invalid @enderror"
                                style="padding-left:2.4rem;"
                                value="{{ old('biaya_operasional', $laporan->biaya_operasional) }}" min="0"
                                max="9999999999999.99" step="1000" required oninput="hitungLaba()">
                        </div>

                        @error('biaya_operasional')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label">Laba Bersih (Otomatis)</label>

                        <div id="labaBersihDisplay"
                            style="background:var(--bg-soft);
                                border-radius:var(--radius-sm);
                                border:1.5px solid var(--border-soft);
                                padding:.68rem 1rem;
                                font-size:.95rem;
                                font-weight:800;
                                color:var(--text-dark);">
                            Rp {{ number_format($laporan->laba_bersih, 0, ',', '.') }}
                        </div>

                        <div style="font-size:.72rem; color:var(--text-muted); margin-top:.35rem;">
                            <i class="bi bi-calculator me-1"></i>
                            Otomatis: Omzet − Biaya Operasional
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ SEKSI 3: BUKTI DAN CATATAN ═══════════════════════════ --}}
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
                            {{ $laporan->has_file_bukti ? 'Ganti Bukti (Opsional)' : 'Upload Bukti Transaksi' }}
                        </label>

                        @if ($laporan->has_file_bukti)
                            <div class="d-flex align-items-center gap-2 mb-2"
                                style="background:var(--bg-soft);
                                    border-radius:var(--radius-sm);
                                    padding:.6rem .9rem;">

                                <i class="bi {{ $laporan->file_bukti_icon }}"
                                    style="font-size:1.3rem; flex-shrink:0;
                                        color:{{ in_array($laporan->file_bukti_extension, ['jpg', 'jpeg', 'png']) ? '#7c3aed' : '#dc2626' }};">
                                </i>

                                <div style="flex:1; min-width:0;">
                                    <div
                                        style="font-size:.78rem; font-weight:700;
                                            overflow:hidden; text-overflow:ellipsis;
                                            white-space:nowrap;">
                                        {{ $laporan->file_bukti }}
                                    </div>

                                    <div style="font-size:.7rem; color:var(--text-muted);">
                                        File lama tetap digunakan jika Anda tidak memilih file baru.
                                    </div>
                                </div>

                                <a href="{{ $laporan->file_bukti_url }}" target="_blank" rel="noopener noreferrer"
                                    class="btn-theme-light" style="padding:.3rem .7rem; font-size:.76rem; flex-shrink:0;">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        @endif

                        <div id="uploadArea"
                            style="border:2px dashed #c7d2fe;
                                border-radius:var(--radius-md);
                                background:var(--primary-light);
                                padding:1rem;
                                text-align:center;
                                cursor:pointer;
                                transition:.2s;">

                            <i class="bi bi-cloud-upload"
                                style="font-size:1.4rem; color:var(--primary);
                                    display:block; margin-bottom:.3rem;">
                            </i>

                            <div id="uploadText"
                                style="font-size:.8rem; font-weight:600;
                                    color:var(--primary-dark);">
                                {{ $laporan->has_file_bukti ? 'Klik untuk memilih file pengganti' : 'Klik atau seret file ke sini' }}
                            </div>

                            <div style="font-size:.7rem; color:var(--text-muted); margin-top:.2rem;">
                                PDF, JPG, JPEG, PNG &mdash; Maks. 5 MB
                            </div>

                            <input type="file" name="file_bukti" id="file_bukti" accept=".pdf,.jpg,.jpeg,.png"
                                class="@error('file_bukti') is-invalid @enderror"
                                style="position:absolute; width:0; height:0; opacity:0;">
                        </div>

                        <div id="filePreview" class="d-none mt-2 align-items-center gap-2"
                            style="background:var(--bg-soft);
                                border-radius:var(--radius-sm);
                                padding:.6rem .9rem;">

                            <i id="fileIcon" class="bi bi-file-earmark-pdf-fill"
                                style="font-size:1.4rem; color:#dc2626; flex-shrink:0;">
                            </i>

                            <div style="min-width:0; flex:1;">
                                <div id="fileName"
                                    style="font-size:.8rem; font-weight:700;
                                        overflow:hidden; text-overflow:ellipsis;
                                        white-space:nowrap;">
                                </div>

                                <div id="fileSize" style="font-size:.71rem; color:var(--text-muted);">
                                </div>
                            </div>

                            <button type="button" id="btnClearFile" aria-label="Batalkan file baru"
                                style="background:none; border:none;
                                    color:var(--danger); font-size:1.1rem;
                                    cursor:pointer;">
                                <i class="bi bi-x-circle-fill"></i>
                            </button>
                        </div>

                        @error('file_bukti')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="keterangan" class="form-label">
                            Catatan / Keterangan
                        </label>

                        <textarea name="keterangan" id="keterangan" rows="6" maxlength="1000"
                            class="form-control @error('keterangan') is-invalid @enderror"
                            placeholder="Jelaskan perbaikan laporan, kondisi usaha, kendala, atau informasi lain yang perlu diketahui pengurus...">{{ old('keterangan', $laporan->keterangan) }}</textarea>

                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn-theme-primary" id="btnSimpan">

                <span id="btnSimpanText" class="d-flex align-items-center gap-2">
                    <i class="bi bi-arrow-repeat"></i>
                    Revisi &amp; Kirim Ulang
                </span>

                <span id="btnSimpanLoading" class="d-none align-items-center gap-2">
                    <span class="spinner-theme"></span>
                    Mengirim...
                </span>
            </button>

            <a href="{{ route('anggota.laporan.show', $laporan->id) }}" class="btn-theme-light">
                Batal
            </a>
        </div>
    </form>

    <div class="theme-loading-overlay" id="loadingOverlay">
        <div class="theme-spinner"></div>
        <span>Mengirim ulang laporan...</span>
    </div>

@endsection

@push('scripts')
    <script>
        function hitungLaba() {
            const omzet = parseFloat(
                document.getElementById('omzet').value
            ) || 0;

            const biaya = parseFloat(
                document.getElementById('biaya_operasional').value
            ) || 0;

            const laba = omzet - biaya;
            const display = document.getElementById('labaBersihDisplay');

            display.textContent =
                (laba < 0 ? '-' : '') +
                'Rp ' +
                Math.abs(laba).toLocaleString('id-ID');

            display.style.color = laba >= 0 ?
                '#047857' :
                '#be123c';
        }

        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('file_bukti');
        const filePreview = document.getElementById('filePreview');
        const uploadText = document.getElementById('uploadText');

        const teksUploadAwal = @json($laporan->has_file_bukti ? 'Klik untuk memilih file pengganti' : 'Klik atau seret file ke sini');

        uploadArea.addEventListener('click', () => {
            fileInput.click();
        });

        uploadArea.addEventListener('dragover', event => {
            event.preventDefault();
            uploadArea.style.borderColor = 'var(--primary)';
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.style.borderColor = '#c7d2fe';
        });

        uploadArea.addEventListener('drop', event => {
            event.preventDefault();
            uploadArea.style.borderColor = '#c7d2fe';

            if (event.dataTransfer.files.length > 0) {
                const file = event.dataTransfer.files[0];
                const dataTransfer = new DataTransfer();

                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;

                tampilkanPreview(file);
            }
        });

        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                tampilkanPreview(this.files[0]);
            }
        });

        function tampilkanPreview(file) {
            const extension = file.name
                .split('.')
                .pop()
                .toLowerCase();

            const icon = document.getElementById('fileIcon');
            const gambar = ['jpg', 'jpeg', 'png'].includes(extension);

            icon.className = gambar ?
                'bi bi-file-earmark-image-fill' :
                'bi bi-file-earmark-pdf-fill';

            icon.style.color = gambar ?
                '#7c3aed' :
                '#dc2626';

            document.getElementById('fileName').textContent = file.name;

            document.getElementById('fileSize').textContent =
                (file.size / 1024 / 1024).toFixed(2) + ' MB';

            filePreview.classList.remove('d-none');
            filePreview.classList.add('d-flex');

            uploadText.textContent =
                'File baru dipilih. Klik untuk mengganti.';
        }

        document
            .getElementById('btnClearFile')
            .addEventListener('click', event => {
                event.stopPropagation();

                fileInput.value = '';

                filePreview.classList.add('d-none');
                filePreview.classList.remove('d-flex');

                uploadText.textContent = teksUploadAwal;
            });

        document
            .getElementById('formLaporan')
            .addEventListener('submit', function(event) {
                if (!this.checkValidity()) {
                    event.preventDefault();
                    this.reportValidity();
                    return;
                }

                document
                    .getElementById('btnSimpanText')
                    .classList
                    .add('d-none');

                document
                    .getElementById('btnSimpanLoading')
                    .classList
                    .remove('d-none');

                document
                    .getElementById('btnSimpanLoading')
                    .classList
                    .add('d-flex');

                document
                    .getElementById('btnSimpan')
                    .disabled = true;

                document
                    .getElementById('loadingOverlay')
                    .classList
                    .add('show');
            });

        hitungLaba();
    </script>
@endpush
