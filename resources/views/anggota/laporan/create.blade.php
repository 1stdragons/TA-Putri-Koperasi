@extends('layouts.app')

@section('title', 'Input Laporan Monitoring')
@section('page-title', 'Input Laporan Monitoring')
@section('page-subtitle', 'Catat perkembangan usaha Anda untuk periode 14 hari ini')

@section('sidebar-menu')
    @include('anggota.partials._sidebar')
@endsection

@section('content')

    <div class="mb-3">
        <a href="{{ route('anggota.laporan.index') }}" class="btn-theme-light" style="padding:.4rem .9rem; font-size:.83rem;">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat
        </a>
    </div>

    <form action="{{ route('anggota.laporan.store') }}" method="POST" enctype="multipart/form-data" id="formLaporan">
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
                    <div class="col-12">
                        <label for="usaha_id" class="form-label">
                            Usaha Anda <span class="text-danger">*</span>
                        </label>
                        <select name="usaha_id" id="usaha_id" class="form-select @error('usaha_id') is-invalid @enderror"
                            required>
                            <option value="">-- Pilih Usaha --</option>
                            @foreach ($usahaList as $usaha)
                                <option value="{{ $usaha->id }}"
                                    {{ old('usaha_id', $selectedUsahaId) == $usaha->id ? 'selected' : '' }}>
                                    {{ $usaha->nama_usaha }} ({{ $usaha->jenis_usaha_label }})
                                </option>
                            @endforeach
                        </select>
                        @error('usaha_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ SEKSI 2: Periode Otomatis ════════════════════════════ --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center gap-2">
                <div
                    style="width:34px; height:34px; border-radius:10px; flex-shrink:0;
                    background:linear-gradient(135deg,#ecfdf5,#d1fae5);
                    display:flex; align-items:center; justify-content:center;
                    font-size:.95rem; color:#047857;">
                    <i class="bi bi-calendar-range-fill"></i>
                </div>
                <span>Periode Monitoring 14 Hari</span>
                <span class="badge-soft tint-emerald ms-auto" style="font-size:.72rem;">
                    <i class="bi bi-magic me-1"></i>Otomatis dari sistem
                </span>
            </div>

            <div class="card-body">
                <div id="periodeKosong"
                    style="background:var(--bg-soft); border:1.5px dashed var(--border-soft);
                    border-radius:var(--radius-md); padding:1.15rem; text-align:center;">
                    <i class="bi bi-calendar2-week"
                        style="font-size:1.7rem; color:var(--text-muted); display:block; margin-bottom:.45rem;"></i>
                    <div style="font-size:.86rem; font-weight:700; color:var(--text-dark);">
                        Pilih usaha terlebih dahulu
                    </div>
                    <div style="font-size:.76rem; color:var(--text-muted); margin-top:.25rem;">
                        Sistem akan menghitung periode berdasarkan tanggal aktivasi monitoring. Anggota tidak dapat mengubah
                        tanggal periode.
                    </div>
                </div>

                <div id="periodeOtomatis" class="d-none">
                    <div
                        style="background:linear-gradient(135deg,#f0fdf4,#ecfdf5);
                        border:1.5px solid #a7f3d0; border-radius:var(--radius-md);
                        padding:1.15rem;">
                        <div class="d-flex flex-column flex-md-row align-items-md-center gap-3">
                            <div
                                style="width:48px; height:48px; border-radius:14px; flex-shrink:0;
                                background:#ffffff; display:flex; align-items:center;
                                justify-content:center; color:#047857; font-size:1.25rem;
                                box-shadow:0 4px 12px rgba(4,120,87,.10);">
                                <i class="bi bi-calendar-check-fill"></i>
                            </div>

                            <div style="min-width:0; flex:1;">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                    <span id="periodeKe" class="badge-soft tint-emerald" style="font-size:.73rem;">
                                        Periode ke-1
                                    </span>
                                    <span style="font-size:.73rem; color:var(--text-muted);">
                                        Durasi 14 hari
                                    </span>
                                </div>

                                <div class="row g-2">
                                    <div class="col-12 col-md-5">
                                        <div style="font-size:.7rem; color:var(--text-muted);">Tanggal Mulai</div>
                                        <div id="periodeTanggalMulai"
                                            style="font-size:.9rem; font-weight:800; color:var(--text-dark);">-</div>
                                    </div>
                                    <div class="col-12 col-md-2 d-flex align-items-center justify-content-md-center">
                                        <i class="bi bi-arrow-right" style="color:#059669; font-size:1rem;"></i>
                                    </div>
                                    <div class="col-12 col-md-5">
                                        <div style="font-size:.7rem; color:var(--text-muted);">Tanggal Selesai</div>
                                        <div id="periodeTanggalSelesai"
                                            style="font-size:.9rem; font-weight:800; color:var(--text-dark);">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="periodePesan" class="mt-3 d-flex align-items-start gap-2"
                            style="background:#ffffff; border-radius:var(--radius-sm);
                            padding:.7rem .85rem; font-size:.77rem; color:#065f46;">
                            <i class="bi bi-info-circle-fill mt-1" style="flex-shrink:0;"></i>
                            <span>Periode dihitung otomatis dari tanggal aktivasi monitoring yang ditetapkan setelah akad
                                disetujui DPS.</span>
                        </div>
                    </div>
                </div>

                <div id="periodeTidakTersedia" class="d-none"
                    style="background:#fff7ed; border:1.5px solid #fed7aa;
                    border-radius:var(--radius-md); padding:1rem;">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-exclamation-triangle-fill"
                            style="color:#c2410c; flex-shrink:0; margin-top:.1rem;"></i>
                        <div>
                            <div style="font-size:.83rem; font-weight:800; color:#9a3412;">
                                Periode baru belum tersedia
                            </div>
                            <div id="periodeTidakTersediaPesan" style="font-size:.76rem; color:#c2410c; margin-top:.2rem;">
                            </div>
                        </div>
                    </div>
                </div>

                @error('periode')
                    <div class="text-danger mt-2" style="font-size:.78rem;">{{ $message }}</div>
                @enderror
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
                <span>Data Keuangan Periode Ini</span>
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
                                value="{{ old('omzet', 0) }}" min="0" step="1000" required oninput="hitungLaba()">
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
                                style="padding-left:2.4rem;" value="{{ old('biaya_operasional', 0) }}" min="0"
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
                            Rp 0
                        </div>
                        <div style="font-size:.73rem; color:var(--text-muted); margin-top:.35rem;">
                            <i class="bi bi-calculator me-1"></i>Otomatis: Omzet − Biaya
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
                <span>Bukti Transaksi &amp; Catatan</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="file_bukti" class="form-label">Upload Bukti Transaksi</label>
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
                                    style="font-size:.8rem; font-weight:700; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
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
                        <div class="mt-2" style="font-size:.74rem; color:var(--text-muted);">
                            <i class="bi bi-info-circle me-1"></i>
                            Lampirkan struk, nota, atau bukti transaksi lainnya sebagai pendukung laporan.
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="keterangan" class="form-label">Catatan / Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="6"
                            class="form-control @error('keterangan') is-invalid @enderror"
                            placeholder="Ceritakan kondisi usaha Anda pada periode ini: kendala, pencapaian, atau hal lain yang perlu diketahui pengurus...">{{ old('keterangan') }}</textarea>
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
                    <i class="bi bi-send-fill"></i> Kirim Laporan
                </span>
                <span id="btnSimpanLoading" class="d-none align-items-center gap-2">
                    <span class="spinner-theme"></span> Mengirim...
                </span>
            </button>
            <a href="{{ route('anggota.laporan.index') }}" class="btn-theme-light">Batal</a>
        </div>

    </form>

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

        const periodeUsaha = @json($periodeUsaha);

        function formatTanggalIndonesia(tanggal) {
            if (!tanggal) return '-';
            const bagian = tanggal.split('-').map(Number);
            const date = new Date(bagian[0], bagian[1] - 1, bagian[2]);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            });
        }

        function tampilkanPeriodeOtomatis() {
            const usahaId = document.getElementById('usaha_id').value;
            const periodeKosong = document.getElementById('periodeKosong');
            const periodeOtomatis = document.getElementById('periodeOtomatis');
            const periodeTidakTersedia = document.getElementById('periodeTidakTersedia');
            const btnSimpan = document.getElementById('btnSimpan');

            periodeKosong.classList.add('d-none');
            periodeOtomatis.classList.add('d-none');
            periodeTidakTersedia.classList.add('d-none');

            if (!usahaId || !periodeUsaha[usahaId]) {
                periodeKosong.classList.remove('d-none');
                btnSimpan.disabled = false;
                return;
            }

            const periode = periodeUsaha[usahaId];

            if (!periode.boleh_dibuat) {
                document.getElementById('periodeTidakTersediaPesan').textContent = periode.pesan;
                periodeTidakTersedia.classList.remove('d-none');
                btnSimpan.disabled = true;
                return;
            }

            document.getElementById('periodeKe').textContent = 'Periode ke-' + periode.periode_ke;
            document.getElementById('periodeTanggalMulai').textContent = formatTanggalIndonesia(periode.tanggal_mulai);
            document.getElementById('periodeTanggalSelesai').textContent = formatTanggalIndonesia(periode.tanggal_selesai);
            document.getElementById('periodePesan').querySelector('span').textContent = periode.pesan;
            periodeOtomatis.classList.remove('d-none');
            btnSimpan.disabled = false;
        }

        document.getElementById('usaha_id').addEventListener('change', tampilkanPeriodeOtomatis);

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
            document.getElementById('uploadText').textContent = 'Klik atau seret file ke sini';
        });

        document.getElementById('formLaporan').addEventListener('submit', function(event) {
            if (!this.checkValidity()) {
                event.preventDefault();
                this.reportValidity();
                return;
            }

            document.getElementById('btnSimpanText').classList.add('d-none');
            document.getElementById('btnSimpanLoading').classList.remove('d-none');
            document.getElementById('btnSimpanLoading').classList.add('d-flex');
            document.getElementById('btnSimpan').disabled = true;
        });

        hitungLaba();

        tampilkanPeriodeOtomatis();
    </script>
@endpush
