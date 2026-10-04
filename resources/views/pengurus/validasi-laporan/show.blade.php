@extends('layouts.app')

@section('title', 'Periksa Laporan')
@section('page-title', 'Periksa Laporan Usaha')
@section('page-subtitle', 'Validasi laporan ' . $laporan->periode_label)

@section('sidebar-menu')
    @include('pengurus.partials._sidebar')
@endsection

@section('content')

    @php
        $usaha = $laporan->usaha;
        $anggota = $usaha->anggota;
        $validasiTersimpan = $laporan->validasi;

        $usahaSiapMonitoring =
            $usaha->status_usaha === 'aktif' &&
            $usaha->status_validasi_syariah === 'disetujui' &&
            !is_null($usaha->tanggal_aktivasi_monitoring);

        $laporanMenunggu = $laporan->status_laporan === 'dikirim';
        $bolehDiproses = $laporanMenunggu && $usahaSiapMonitoring;

        $hasilEvaluasi = $validasiTersimpan?->hasil_evaluasi ?? $prediksiEvaluasi;

        $hasilTint = match ($hasilEvaluasi) {
            'aktif' => 'emerald',
            'peringatan' => 'amber',
            'tidak_aktif' => 'rose',
            default => 'slate',
        };

        $hasilLabel = match ($hasilEvaluasi) {
            'aktif' => 'Aktif',
            'peringatan' => 'Peringatan',
            'tidak_aktif' => 'Tidak Aktif',
            default => '-',
        };
    @endphp

    {{-- ── Tombol Navigasi ─────────────────────────────────────── --}}
    <div class="d-flex align-items-center gap-2 flex-wrap mb-3">
        <a href="{{ route('pengurus.validasi-laporan.index') }}" class="btn-theme-light"
            style="padding:.4rem .9rem; font-size:.83rem;">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>

        @if ($bolehDiproses)
            <button type="button" class="btn-theme-primary" id="btnSetujui" data-bs-toggle="modal"
                data-bs-target="#modalSetujui" style="padding:.4rem 1rem; font-size:.83rem;">
                <i class="bi bi-check-circle-fill me-1"></i> Setujui Laporan
            </button>
            <button type="button" class="btn-theme-danger" id="btnTolak" data-bs-toggle="modal"
                data-bs-target="#modalTolak" style="padding:.4rem 1rem; font-size:.83rem;">
                <i class="bi bi-x-circle-fill me-1"></i> Tolak Laporan
            </button>
        @elseif ($laporanMenunggu)
            <span class="badge-soft tint-amber" style="font-size:.82rem; padding:.4rem .9rem;">
                <i class="bi bi-shield-exclamation me-1"></i>
                Belum dapat diproses
            </span>
        @else
            <span class="badge-soft tint-{{ $laporan->status_laporan_tint }}"
                style="font-size:.82rem; padding:.4rem .9rem;">
                <i class="bi {{ $laporan->status_laporan_icon }} me-1"></i>
                Laporan sudah {{ $laporan->status_laporan_label }}
            </span>
        @endif
    </div>

    @if ($laporanMenunggu && !$usahaSiapMonitoring)
        <div class="alert alert-warning border-0 shadow-sm d-flex align-items-start gap-3 mb-3"
            style="border-radius:var(--radius-md);">
            <i class="bi bi-shield-exclamation fs-5 mt-1"></i>
            <div>
                <div class="fw-bold mb-1">Laporan belum dapat divalidasi Pengurus</div>
                <div style="font-size:.84rem; line-height:1.55;">
                    Sesuai alur sistem, akad usaha harus disetujui DPS dan status usaha harus aktif
                    sebelum laporan monitoring diproses. Selesaikan validasi akad terlebih dahulu.
                </div>
            </div>
        </div>
    @endif

    <div class="row g-3">

        {{-- ═══════════════════════════════════════════════════════════
     KOLOM KIRI — Info + Smart Validation
═══════════════════════════════════════════════════════════ --}}
        <div class="col-12 col-lg-4">

            {{-- Profil Laporan --}}
            <div class="card mb-3" style="overflow:hidden;">
                <div
                    style="background:linear-gradient(120deg, var(--primary) 0%, #818cf8 60%, var(--secondary) 130%);
                    padding:1.5rem; text-align:center; position:relative; overflow:hidden;">
                    <div
                        style="position:absolute; width:100px; height:100px; background:rgba(255,255,255,.1);
                        border-radius:50%; top:-40px; right:-30px;">
                    </div>
                    <div
                        style="width:60px; height:60px; border-radius:14px; background:rgba(255,255,255,.18);
                        border:2px solid rgba(255,255,255,.4);
                        display:flex; align-items:center; justify-content:center;
                        font-size:1.7rem; color:#fff; margin:0 auto .6rem; position:relative; z-index:1;">
                        <i class="bi bi-journal-richtext"></i>
                    </div>
                    <div style="font-weight:800; color:#fff; font-size:.95rem; position:relative; z-index:1;">
                        Periode ke-{{ $laporan->periode_ke }}
                    </div>
                    <div
                        style="font-size:.78rem; color:rgba(255,255,255,.85); margin:.2rem 0 .65rem; position:relative; z-index:1;">
                        {{ $laporan->tanggal_mulai->format('d/m/Y') }} — {{ $laporan->tanggal_selesai->format('d/m/Y') }}
                    </div>
                    <span class="badge-soft"
                        style="background:rgba(255,255,255,.2) !important; color:#fff !important;
                         border:1px solid rgba(255,255,255,.3); font-size:.76rem; position:relative; z-index:1;">
                        <i class="bi {{ $laporan->status_laporan_icon }} me-1"></i>
                        {{ $laporan->status_laporan_label }}
                    </span>
                </div>

                {{-- Catatan pengurus jika sudah ada --}}
                @if ($laporan->catatan_pengurus)
                    <div class="card-body py-3">
                        <div
                            style="font-size:.7rem; font-weight:700; text-transform:uppercase;
                            letter-spacing:.4px; color:var(--text-muted); margin-bottom:.4rem;">
                            Catatan Pengurus
                        </div>
                        <p style="font-size:.82rem; color:var(--text-dark); line-height:1.55; margin:0;">
                            {{ $laporan->catatan_pengurus }}
                        </p>
                    </div>
                @endif
            </div>

            {{-- ═══════════════════════════════════════════════════════
         SMART VALIDATION PREVIEW
    ═══════════════════════════════════════════════════════ --}}
            @php
                $prediksiTint = match ($prediksiEvaluasi) {
                    'aktif' => 'emerald',
                    'peringatan' => 'amber',
                    'tidak_aktif' => 'rose',
                    default => 'slate',
                };
                $prediksiLabel = match ($prediksiEvaluasi) {
                    'aktif' => 'Aktif',
                    'peringatan' => 'Peringatan',
                    'tidak_aktif' => 'Tidak Aktif',
                    default => '-',
                };
            @endphp

            <div class="card mb-3"
                style="border:2px solid {{ $hasilEvaluasi === 'aktif' ? '#10b981' : ($hasilEvaluasi === 'tidak_aktif' ? '#f43f5e' : '#f59e0b') }};">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span>
                        <i class="bi bi-calculator-fill me-2" style="color:var(--primary);"></i>
                        Analisis Evaluasi Otomatis
                    </span>
                    @if ($laporanMenunggu)
                        <span style="font-size:.68rem; font-weight:600; color:var(--text-light);">
                            Preview — belum disimpan
                        </span>
                    @else
                        <span style="font-size:.68rem; font-weight:600; color:#047857;">
                            Hasil tersimpan
                        </span>
                    @endif
                </div>
                <div class="card-body">

                    {{-- Prediksi Hasil --}}
                    <div class="text-center mb-3 pb-3" style="border-bottom:1px solid var(--border-soft);">
                        <div
                            style="font-size:.72rem; font-weight:700; text-transform:uppercase;
                            letter-spacing:.3px; color:var(--text-muted); margin-bottom:.5rem;">
                            {{ $laporanMenunggu ? 'Prediksi Hasil Evaluasi' : 'Hasil Evaluasi Otomatis' }}
                        </div>
                        <span class="badge-soft tint-{{ $hasilTint }}" style="font-size:.95rem; padding:.5rem 1.3rem;">
                            {{ $hasilLabel }}
                        </span>
                    </div>

                    {{-- Indikator Omzet --}}
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <div style="font-size:.8rem; font-weight:700; color:var(--text-dark);">
                                <i class="bi bi-graph-up me-1"></i> Kenaikan Omzet
                            </div>
                            <span class="badge-soft {{ $indikatorOmzet ? 'tint-emerald' : 'tint-rose' }}"
                                style="font-size:.72rem;">
                                {{ $indikatorOmzet ? '✓ Memenuhi' : '✗ Tidak Memenuhi' }}
                            </span>
                        </div>
                        <div class="row g-2" style="font-size:.8rem;">
                            <div class="col-6">
                                <div
                                    style="background:var(--bg-soft); border-radius:var(--radius-sm); padding:.5rem .7rem;">
                                    <div style="font-size:.68rem; color:var(--text-muted); font-weight:600;">Omzet
                                        Sebelumnya</div>
                                    <div style="font-weight:700; color:var(--text-dark);">
                                        @if ($omzetSebelumnya > 0)
                                            Rp {{ number_format($omzetSebelumnya, 0, ',', '.') }}
                                        @else
                                            <span style="color:var(--text-light);">Periode pertama</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div
                                    style="background:var(--bg-soft); border-radius:var(--radius-sm); padding:.5rem .7rem;">
                                    <div style="font-size:.68rem; color:var(--text-muted); font-weight:600;">Omzet Sekarang
                                    </div>
                                    <div style="font-weight:700; color:var(--text-dark);">
                                        Rp {{ number_format($laporan->omzet, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-2"
                            style="background:{{ $indikatorOmzet ? 'linear-gradient(135deg,#ecfdf5,#d1fae5)' : 'linear-gradient(135deg,#fff1f2,#ffe4e6)' }};
                            border-radius:var(--radius-sm); padding:.5rem; font-size:.82rem; font-weight:800;
                            color:{{ $indikatorOmzet ? '#047857' : '#be123c' }};">
                            <i class="bi bi-{{ $pctKenaikan >= 0 ? 'arrow-up' : 'arrow-down' }}-circle-fill me-1"></i>
                            {{ $pctKenaikan >= 0 ? '+' : '' }}{{ number_format($pctKenaikan, 2) }}%
                            <span style="font-size:.7rem; font-weight:600; opacity:.8;">(target ≥ 5%)</span>
                        </div>
                    </div>

                    {{-- Indikator Biaya --}}
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <div style="font-size:.8rem; font-weight:700; color:var(--text-dark);">
                                <i class="bi bi-pie-chart-fill me-1"></i> Rasio Biaya Operasional
                            </div>
                            <span class="badge-soft {{ $indikatorBiaya ? 'tint-emerald' : 'tint-rose' }}"
                                style="font-size:.72rem;">
                                {{ $indikatorBiaya ? '✓ Memenuhi' : '✗ Tidak Memenuhi' }}
                            </span>
                        </div>
                        <div class="text-center"
                            style="background:{{ $indikatorBiaya ? 'linear-gradient(135deg,#ecfdf5,#d1fae5)' : 'linear-gradient(135deg,#fff1f2,#ffe4e6)' }};
                            border-radius:var(--radius-sm); padding:.6rem; font-size:.88rem; font-weight:800;
                            color:{{ $indikatorBiaya ? '#047857' : '#be123c' }};">
                            {{ number_format($rasioBiaya, 2) }}% dari omzet
                            <div style="font-size:.7rem; font-weight:600; opacity:.8; margin-top:.2rem;">(target &lt; 60%)
                            </div>
                        </div>

                        {{-- Progress bar rasio biaya --}}
                        <div class="progress-soft mt-2">
                            <div class="progress-bar"
                                style="width:{{ min($rasioBiaya, 100) }}%;
                                background:{{ $indikatorBiaya ? 'linear-gradient(90deg,#10b981,#34d399)' : 'linear-gradient(90deg,#f43f5e,#fb7185)' }};">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-1" style="font-size:.68rem; color:var(--text-light);">
                            <span>0%</span>
                            <span style="color:var(--warning); font-weight:700;">Batas: 60%</span>
                            <span>100%</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Informasi Anggota --}}
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-person-badge-fill me-2" style="color:var(--accent);"></i>Informasi Anggota
                </div>
                <div class="card-body p-0">
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
                    @php
                        $aTint = match ($anggota->status_anggota) {
                            'aktif' => 'emerald',
                            'peringatan' => 'amber',
                            default => 'rose',
                        };
                    @endphp
                    <div class="akun-info-row px-3" style="border-bottom:none;">
                        <div class="akun-info-icon tint-{{ $aTint }}"><i class="bi bi-toggle-on"></i></div>
                        <div>
                            <div class="akun-info-label">Status Anggota Saat Ini</div>
                            <div class="akun-info-value">
                                <span
                                    class="badge-soft tint-{{ $aTint }}">{{ $anggota->status_anggota_label }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
     KOLOM KANAN — Detail Laporan + Riwayat
═══════════════════════════════════════════════════════════ --}}
        <div class="col-12 col-lg-8">

            {{-- Informasi Usaha --}}
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-shop-window me-2" style="color:var(--primary);"></i>Informasi Usaha
                </div>
                <div class="card-body p-0">

                    <div class="akun-info-row px-4">
                        <div class="akun-info-icon tint-indigo"><i class="bi bi-shop-window"></i></div>
                        <div>
                            <div class="akun-info-label">Nama Usaha</div>
                            <div class="akun-info-value">{{ $usaha->nama_usaha }}</div>
                        </div>
                    </div>

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

                    @if ($usaha->jenis_akad)
                        <div class="akun-info-row px-4">
                            <div class="akun-info-icon tint-violet"><i class="bi bi-patch-check-fill"></i></div>
                            <div>
                                <div class="akun-info-label">Jenis Akad Syariah</div>
                                <div class="akun-info-value">
                                    <span
                                        class="badge-soft tint-{{ $usaha->jenis_akad_tint }}">{{ $usaha->jenis_akad_label }}</span>
                                    <span class="badge-soft tint-{{ $usaha->status_validasi_syariah_tint }} ms-2"
                                        style="font-size:.68rem;">
                                        <i class="bi {{ $usaha->status_validasi_syariah_icon }} me-1"></i>
                                        {{ $usaha->status_validasi_syariah_label }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="akun-info-row px-4" style="border-bottom:none;">
                        <div class="akun-info-icon tint-emerald"><i class="bi bi-cash-stack"></i></div>
                        <div>
                            <div class="akun-info-label">Modal Awal</div>
                            <div class="akun-info-value">Rp {{ number_format($usaha->modal_awal, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Data Keuangan Laporan --}}
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-cash-stack me-2" style="color:var(--warning);"></i>Data Keuangan Periode Ini
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-sm-4">
                            <div
                                style="background:linear-gradient(135deg,#ecfdf5,#d1fae5); border-radius:var(--radius-md);
                                padding:1rem; text-align:center;">
                                <div
                                    style="font-size:.7rem; font-weight:700; color:#047857; text-transform:uppercase;
                                    letter-spacing:.3px; margin-bottom:.35rem;">
                                    Omzet</div>
                                <div style="font-size:1.1rem; font-weight:800; color:#047857;">
                                    Rp {{ number_format($laporan->omzet, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div
                                style="background:linear-gradient(135deg,#fff1f2,#ffe4e6); border-radius:var(--radius-md);
                                padding:1rem; text-align:center;">
                                <div
                                    style="font-size:.7rem; font-weight:700; color:#be123c; text-transform:uppercase;
                                    letter-spacing:.3px; margin-bottom:.35rem;">
                                    Biaya Operasional</div>
                                <div style="font-size:1.1rem; font-weight:800; color:#be123c;">
                                    Rp {{ number_format($laporan->biaya_operasional, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                        @php $labaPositif = (float) $laporan->laba_bersih >= 0; @endphp
                        <div class="col-12 col-sm-4">
                            <div
                                style="background:{{ $labaPositif ? 'linear-gradient(135deg,#eef2ff,#e0e7ff)' : 'linear-gradient(135deg,#fff1f2,#ffe4e6)' }};
                                border-radius:var(--radius-md); padding:1rem; text-align:center;
                                border:2px solid {{ $labaPositif ? '#c7d2fe' : '#fecaca' }};">
                                <div
                                    style="font-size:.7rem; font-weight:700; color:{{ $labaPositif ? '#4338ca' : '#be123c' }};
                                    text-transform:uppercase; letter-spacing:.3px; margin-bottom:.35rem;">
                                    Laba Bersih</div>
                                <div
                                    style="font-size:1.1rem; font-weight:800; color:{{ $labaPositif ? '#4338ca' : '#be123c' }};">
                                    <i class="bi bi-arrow-{{ $labaPositif ? 'up' : 'down' }}-circle-fill me-1"></i>
                                    Rp {{ number_format(abs($laporan->laba_bersih), 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Catatan Anggota --}}
                    @if ($laporan->keterangan)
                        <div class="mt-3 p-3"
                            style="background:var(--bg-soft); border-radius:var(--radius-sm);
                            border-left:3px solid var(--secondary);">
                            <div
                                style="font-size:.7rem; font-weight:700; text-transform:uppercase;
                                letter-spacing:.3px; color:var(--text-muted); margin-bottom:.3rem;">
                                Catatan Anggota
                            </div>
                            <p style="font-size:.84rem; color:var(--text-dark); line-height:1.6; margin:0;">
                                {{ $laporan->keterangan }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Bukti Laporan --}}
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-paperclip me-2" style="color:#be185d;"></i>Bukti Laporan
                </div>
                <div class="card-body">
                    @if ($laporan->has_file_bukti)
                        <div class="d-flex align-items-center gap-3"
                            style="background:var(--bg-soft); border-radius:var(--radius-md); padding:1rem 1.1rem;">
                            <i class="bi {{ $laporan->file_bukti_icon }}"
                                style="font-size:2rem; flex-shrink:0;
                              color:{{ in_array($laporan->file_bukti_extension, ['jpg', 'jpeg', 'png']) ? '#7c3aed' : '#dc2626' }};"></i>
                            <div style="flex:1; min-width:0;">
                                <div
                                    style="font-size:.84rem; font-weight:700; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                    {{ $laporan->file_bukti }}
                                </div>
                                <span class="badge-soft tint-slate"
                                    style="font-size:.68rem;">{{ strtoupper($laporan->file_bukti_extension) }}</span>
                            </div>
                            <a href="{{ $laporan->file_bukti_url }}" target="_blank" class="btn-theme-primary"
                                style="padding:.45rem 1rem; font-size:.8rem; flex-shrink:0;">
                                <i class="bi bi-eye me-1"></i> Lihat Bukti
                            </a>
                        </div>
                    @else
                        <div class="theme-empty-state" style="padding:1.6rem 1rem;">
                            <div class="empty-icon" style="width:52px; height:52px; font-size:1.4rem;">
                                <i class="bi bi-file-earmark-x"></i>
                            </div>
                            <h6 style="font-size:.86rem;">Tidak ada bukti laporan</h6>
                            <p style="font-size:.76rem;">Anggota tidak mengupload bukti untuk periode ini.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Riwayat 3 Laporan Sebelumnya --}}
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span>
                        <i class="bi bi-clock-history me-2" style="color:var(--accent);"></i>Riwayat Laporan Sebelumnya
                    </span>
                    <span class="badge-soft tint-slate" style="font-size:.72rem;">3 terakhir</span>
                </div>
                <div class="card-body p-0">
                    @forelse ($riwayatLaporan as $riwayat)
                        @php
                            $rLabaPositif = (float) $riwayat->laba_bersih >= 0;
                            $rRasio =
                                (float) $riwayat->omzet > 0
                                    ? round(((float) $riwayat->biaya_operasional / (float) $riwayat->omzet) * 100, 1)
                                    : 100;
                        @endphp
                        <div class="px-4 py-3" style="border-bottom:1px solid #f6f7fb;">
                            <div class="d-flex align-items-center justify-content-between mb-1 flex-wrap gap-2">
                                <div>
                                    <span class="badge-soft tint-indigo" style="font-size:.72rem;">
                                        Ke-{{ $riwayat->periode_ke }}
                                    </span>
                                    <span style="font-size:.76rem; color:var(--text-muted); margin-left:.5rem;">
                                        {{ $riwayat->tanggal_mulai->format('d/m/Y') }} –
                                        {{ $riwayat->tanggal_selesai->format('d/m/Y') }}
                                    </span>
                                </div>
                                @if ($riwayat->validasi)
                                    <span class="badge-soft tint-{{ $riwayat->validasi->hasil_evaluasi_tint }}"
                                        style="font-size:.7rem;">
                                        {{ $riwayat->validasi->hasil_evaluasi_label }}
                                    </span>
                                @endif
                            </div>
                            <div class="row g-2">
                                <div class="col-4">
                                    <div style="font-size:.7rem; color:var(--text-muted); font-weight:600;">Omzet</div>
                                    <div style="font-size:.82rem; font-weight:700; color:#047857;">
                                        Rp {{ number_format($riwayat->omzet, 0, ',', '.') }}
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div style="font-size:.7rem; color:var(--text-muted); font-weight:600;">Biaya</div>
                                    <div style="font-size:.82rem; color:var(--text-muted);">
                                        Rp {{ number_format($riwayat->biaya_operasional, 0, ',', '.') }}
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div style="font-size:.7rem; color:var(--text-muted); font-weight:600;">Rasio Biaya
                                    </div>
                                    <div
                                        style="font-size:.82rem; font-weight:700; color:{{ $rRasio < 60 ? '#047857' : '#be123c' }};">
                                        {{ $rRasio }}%
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="theme-empty-state" style="padding:1.6rem 1rem;">
                            <div class="empty-icon" style="width:52px; height:52px; font-size:1.4rem;">
                                <i class="bi bi-journal-x"></i>
                            </div>
                            <h6 style="font-size:.86rem;">Ini laporan periode pertama</h6>
                            <p style="font-size:.76rem;">Belum ada laporan sebelumnya untuk usaha ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>{{-- End kolom kanan --}}
    </div>{{-- End row --}}

    @if ($bolehDiproses)
        {{-- ═══════════════════════════════════════════════════════════
     MODAL SETUJUI
═══════════════════════════════════════════════════════════ --}}
        <div class="modal fade" id="modalSetujui" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content"
                    style="border-radius:var(--radius-lg); border:none;
                    max-height:calc(100vh - 2rem);
                    box-shadow:0 20px 60px rgba(15,23,42,.2);">
                    <div class="modal-header"
                        style="border-bottom:1px solid var(--border-soft);
                        padding:1.25rem 1.5rem;">
                        <h5 class="modal-title" style="font-weight:800; color:var(--text-dark); font-size:1rem;">
                            <i class="bi bi-check-circle-fill me-2 text-success"></i>Konfirmasi Persetujuan
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('pengurus.validasi-laporan.setujui', $laporan->id) }}" method="POST"
                        id="formSetujui">
                        @csrf
                        <div class="modal-body" style="padding:1.5rem; overflow-y:auto;">

                            {{-- Preview ringkas --}}
                            <div class="mb-3 p-3 text-center"
                                style="background:linear-gradient(135deg,#ecfdf5,#d1fae5);
                                border-radius:var(--radius-md);">
                                <div style="font-size:.78rem; color:#047857; font-weight:600; margin-bottom:.3rem;">
                                    Prediksi hasil evaluasi:
                                </div>
                                <span class="badge-soft tint-{{ $prediksiTint }}" style="font-size:.88rem;">
                                    {{ $prediksiLabel }}
                                </span>
                                <div style="font-size:.74rem; color:#065f46; margin-top:.3rem;">
                                    Indikator Omzet: {{ $indikatorOmzet ? '✓' : '✗' }}
                                    &nbsp;|&nbsp;
                                    Indikator Biaya: {{ $indikatorBiaya ? '✓' : '✗' }}
                                </div>
                            </div>

                            <p style="font-size:.85rem; color:var(--text-muted); margin-bottom:1rem;">
                                Anda akan menyetujui laporan periode ke-<strong>{{ $laporan->periode_ke }}</strong>
                                usaha <strong>{{ $laporan->usaha->nama_usaha }}</strong>.
                                Setelah laporan disetujui, sistem akan menjalankan evaluasi otomatis
                                berdasarkan indikator monitoring dan memperbarui status anggota.
                            </p>

                            <div class="mb-1">
                                <label for="catatanSetujui" class="form-label">
                                    Catatan Pengurus <span
                                        style="font-size:.74rem; color:var(--text-muted);">(Opsional)</span>
                                </label>
                                <textarea name="catatan_pengurus" id="catatanSetujui" rows="3"
                                    class="form-control @error('catatan_pengurus', 'setujui') is-invalid @enderror"
                                    placeholder="Tambahkan catatan untuk anggota (opsional)..." maxlength="1000">{{ old('catatan_pengurus') }}</textarea>
                                @error('catatan_pengurus', 'setujui')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer"
                            style="border-top:1px solid var(--border-soft); padding:1rem 1.5rem; background:#fff; position:sticky; bottom:0; z-index:2;">
                            <button type="button" class="btn-theme-light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn-theme-primary" id="btnKonfirmasiSetujui">
                                <span id="setujuiText" class="d-flex align-items-center gap-2">
                                    <i class="bi bi-check-circle-fill"></i> Setujui Laporan
                                </span>
                                <span id="setujuiLoading" class="d-none align-items-center gap-2">
                                    <span class="spinner-theme"></span> Memproses...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
     MODAL TOLAK
═══════════════════════════════════════════════════════════ --}}
        <div class="modal fade" id="modalTolak" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content"
                    style="border-radius:var(--radius-lg); border:none;
                    max-height:calc(100vh - 2rem);
                    box-shadow:0 20px 60px rgba(15,23,42,.2);">
                    <div class="modal-header"
                        style="border-bottom:1px solid var(--border-soft);
                        padding:1.25rem 1.5rem;">
                        <h5 class="modal-title" style="font-weight:800; color:var(--text-dark); font-size:1rem;">
                            <i class="bi bi-x-circle-fill me-2 text-danger"></i>Konfirmasi Penolakan
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('pengurus.validasi-laporan.tolak', $laporan->id) }}" method="POST"
                        id="formTolak">
                        @csrf
                        <div class="modal-body" style="padding:1.5rem; overflow-y:auto;">
                            <div class="mb-3 p-3"
                                style="background:linear-gradient(135deg,#fff1f2,#ffe4e6);
                                border-radius:var(--radius-md); border-left:4px solid #f43f5e;">
                                <div style="font-size:.82rem; color:#be123c;">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                    Laporan akan ditolak. Anggota akan mendapat notifikasi dan diminta mengirim ulang.
                                </div>
                            </div>

                            <div class="mb-1">
                                <label for="catatanTolak" class="form-label">
                                    Alasan Penolakan <span class="text-danger">*</span>
                                </label>
                                <textarea name="catatan_pengurus" id="catatanTolak" rows="4"
                                    class="form-control @error('catatan_pengurus', 'tolak') is-invalid @enderror"
                                    placeholder="Jelaskan alasan penolakan laporan ini... (wajib diisi)" minlength="5" maxlength="1000" required>{{ old('catatan_pengurus') }}</textarea>
                                @error('catatan_pengurus', 'tolak')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer"
                            style="border-top:1px solid var(--border-soft); padding:1rem 1.5rem; background:#fff; position:sticky; bottom:0; z-index:2;">
                            <button type="button" class="btn-theme-light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn-theme-danger" id="btnKonfirmasiTolak">
                                <span id="tolakText" class="d-flex align-items-center gap-2">
                                    <i class="bi bi-x-circle-fill"></i> Ya, Tolak Laporan
                                </span>
                                <span id="tolakLoading" class="d-none align-items-center gap-2">
                                    <span class="spinner-theme"></span> Memproses...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

@endsection


@push('styles')
    <style>
        /*
                 * Hotfix modal:
                 * layout utama memiliki stacking context sendiri sehingga backdrop
                 * dapat berada di atas modal. Nilai ini memastikan modal selalu
                 * berada di atas backdrop dan tetap dapat diklik.
                 */
        .modal-backdrop {
            z-index: 19990 !important;
        }

        .modal {
            z-index: 20000 !important;
            pointer-events: auto !important;
        }

        .modal-dialog,
        .modal-content {
            pointer-events: auto !important;
        }

        body.modal-open .theme-loading-overlay {
            display: none !important;
            pointer-events: none !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            /*
             * Bootstrap menambahkan backdrop langsung ke body.
             * Modal juga dipindahkan ke body agar tidak tertahan oleh
             * stacking context, transform, atau overflow dari layout.
             */
            ['modalSetujui', 'modalTolak'].forEach(function(modalId) {
                const modalElement = document.getElementById(modalId);

                if (modalElement && modalElement.parentElement !== document.body) {
                    document.body.appendChild(modalElement);
                }
            });

            function bindSubmit(formId, buttonId, textId, loadingId) {
                const form = document.getElementById(formId);
                const button = document.getElementById(buttonId);
                const text = document.getElementById(textId);
                const loading = document.getElementById(loadingId);

                form?.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        form.reportValidity();
                        return;
                    }

                    if (button?.disabled) {
                        event.preventDefault();
                        return;
                    }

                    if (button) button.disabled = true;
                    text?.classList.add('d-none');
                    loading?.classList.remove('d-none');
                    loading?.classList.add('d-flex');
                });
            }

            bindSubmit(
                'formSetujui',
                'btnKonfirmasiSetujui',
                'setujuiText',
                'setujuiLoading'
            );

            bindSubmit(
                'formTolak',
                'btnKonfirmasiTolak',
                'tolakText',
                'tolakLoading'
            );

            document.querySelectorAll('.modal').forEach(function(modalElement) {
                modalElement.addEventListener('shown.bs.modal', function() {
                    modalElement.style.zIndex = '20000';

                    document.querySelectorAll('.modal-backdrop')
                        .forEach(function(backdrop) {
                            backdrop.style.zIndex = '19990';
                        });

                    const firstInput = modalElement.querySelector(
                        'textarea, input, button[type="submit"]'
                    );

                    firstInput?.focus();
                });

                modalElement.addEventListener('hidden.bs.modal', function() {
                    setTimeout(function() {
                        if (!document.querySelector('.modal.show')) {
                            document.querySelectorAll('.modal-backdrop')
                                .forEach(function(backdrop) {
                                    backdrop.remove();
                                });

                            document.body.classList.remove('modal-open');
                            document.body.style.removeProperty('overflow');
                            document.body.style.removeProperty('padding-right');
                        }
                    }, 50);
                });
            });

            @if ($errors->setujui->any())
                bootstrap.Modal
                    .getOrCreateInstance(document.getElementById('modalSetujui'))
                    .show();
            @endif

            @if ($errors->tolak->any())
                bootstrap.Modal
                    .getOrCreateInstance(document.getElementById('modalTolak'))
                    .show();
            @endif

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: @json(session('success')),
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

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: @json(session('error')),
                    buttonsStyling: false,
                    customClass: {
                        popup: 'swal-theme-popup',
                        title: 'swal-theme-title',
                        htmlContainer: 'swal-theme-html',
                        confirmButton: 'swal-theme-deny'
                    }
                });
            @endif
        });
    </script>
@endpush
