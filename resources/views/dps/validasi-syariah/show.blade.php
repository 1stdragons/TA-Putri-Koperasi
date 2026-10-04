@extends('layouts.app')

@section('title', 'Detail Validasi Syariah')
@section('page-title', 'Detail Validasi Syariah')
@section('page-subtitle', $usaha->nama_usaha . ' — ' . $usaha->anggota->akun->nama)

@section('sidebar-menu')
    @include('dps.partials._sidebar')
@endsection

@section('content')

    {{-- ── Tombol Navigasi ─────────────────────────────────────── --}}
    <div class="mb-3">
        <a href="{{ route('dps.validasi-syariah.index') }}" class="btn-theme-light"
            style="padding:.4rem .9rem; font-size:.83rem;">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="row g-3">

        {{-- ═══════════════════════════════════════════════════════════
     KOLOM KIRI — Status + Form Validasi
═══════════════════════════════════════════════════════════ --}}
        <div class="col-12 col-lg-4">

            {{-- Header Usaha --}}
            <div class="card mb-3" style="overflow:hidden;">
                <div
                    style="background:linear-gradient(120deg, #047857 0%, #10b981 60%, #059669 130%);
                    padding:1.6rem; text-align:center; position:relative; overflow:hidden;">
                    <div
                        style="position:absolute; width:100px; height:100px; background:rgba(255,255,255,.1);
                        border-radius:50%; top:-40px; right:-30px;">
                    </div>
                    <div
                        style="width:62px; height:62px; border-radius:16px; background:rgba(255,255,255,.18);
                        border:2px solid rgba(255,255,255,.4);
                        display:flex; align-items:center; justify-content:center;
                        font-size:1.7rem; color:#fff; margin:0 auto .7rem; position:relative; z-index:1;">
                        <i class="bi bi-shop-window"></i>
                    </div>
                    <div style="font-weight:800; color:#fff; font-size:1rem; position:relative; z-index:1;">
                        {{ $usaha->nama_usaha }}
                    </div>
                    <div
                        style="font-size:.78rem; color:rgba(255,255,255,.85); margin:.2rem 0 .65rem; position:relative; z-index:1;">
                        {{ $usaha->anggota->akun->nama }} &bull; {{ $usaha->anggota->nomor_anggota }}
                    </div>

                    {{-- Status ganda: Usaha & Syariah --}}
                    <div class="d-flex justify-content-center gap-2 flex-wrap" style="position:relative; z-index:1;">
                        <span
                            style="background:rgba(255,255,255,.2); border:1px solid rgba(255,255,255,.3);
                             border-radius:30px; padding:.28rem .7rem; font-size:.72rem; font-weight:700; color:#fff;">
                            <i class="bi {{ $usaha->status_usaha_icon }} me-1"></i>
                            {{ $usaha->status_usaha_label }}
                        </span>
                        <span class="badge-soft tint-{{ $usaha->status_validasi_syariah_tint }}" style="font-size:.72rem;">
                            <i class="bi {{ $usaha->status_validasi_syariah_icon }} me-1"></i>
                            {{ $usaha->status_validasi_syariah_label }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- ════════════════════════════════════════════════════════
         FORM VALIDASI DPS
    ════════════════════════════════════════════════════════ --}}
            <div class="card mb-3" style="border:2px solid var(--primary); box-shadow:0 8px 24px rgba(99,102,241,.15);">
                <div class="card-header" style="background:linear-gradient(135deg,var(--primary-light),#f5f3ff);">
                    <i class="bi bi-patch-check-fill me-2" style="color:var(--primary);"></i>
                    <strong>Form Validasi DPS</strong>
                </div>
                <div class="card-body">
                    <form action="{{ route('dps.validasi-syariah.proses', $usaha->id) }}" method="POST"
                        id="formValidasiDps">
                        @csrf

                        {{-- Informasi DPS yang sedang login --}}
                        <div class="mb-3 p-2 d-flex align-items-center gap-2"
                            style="background:var(--bg-soft); border-radius:var(--radius-sm);">
                            <img src="{{ Auth::user()->foto_url }}" class="avatar-sm" alt="">
                            <div>
                                <div style="font-size:.8rem; font-weight:700; color:var(--text-dark);">
                                    {{ Auth::user()->nama }}</div>
                                <div style="font-size:.72rem; color:var(--text-muted);">Dewan Pengawas Syariah</div>
                            </div>
                        </div>

                        {{-- Pilihan Keputusan --}}
                        <div class="mb-3">
                            <label class="form-label">
                                Keputusan Validasi <span class="text-danger">*</span>
                            </label>

                            <div class="d-flex flex-column gap-2">
                                {{-- Opsi Disetujui --}}
                                <label for="setujui" class="d-flex align-items-center gap-2 p-3 rounded cursor-pointer"
                                    style="border:2px solid #e2e8f0; border-radius:var(--radius-md) !important;
                                      cursor:pointer; transition:border-color .2s, background .2s;"
                                    id="labelSetujui">
                                    <input type="radio" name="keputusan" id="setujui" value="disetujui"
                                        class="form-check-input"
                                        style="width:18px; height:18px; accent-color:#047857; flex-shrink:0;">
                                    <div>
                                        <div style="font-size:.84rem; font-weight:700; color:#047857;">
                                            <i class="bi bi-check-circle-fill me-1"></i> Disetujui
                                        </div>
                                        <div style="font-size:.73rem; color:var(--text-muted);">
                                            Akad sesuai syariah; usaha diaktifkan dan monitoring dimulai
                                        </div>
                                    </div>
                                </label>

                                {{-- Opsi Perlu Revisi --}}
                                <label for="perluRevisi" class="d-flex align-items-center gap-2 p-3"
                                    style="border:2px solid #e2e8f0; border-radius:var(--radius-md);
                                      cursor:pointer; transition:border-color .2s, background .2s;"
                                    id="labelPerluRevisi">
                                    <input type="radio" name="keputusan" id="perluRevisi" value="perlu_revisi"
                                        class="form-check-input"
                                        style="width:18px; height:18px; accent-color:#be123c; flex-shrink:0;">
                                    <div>
                                        <div style="font-size:.84rem; font-weight:700; color:#be123c;">
                                            <i class="bi bi-arrow-repeat me-1"></i> Perlu Revisi
                                        </div>
                                        <div style="font-size:.73rem; color:var(--text-muted);">
                                            Usaha tetap belum aktif sampai akad diperbaiki
                                        </div>
                                    </div>
                                </label>
                            </div>
                            @error('keputusan')
                                <div style="font-size:.76rem; color:var(--danger); margin-top:.35rem; font-weight:600;">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Catatan DPS --}}
                        <div class="mb-3">
                            <label for="catatan_dps" class="form-label">
                                Catatan DPS
                                <span id="labelCatatanWajib" class="d-none"
                                    style="font-size:.74rem; color:var(--danger);">*Wajib diisi</span>
                                <span id="labelCatatanOpsional"
                                    style="font-size:.74rem; color:var(--text-muted);">(Opsional)</span>
                            </label>
                            <textarea name="catatan_dps" id="catatan_dps" rows="4"
                                class="form-control @error('catatan_dps') is-invalid @enderror"
                                placeholder="Tuliskan catatan validasi syariah, aspek yang perlu diperbaiki, atau keterangan tambahan...">{{ old('catatan_dps') }}</textarea>
                            @error('catatan_dps')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Info tanggal otomatis --}}
                        <div class="mb-3 p-2 d-flex align-items-center gap-2"
                            style="background:var(--primary-light); border-radius:var(--radius-sm);
                            font-size:.76rem; color:var(--primary-dark);">
                            <i class="bi bi-calendar-check" style="flex-shrink:0;"></i>
                            Tanggal validasi otomatis diisi:
                            <strong>{{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB</strong>
                        </div>

                        {{-- Dampak keputusan DPS --}}
                        <div class="mb-3 p-3"
                            style="background:linear-gradient(135deg,#f8fafc,#eef2ff);
                            border:1px solid var(--border-soft); border-radius:var(--radius-md);">
                            <div style="font-size:.78rem; font-weight:800; color:var(--text-dark); margin-bottom:.5rem;">
                                <i class="bi bi-diagram-3-fill me-1" style="color:var(--primary);"></i>
                                Dampak Keputusan
                            </div>
                            <div style="font-size:.74rem; color:var(--text-muted); line-height:1.65;">
                                <div class="mb-1">
                                    <i class="bi bi-check-circle-fill me-1" style="color:#10b981;"></i>
                                    <strong>Disetujui:</strong> status usaha menjadi Aktif, tanggal aktivasi monitoring
                                    diisi otomatis,
                                    dan periode monitoring 14 hari pertama dimulai.
                                </div>
                                <div>
                                    <i class="bi bi-arrow-repeat me-1" style="color:#f43f5e;"></i>
                                    <strong>Perlu Revisi:</strong> status usaha tetap Belum Aktif dan monitoring belum dapat
                                    dimulai.
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Simpan --}}
                        <button type="submit" class="btn-theme-primary w-100" id="btnSimpanValidasi">
                            <span id="btnSimpanText" class="d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-patch-check-fill"></i> Simpan Keputusan Validasi
                            </span>
                            <span id="btnSimpanLoading" class="d-none align-items-center justify-content-center gap-2">
                                <span class="spinner-theme"></span> Menyimpan...
                            </span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Informasi Anggota --}}
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-person-badge-fill me-2" style="color:var(--accent);"></i>Informasi Anggota
                </div>
                <div class="card-body p-0">
                    @php $anggota = $usaha->anggota; @endphp
                    <div class="akun-info-row px-3">
                        <img src="{{ $anggota->akun->foto_url }}" class="avatar-sm" alt="">
                        <div>
                            <div class="akun-info-label">Nama Anggota</div>
                            <div class="akun-info-value">{{ $anggota->akun->nama }}</div>
                        </div>
                    </div>
                    <div class="akun-info-row px-3">
                        <div class="akun-info-icon tint-indigo"><i class="bi bi-hash"></i></div>
                        <div>
                            <div class="akun-info-label">Nomor Anggota</div>
                            <div class="akun-info-value"><span class="code-tag">{{ $anggota->nomor_anggota }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="akun-info-row px-3">
                        <div class="akun-info-icon tint-slate"><i class="bi bi-credit-card-fill"></i></div>
                        <div>
                            <div class="akun-info-label">NIK</div>
                            <div class="akun-info-value"><span class="code-tag">{{ $anggota->nik }}</span></div>
                        </div>
                    </div>
                    <div class="akun-info-row px-3" style="border-bottom:none;">
                        <div class="akun-info-icon tint-emerald"><i class="bi bi-telephone-fill"></i></div>
                        <div>
                            <div class="akun-info-label">Telepon</div>
                            <div class="akun-info-value">{{ $anggota->telepon }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
     KOLOM KANAN — Detail Usaha, Akad, Laporan, Riwayat
═══════════════════════════════════════════════════════════ --}}
        <div class="col-12 col-lg-8">


            {{-- ── Alur Aktivasi Monitoring ─────────────────────────────── --}}
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-signpost-split-fill me-2" style="color:var(--primary);"></i>
                    Alur Aktivasi Monitoring
                </div>
                <div class="card-body">
                    <div class="row g-2 text-center">
                        <div class="col-12 col-md-4">
                            <div class="p-3 h-100"
                                style="background:#f8fafc; border-radius:var(--radius-md); border:1px solid var(--border-soft);">
                                <i class="bi bi-file-earmark-text-fill" style="font-size:1.35rem; color:#6366f1;"></i>
                                <div style="font-size:.78rem; font-weight:800; margin-top:.35rem;">1. Akad Diajukan</div>
                                <div style="font-size:.7rem; color:var(--text-muted);">Dokumen diperiksa oleh DPS</div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="p-3 h-100"
                                style="background:#fffbeb; border-radius:var(--radius-md); border:1px solid #fde68a;">
                                <i class="bi bi-shield-check" style="font-size:1.35rem; color:#d97706;"></i>
                                <div style="font-size:.78rem; font-weight:800; margin-top:.35rem;">2. Keputusan DPS</div>
                                <div style="font-size:.7rem; color:var(--text-muted);">Disetujui atau perlu revisi</div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="p-3 h-100"
                                style="background:#ecfdf5; border-radius:var(--radius-md); border:1px solid #a7f3d0;">
                                <i class="bi bi-calendar2-check-fill" style="font-size:1.35rem; color:#047857;"></i>
                                <div style="font-size:.78rem; font-weight:800; margin-top:.35rem;">3. Monitoring Aktif
                                </div>
                                <div style="font-size:.7rem; color:var(--text-muted);">
                                    @if ($usaha->tanggal_aktivasi_monitoring)
                                        Mulai {{ $usaha->tanggal_aktivasi_monitoring->translatedFormat('d F Y') }}
                                    @else
                                        Menunggu persetujuan DPS
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Informasi Usaha & Akad ────────────────────────────── --}}
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-shop-window me-2" style="color:var(--primary);"></i>Informasi Usaha &amp; Akad
                </div>
                <div class="card-body p-0">

                    <div class="akun-info-row px-4">
                        <div class="akun-info-icon tint-cyan"><i class="bi bi-grid-3x3-gap-fill"></i></div>
                        <div>
                            <div class="akun-info-label">Jenis Usaha</div>
                            <div class="akun-info-value">
                                <span
                                    class="badge-soft tint-{{ $usaha->jenis_usaha_tint }}">{{ $usaha->jenis_usaha_label }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="akun-info-row px-4">
                        <div class="akun-info-icon tint-violet"><i class="bi bi-patch-check-fill"></i></div>
                        <div>
                            <div class="akun-info-label">Jenis Akad Syariah</div>
                            <div class="akun-info-value">
                                @if ($usaha->jenis_akad)
                                    <span class="badge-soft tint-{{ $usaha->jenis_akad_tint }}"
                                        style="font-size:.85rem; padding:.35rem .9rem;">
                                        {{ $usaha->jenis_akad }}
                                    </span>
                                @else
                                    <span style="color:var(--danger); font-size:.82rem; font-weight:700;">
                                        <i class="bi bi-exclamation-circle-fill me-1"></i>Jenis akad belum ditentukan
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="akun-info-row px-4">
                        <div class="akun-info-icon tint-emerald"><i class="bi bi-cash-stack"></i></div>
                        <div>
                            <div class="akun-info-label">Modal Awal</div>
                            <div class="akun-info-value">Rp {{ number_format($usaha->modal_awal, 0, ',', '.') }}</div>
                        </div>
                    </div>

                    <div class="akun-info-row px-4">
                        <div class="akun-info-icon tint-cyan"><i class="bi bi-calendar-event"></i></div>
                        <div>
                            <div class="akun-info-label">Tanggal Mulai Usaha</div>
                            <div class="akun-info-value">
                                {{ $usaha->tanggal_mulai ? $usaha->tanggal_mulai->translatedFormat('d F Y') : '—' }}
                            </div>
                        </div>
                    </div>

                    <div class="akun-info-row px-4">
                        <div class="akun-info-icon tint-indigo"><i class="bi bi-calendar2-check-fill"></i></div>
                        <div>
                            <div class="akun-info-label">Tanggal Aktivasi Monitoring</div>
                            <div class="akun-info-value">
                                @if ($usaha->tanggal_aktivasi_monitoring)
                                    <span style="color:#047857; font-weight:800;">
                                        {{ $usaha->tanggal_aktivasi_monitoring->translatedFormat('d F Y') }}
                                    </span>
                                @else
                                    <span style="color:var(--text-muted);">Belum dimulai — menunggu persetujuan DPS</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($usaha->deskripsi)
                        <div class="akun-info-row px-4">
                            <div class="akun-info-icon tint-blue"><i class="bi bi-card-text"></i></div>
                            <div>
                                <div class="akun-info-label">Deskripsi Usaha</div>
                                <div class="akun-info-value" style="font-weight:500; line-height:1.55;">
                                    {{ $usaha->deskripsi }}</div>
                            </div>
                        </div>
                    @endif

                    <div class="akun-info-row px-4" style="border-bottom:none;">
                        <div class="akun-info-icon tint-amber"><i class="bi bi-geo-alt-fill"></i></div>
                        <div>
                            <div class="akun-info-label">Alamat Usaha</div>
                            <div class="akun-info-value" style="font-weight:500;">{{ $usaha->alamat_usaha ?: '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Dokumen Akad ──────────────────────────────────────── --}}
            <div class="card mb-3"
                style="border:{{ $usaha->has_file_akad ? '2px solid #10b981' : '2px solid #f43f5e' }};">
                <div class="card-header d-flex align-items-center justify-content-between"
                    style="background:{{ $usaha->has_file_akad ? 'linear-gradient(135deg,#ecfdf5,#d1fae5)' : 'linear-gradient(135deg,#fff1f2,#ffe4e6)' }};">
                    <span>
                        <i class="bi bi-file-earmark-text-fill me-2"
                            style="color:{{ $usaha->has_file_akad ? '#047857' : '#be123c' }};"></i>
                        Dokumen Ketentuan Akad
                    </span>
                    @if (!$usaha->has_file_akad)
                        <span class="badge-soft tint-rose" style="font-size:.72rem;">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>Dokumen Belum Ada
                        </span>
                    @endif
                </div>
                <div class="card-body">
                    @if ($usaha->has_file_akad)
                        <div class="d-flex align-items-center gap-3"
                            style="background:var(--bg-soft); border-radius:var(--radius-md); padding:1rem 1.1rem;">
                            <i class="bi {{ $usaha->file_akad_icon }}"
                                style="font-size:2.4rem; flex-shrink:0;
                              color:{{ $usaha->file_akad_extension === 'pdf' ? '#dc2626' : '#2563eb' }};"></i>
                            <div style="flex:1; min-width:0;">
                                <div
                                    style="font-size:.85rem; font-weight:700; overflow:hidden;
                                    text-overflow:ellipsis; white-space:nowrap;">
                                    {{ $usaha->file_akad }}</div>
                                <span class="badge-soft tint-slate" style="font-size:.68rem;">
                                    {{ strtoupper($usaha->file_akad_extension) }}
                                </span>
                            </div>
                            <a href="{{ $usaha->file_akad_url }}" target="_blank" class="btn-theme-primary"
                                style="padding:.5rem 1.1rem; font-size:.82rem; flex-shrink:0;">
                                <i class="bi bi-eye me-1"></i> Buka Dokumen
                            </a>
                        </div>
                        <div class="mt-2 p-2 d-flex align-items-center gap-2"
                            style="background:#fffbeb; border-radius:var(--radius-sm); border-left:3px solid var(--warning);
                            font-size:.76rem; color:#92400e;">
                            <i class="bi bi-info-circle-fill" style="flex-shrink:0;"></i>
                            Periksa kesesuaian isi dokumen akad dengan jenis akad yang dipilih
                            (<strong>{{ $usaha->jenis_akad ?? 'Belum ditentukan' }}</strong>) dan prinsip syariah.
                        </div>
                    @else
                        <div class="theme-empty-state" style="padding:1.8rem 1rem;">
                            <div class="empty-icon"
                                style="width:58px; height:58px; font-size:1.5rem; background:linear-gradient(135deg,#fff1f2,#ffe4e6);">
                                <i class="bi bi-file-earmark-x" style="color:#be123c;"></i>
                            </div>
                            <h6 style="font-size:.88rem; color:#be123c;">Dokumen akad belum diupload</h6>
                            <p style="font-size:.78rem;">
                                Anggota belum mengunggah dokumen ketentuan akad.
                                Anda dapat memberikan keputusan "Perlu Revisi" agar anggota segera mengupload dokumen.
                            </p>
                        </div>
                    @endif
                </div>
            </div>


            {{-- ── Riwayat Validasi Akad ─────────────────────────────── --}}
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span>
                        <i class="bi bi-shield-check me-2" style="color:#047857;"></i>
                        Riwayat Validasi Akad Sebelumnya
                    </span>
                    <span class="badge-soft tint-slate" style="font-size:.72rem;">
                        {{ $usaha->validasiAkad->count() }} iterasi
                    </span>
                </div>
                <div class="card-body p-0">
                    @forelse ($usaha->validasiAkad->sortByDesc('iterasi') as $va)
                        <div class="px-4 py-3" style="border-bottom:1px solid #f6f7fb;">
                            <div class="d-flex align-items-start justify-content-between gap-2 mb-2 flex-wrap">
                                <div class="d-flex align-items-center gap-2">
                                    <div
                                        style="width:32px; height:32px; border-radius:8px; flex-shrink:0;
                                        background:linear-gradient(135deg,#eef2ff,#e0e7ff);
                                        display:flex; align-items:center; justify-content:center;
                                        font-size:.78rem; color:var(--primary); font-weight:800;">
                                        #{{ $va->iterasi }}
                                    </div>
                                    <div>
                                        <div style="font-size:.82rem; font-weight:700; color:var(--text-dark);">
                                            Iterasi ke-{{ $va->iterasi }}
                                        </div>
                                        <div style="font-size:.72rem; color:var(--text-muted);">
                                            {{ $va->created_at->translatedFormat('d F Y, H:i') }}
                                        </div>
                                    </div>
                                </div>
                                <span class="badge-soft tint-{{ $va->status_tint }}" style="flex-shrink:0;">
                                    <i class="bi {{ $va->status_icon }} me-1"></i>
                                    {{ $va->status_label }}
                                </span>
                            </div>

                            {{-- Info DPS --}}
                            @if ($va->dps)
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <img src="{{ $va->dps->foto_url }}" class="avatar-sm" alt="">
                                    <div>
                                        <div style="font-size:.78rem; font-weight:700;">{{ $va->dps->nama }}</div>
                                        <div style="font-size:.7rem; color:var(--text-muted);">Dewan Pengawas Syariah</div>
                                    </div>
                                </div>
                            @endif

                            {{-- Tanggal validasi --}}
                            @if ($va->divalidasi_pada)
                                <div style="font-size:.74rem; color:var(--text-muted); margin-bottom:.4rem;">
                                    <i class="bi bi-calendar-check me-1"></i>
                                    {{ $va->divalidasi_pada->translatedFormat('d F Y, H:i') }}
                                </div>
                            @endif

                            {{-- Catatan DPS --}}
                            @if ($va->catatan_dps)
                                <div
                                    style="background:var(--bg-soft); border-radius:var(--radius-sm);
                                    padding:.55rem .75rem; font-size:.78rem; color:var(--text-dark);
                                    line-height:1.5; border-left:3px solid
                                    {{ $va->status === 'disetujui' ? '#10b981' : '#f59e0b' }};">
                                    <div
                                        style="font-size:.68rem; font-weight:700; text-transform:uppercase;
                                        letter-spacing:.3px; color:var(--text-muted); margin-bottom:.25rem;">
                                        Catatan DPS
                                    </div>
                                    {{ $va->catatan_dps }}
                                </div>
                            @endif

                            {{-- Link dokumen iterasi ini --}}
                            @if ($va->file_akad_divalidasi && $va->file_akad_divalidasi !== '-')
                                <div class="mt-2">
                                    <a href="{{ asset('uploads/akad/' . $va->file_akad_divalidasi) }}" target="_blank"
                                        style="font-size:.76rem; color:var(--primary); font-weight:600;
                                      text-decoration:none; display:inline-flex; align-items:center; gap:.3rem;">
                                        <i class="bi bi-file-earmark-text"></i>
                                        Lihat dokumen akad iterasi ini
                                    </a>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="theme-empty-state" style="padding:2rem 1rem;">
                            <div class="empty-icon" style="width:56px; height:56px; font-size:1.4rem;">
                                <i class="bi bi-shield-x"></i>
                            </div>
                            <h6 style="font-size:.88rem;">Belum ada riwayat validasi</h6>
                            <p style="font-size:.78rem;">Ini adalah validasi pertama untuk usaha ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>{{-- End kolom kanan --}}
    </div>{{-- End row --}}

    {{-- Loading Overlay --}}
    <div class="theme-loading-overlay" id="loadingOverlay">
        <div class="theme-spinner"></div>
        <span>Menyimpan keputusan validasi...</span>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // ── Styling interaktif pada radio button ─────────────────
        const radioSetujui = document.getElementById('setujui');
        const radioPerluRevisi = document.getElementById('perluRevisi');
        const labelSetujui = document.getElementById('labelSetujui');
        const labelPerluRevisi = document.getElementById('labelPerluRevisi');
        const labelCatatanWajib = document.getElementById('labelCatatanWajib');
        const labelCatatanOpsional = document.getElementById('labelCatatanOpsional');

        function updateRadioStyle() {
            if (radioSetujui.checked) {
                labelSetujui.style.borderColor = '#10b981';
                labelSetujui.style.background = 'linear-gradient(135deg,#ecfdf5,#d1fae5)';
                labelPerluRevisi.style.borderColor = '#e2e8f0';
                labelPerluRevisi.style.background = '';
                labelCatatanWajib.classList.add('d-none');
                labelCatatanOpsional.classList.remove('d-none');
            } else if (radioPerluRevisi.checked) {
                labelPerluRevisi.style.borderColor = '#f43f5e';
                labelPerluRevisi.style.background = 'linear-gradient(135deg,#fff1f2,#ffe4e6)';
                labelSetujui.style.borderColor = '#e2e8f0';
                labelSetujui.style.background = '';
                labelCatatanWajib.classList.remove('d-none');
                labelCatatanOpsional.classList.add('d-none');
            }
        }

        radioSetujui.addEventListener('change', updateRadioStyle);
        radioPerluRevisi.addEventListener('change', updateRadioStyle);

        // ── Konfirmasi sebelum submit ─────────────────────────────
        document.getElementById('formValidasiDps').addEventListener('submit', function(e) {
            e.preventDefault();

            const keputusan = document.querySelector('input[name="keputusan"]:checked');
            if (!keputusan) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Keputusan',
                    text: 'Silakan pilih keputusan validasi terlebih dahulu.',
                    buttonsStyling: false,
                    customClass: {
                        popup: 'swal-theme-popup',
                        title: 'swal-theme-title',
                        htmlContainer: 'swal-theme-html',
                        confirmButton: 'swal-theme-confirm'
                    }
                });
                return;
            }

            const catatan = document.getElementById('catatan_dps').value.trim();
            if (keputusan.value === 'perlu_revisi' && catatan.length < 5) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Catatan Wajib Diisi',
                    text: 'Catatan DPS wajib diisi minimal 5 karakter saat meminta revisi.',
                    buttonsStyling: false,
                    customClass: {
                        popup: 'swal-theme-popup',
                        title: 'swal-theme-title',
                        htmlContainer: 'swal-theme-html',
                        confirmButton: 'swal-theme-confirm'
                    }
                });
                document.getElementById('catatan_dps').focus();
                return;
            }

            const isSetujui = keputusan.value === 'disetujui';
            const judulKonfirmasi = isSetujui ? 'Setujui Akad Syariah?' : 'Minta Revisi Akad?';
            const pesanKonfirmasi = isSetujui ?
                'Akad usaha <strong>{{ $usaha->nama_usaha }}</strong> akan dinyatakan <strong>sesuai syariah</strong>. Usaha akan menjadi aktif, tanggal aktivasi monitoring akan diisi otomatis, dan periode monitoring 14 hari pertama akan dimulai.' :
                'Anggota akan diminta memperbaiki dokumen akad. Pastikan catatan revisi sudah lengkap.';
            const konfirmBtn = isSetujui ? 'swal-theme-confirm' : 'swal-theme-deny';

            Swal.fire({
                title: judulKonfirmasi,
                html: `<p style="font-size:.87rem; color:var(--text-muted);">${pesanKonfirmasi}</p>`,
                icon: isSetujui ? 'question' : 'warning',
                showCancelButton: true,
                confirmButtonText: isSetujui ?
                    '<i class="bi bi-patch-check-fill me-1"></i> Ya, Setujui' :
                    '<i class="bi bi-arrow-repeat me-1"></i> Ya, Minta Revisi',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                buttonsStyling: false,
                customClass: {
                    popup: 'swal-theme-popup',
                    title: 'swal-theme-title',
                    htmlContainer: 'swal-theme-html',
                    confirmButton: konfirmBtn,
                    cancelButton: 'swal-theme-cancel',
                }
            }).then(result => {
                if (result.isConfirmed) {
                    document.getElementById('btnSimpanText').classList.add('d-none');
                    document.getElementById('btnSimpanLoading').classList.remove('d-none');
                    document.getElementById('btnSimpanLoading').classList.add('d-flex');
                    document.getElementById('btnSimpanValidasi').disabled = true;
                    document.getElementById('loadingOverlay').classList.add('show');
                    document.getElementById('formValidasiDps').submit();
                }
            });
        });

        // ── Flash message ──────────────────────────────────────────
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 4000,
                timerProgressBar: true,
                buttonsStyling: false,
                customClass: {
                    popup: 'swal-theme-popup',
                    title: 'swal-theme-title',
                    htmlContainer: 'swal-theme-html',
                    confirmButton: 'swal-theme-confirm'
                }
            });
        @endif
    </script>
@endpush
