@extends('layouts.app')

@section('title', 'Detail Evaluasi')
@section('page-title', 'Detail Hasil Evaluasi 14 Hari')
@section('page-subtitle', $validasi->laporan->usaha->nama_usaha . ' — Periode ke-' . $validasi->laporan->periode_ke)

@section('sidebar-menu')
    @include('admin.partials._sidebar')
@endsection

@section('content')

    <div class="mb-3">
        <a href="{{ route('admin.evaluasi.index') }}" class="btn-theme-light" style="padding:.4rem .9rem; font-size:.83rem;">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @php
        $laporan = $validasi->laporan;
        $usaha = $laporan->usaha;
        $anggota = $usaha->anggota;
        $labaPos = (float) $laporan->laba_bersih >= 0;
    @endphp

    <div class="row g-3">

        {{-- ── Kolom Kiri ──────────────────────────────────────────── --}}
        <div class="col-12 col-lg-4">

            {{-- Hasil Evaluasi Card --}}
            @php
                $eTint = $validasi->hasil_evaluasi_tint;
                $eGradient = match ($validasi->hasil_evaluasi) {
                    'aktif' => 'linear-gradient(120deg, #047857, #10b981)',
                    'peringatan' => 'linear-gradient(120deg, #b45309, #f59e0b)',
                    'tidak_aktif' => 'linear-gradient(120deg, #be123c, #f43f5e)',
                    default => 'linear-gradient(120deg, var(--primary), #818cf8)',
                };
            @endphp
            <div class="card mb-3" style="overflow:hidden;">
                <div
                    style="background:{{ $eGradient }}; padding:1.75rem; text-align:center; position:relative; overflow:hidden;">
                    <div
                        style="position:absolute; width:120px; height:120px; background:rgba(255,255,255,.1); border-radius:50%; top:-50px; right:-30px;">
                    </div>
                    <div style="font-size:2.8rem; margin-bottom:.4rem; position:relative; z-index:1;">
                        {{ match ($validasi->hasil_evaluasi) {'aktif' => '✅','peringatan' => '⚠️','tidak_aktif' => '❌',default => '—'} }}
                    </div>
                    <div style="font-weight:900; color:#fff; font-size:1.3rem; position:relative; z-index:1;">
                        {{ $validasi->hasil_evaluasi_label }}
                    </div>
                    <div
                        style="font-size:.78rem; color:rgba(255,255,255,.85); margin-top:.3rem; position:relative; z-index:1;">
                        Hasil Evaluasi 14 Hari
                    </div>
                </div>
            </div>

            {{-- Indikator Detail --}}
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-bar-chart-fill me-2" style="color:var(--primary);"></i>Detail Indikator
                </div>
                <div class="card-body d-flex flex-column gap-3">

                    {{-- Indikator Omzet --}}
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <div style="font-size:.82rem; font-weight:700; color:var(--text-dark);">
                                <i class="bi bi-graph-up me-1"></i> Kenaikan Omzet
                            </div>
                            <span class="badge-soft {{ $validasi->indikator_omzet ? 'tint-emerald' : 'tint-rose' }}"
                                style="font-size:.72rem;">
                                {{ $validasi->indikator_omzet ? '✓ Terpenuhi' : '✗ Tidak Terpenuhi' }}
                            </span>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <div
                                    style="background:var(--bg-soft); border-radius:var(--radius-sm); padding:.6rem; text-align:center;">
                                    <div
                                        style="font-size:.68rem; color:var(--text-muted); font-weight:600; margin-bottom:.2rem;">
                                        Sebelumnya</div>
                                    <div style="font-size:.82rem; font-weight:700;">
                                        @if ($validasi->omzet_sebelumnya > 0)
                                            Rp {{ number_format($validasi->omzet_sebelumnya, 0, ',', '.') }}
                                        @else
                                            <span style="color:var(--text-light); font-size:.76rem;">Pertama kali</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div
                                    style="background:var(--bg-soft); border-radius:var(--radius-sm); padding:.6rem; text-align:center;">
                                    <div
                                        style="font-size:.68rem; color:var(--text-muted); font-weight:600; margin-bottom:.2rem;">
                                        Sekarang</div>
                                    <div style="font-size:.82rem; font-weight:700; color:#047857;">
                                        Rp {{ number_format($laporan->omzet, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-2"
                            style="background:{{ $validasi->indikator_omzet ? 'linear-gradient(135deg,#ecfdf5,#d1fae5)' : 'linear-gradient(135deg,#fff1f2,#ffe4e6)' }};
                                border-radius:var(--radius-sm); padding:.5rem;
                                font-size:.88rem; font-weight:800;
                                color:{{ $validasi->indikator_omzet ? '#047857' : '#be123c' }};">
                            {{ $validasi->persentase_kenaikan_omzet >= 0 ? '+' : '' }}{{ $validasi->persentase_kenaikan_omzet }}%
                            <span style="font-size:.7rem; font-weight:600; opacity:.8;">(target ≥ 5%)</span>
                        </div>
                    </div>

                    {{-- Indikator Biaya --}}
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <div style="font-size:.82rem; font-weight:700; color:var(--text-dark);">
                                <i class="bi bi-pie-chart-fill me-1"></i> Rasio Biaya Operasional
                            </div>
                            <span class="badge-soft {{ $validasi->indikator_biaya ? 'tint-emerald' : 'tint-rose' }}"
                                style="font-size:.72rem;">
                                {{ $validasi->indikator_biaya ? '✓ Terpenuhi' : '✗ Tidak Terpenuhi' }}
                            </span>
                        </div>
                        <div class="text-center"
                            style="background:{{ $validasi->indikator_biaya ? 'linear-gradient(135deg,#ecfdf5,#d1fae5)' : 'linear-gradient(135deg,#fff1f2,#ffe4e6)' }};
                                border-radius:var(--radius-sm); padding:.6rem;
                                font-size:.92rem; font-weight:800;
                                color:{{ $validasi->indikator_biaya ? '#047857' : '#be123c' }};">
                            {{ $validasi->rasio_biaya }}% dari omzet
                            <div style="font-size:.7rem; font-weight:600; opacity:.8; margin-top:.2rem;">(target &lt; 60%)
                            </div>
                        </div>
                        <div class="progress-soft mt-2">
                            <div class="progress-bar"
                                style="width:{{ min($validasi->rasio_biaya, 100) }}%;
                                    background:{{ $validasi->indikator_biaya ? 'linear-gradient(90deg,#10b981,#34d399)' : 'linear-gradient(90deg,#f43f5e,#fb7185)' }};">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-1" style="font-size:.68rem; color:var(--text-light);">
                            <span>0%</span>
                            <span style="color:var(--warning); font-weight:700;">Batas 60%</span>
                            <span>100%</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Validator Info --}}
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-people-fill me-2" style="color:var(--accent);"></i>Validator
                </div>
                <div class="card-body p-0">
                    @if ($validasi->validator)
                        <div class="d-flex align-items-center gap-2 px-3 py-3" style="border-bottom:1px solid #f6f7fb;">
                            <img src="{{ $validasi->validator->foto_url }}" class="avatar-sm" alt="">
                            <div>
                                <div style="font-size:.72rem; color:var(--text-muted); font-weight:600;">Pengurus</div>
                                <div style="font-size:.83rem; font-weight:700;">{{ $validasi->validator->nama }}</div>
                                @if ($validasi->tanggal_validasi_pengurus)
                                    <div style="font-size:.72rem; color:var(--text-muted);">
                                        {{ $validasi->tanggal_validasi_pengurus->format('d/m/Y H:i') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                    @if ($validasi->dps)
                        <div class="d-flex align-items-center gap-2 px-3 py-3">
                            <img src="{{ $validasi->dps->foto_url }}" class="avatar-sm" alt="">
                            <div>
                                <div style="font-size:.72rem; color:var(--text-muted); font-weight:600;">Dewan Pengawas
                                    Syariah</div>
                                <div style="font-size:.83rem; font-weight:700;">{{ $validasi->dps->nama }}</div>
                                @if ($validasi->tanggal_validasi_dps)
                                    <div style="font-size:.72rem; color:var(--text-muted);">
                                        {{ $validasi->tanggal_validasi_dps->format('d/m/Y H:i') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Kolom Kanan ─────────────────────────────────────────── --}}
        <div class="col-12 col-lg-8">

            {{-- Data Anggota & Usaha --}}
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-person-badge-fill me-2" style="color:var(--accent);"></i>Anggota &amp;
                        Usaha</span>
                </div>
                <div class="card-body p-0">
                    <div class="akun-info-row px-4">
                        <div class="akun-info-icon tint-indigo"><i class="bi bi-person-fill"></i></div>
                        <div>
                            <div class="akun-info-label">Nama Anggota</div>
                            <div class="akun-info-value">{{ $anggota->akun->nama }}</div>
                        </div>
                    </div>
                    <div class="akun-info-row px-4">
                        <div class="akun-info-icon tint-violet"><i class="bi bi-hash"></i></div>
                        <div>
                            <div class="akun-info-label">Nomor Anggota</div>
                            <div class="akun-info-value"><span class="code-tag">{{ $anggota->nomor_anggota }}</span></div>
                        </div>
                    </div>
                    <div class="akun-info-row px-4">
                        <div class="akun-info-icon tint-cyan"><i class="bi bi-shop-window"></i></div>
                        <div>
                            <div class="akun-info-label">Nama Usaha</div>
                            <div class="akun-info-value">{{ $usaha->nama_usaha }}</div>
                        </div>
                    </div>
                    @if ($usaha->jenis_akad)
                        <div class="akun-info-row px-4">
                            <div class="akun-info-icon tint-violet"><i class="bi bi-patch-check-fill"></i></div>
                            <div>
                                <div class="akun-info-label">Jenis Akad</div>
                                <div class="akun-info-value">
                                    <span
                                        class="badge-soft tint-{{ $usaha->jenis_akad_tint }}">{{ $usaha->jenis_akad }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="akun-info-row px-4" style="border-bottom:none;">
                        <div class="akun-info-icon tint-{{ $usaha->status_validasi_syariah_tint }}">
                            <i class="bi {{ $usaha->status_validasi_syariah_icon }}"></i>
                        </div>
                        <div>
                            <div class="akun-info-label">Validasi Syariah</div>
                            <div class="akun-info-value">
                                <span class="badge-soft tint-{{ $usaha->status_validasi_syariah_tint }}">
                                    {{ $usaha->status_validasi_syariah_label }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Data Laporan --}}
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-journal-richtext me-2" style="color:var(--primary);"></i>
                    Data Laporan Periode ke-{{ $laporan->periode_ke }}
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6 col-sm-3">
                            <div
                                style="background:linear-gradient(135deg,#ecfdf5,#d1fae5); border-radius:var(--radius-md); padding:.9rem; text-align:center;">
                                <div
                                    style="font-size:.68rem; font-weight:700; color:#047857; text-transform:uppercase; margin-bottom:.25rem;">
                                    Omzet</div>
                                <div style="font-size:.9rem; font-weight:800; color:#047857;">
                                    Rp {{ number_format($laporan->omzet / 1000000, 1) }}jt
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div
                                style="background:linear-gradient(135deg,#fff1f2,#ffe4e6); border-radius:var(--radius-md); padding:.9rem; text-align:center;">
                                <div
                                    style="font-size:.68rem; font-weight:700; color:#be123c; text-transform:uppercase; margin-bottom:.25rem;">
                                    Biaya</div>
                                <div style="font-size:.9rem; font-weight:800; color:#be123c;">
                                    Rp {{ number_format($laporan->biaya_operasional / 1000000, 1) }}jt
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div
                                style="background:{{ $labaPos ? 'linear-gradient(135deg,#eef2ff,#e0e7ff)' : 'linear-gradient(135deg,#fff1f2,#ffe4e6)' }}; border-radius:var(--radius-md); padding:.9rem; text-align:center; border:2px solid {{ $labaPos ? '#c7d2fe' : '#fecaca' }};">
                                <div
                                    style="font-size:.68rem; font-weight:700; color:{{ $labaPos ? '#4338ca' : '#be123c' }}; text-transform:uppercase; margin-bottom:.25rem;">
                                    Laba</div>
                                <div
                                    style="font-size:.9rem; font-weight:800; color:{{ $labaPos ? '#4338ca' : '#be123c' }};">
                                    <i class="bi bi-arrow-{{ $labaPos ? 'up' : 'down' }}-circle-fill"
                                        style="font-size:.8rem;"></i>
                                    Rp {{ number_format(abs($laporan->laba_bersih) / 1000000, 1) }}jt
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div
                                style="background:var(--bg-soft); border-radius:var(--radius-md); padding:.9rem; text-align:center;">
                                <div
                                    style="font-size:.68rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; margin-bottom:.25rem;">
                                    Tanggal</div>
                                <div style="font-size:.78rem; font-weight:700; color:var(--text-dark);">
                                    {{ \Carbon\Carbon::parse($laporan->tanggal_mulai)->format('d/m') }}
                                    – {{ \Carbon\Carbon::parse($laporan->tanggal_selesai)->format('d/m/Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Catatan --}}
            @if ($validasi->catatan_pengurus || ($validasi->dps && $usaha->validasiAkadTerakhir?->catatan_dps))
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-chat-left-text-fill me-2" style="color:var(--secondary);"></i>Catatan Validator
                    </div>
                    <div class="card-body d-flex flex-column gap-3">
                        @if ($validasi->catatan_pengurus)
                            <div>
                                <div
                                    style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.3px; color:var(--text-muted); margin-bottom:.4rem;">
                                    <i class="bi bi-person-gear me-1"></i>Catatan Pengurus
                                </div>
                                <div
                                    style="background:var(--bg-soft); border-radius:var(--radius-sm); padding:.75rem; font-size:.84rem; line-height:1.6; color:var(--text-dark); border-left:3px solid var(--primary);">
                                    {{ $validasi->catatan_pengurus }}
                                </div>
                            </div>
                        @endif
                        @if ($usaha->validasiAkadTerakhir?->catatan_dps)
                            <div>
                                <div
                                    style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.3px; color:var(--text-muted); margin-bottom:.4rem;">
                                    <i class="bi bi-shield-check me-1"></i>Catatan DPS
                                </div>
                                <div
                                    style="background:var(--bg-soft); border-radius:var(--radius-sm); padding:.75rem; font-size:.84rem; line-height:1.6; color:var(--text-dark); border-left:3px solid var(--success);">
                                    {{ $usaha->validasiAkadTerakhir->catatan_dps }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection
