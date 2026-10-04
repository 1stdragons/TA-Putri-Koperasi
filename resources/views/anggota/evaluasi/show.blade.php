@extends('layouts.app')

@section('title', 'Detail Evaluasi')
@section('page-title', 'Detail Hasil Evaluasi')
@section('page-subtitle', $validasi->laporan->usaha->nama_usaha . ' — Periode ke-' . $validasi->laporan->periode_ke)

@section('sidebar-menu')
    @include('anggota.partials._sidebar')
@endsection

@section('content')

    <div class="mb-3">
        <a href="{{ route('anggota.evaluasi.index') }}" class="btn-theme-light" style="padding:.4rem .9rem; font-size:.83rem;">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat Evaluasi
        </a>
    </div>

    @php
        $laporan = $validasi->laporan;
        $usaha = $laporan->usaha;
        $labaPos = (float) $laporan->laba_bersih >= 0;
        $eGradient = match ($validasi->hasil_evaluasi) {
            'aktif' => 'linear-gradient(120deg, #047857, #10b981)',
            'peringatan' => 'linear-gradient(120deg, #b45309, #f59e0b)',
            'tidak_aktif' => 'linear-gradient(120deg, #be123c, #f43f5e)',
            default => 'linear-gradient(120deg, var(--primary), #818cf8)',
        };
        $emoji = match ($validasi->hasil_evaluasi) {
            'aktif' => '✅',
            'peringatan' => '⚠️',
            'tidak_aktif' => '❌',
            default => '—',
        };
    @endphp

    <div class="row g-3">
        <div class="col-12 col-lg-4">

            {{-- Hasil Evaluasi --}}
            <div class="card mb-3" style="overflow:hidden;">
                <div
                    style="background:{{ $eGradient }}; padding:2rem 1.5rem; text-align:center; position:relative; overflow:hidden;">
                    <div
                        style="position:absolute; width:120px; height:120px; background:rgba(255,255,255,.1);
                            border-radius:50%; top:-50px; right:-30px;">
                    </div>
                    <div style="font-size:3rem; margin-bottom:.4rem; position:relative; z-index:1;">{{ $emoji }}
                    </div>
                    <div style="font-weight:900; color:#fff; font-size:1.35rem; position:relative; z-index:1;">
                        {{ $validasi->hasil_evaluasi_label }}
                    </div>
                    <div
                        style="font-size:.78rem; color:rgba(255,255,255,.85); margin-top:.35rem; position:relative; z-index:1;">
                        Hasil Evaluasi Periode ke-{{ $laporan->periode_ke }}
                    </div>
                </div>

                {{-- Penjelasan hasil --}}
                <div class="card-body">
                    @if ($validasi->hasil_evaluasi === 'aktif')
                        <div
                            style="background:linear-gradient(135deg,#ecfdf5,#d1fae5); border-radius:var(--radius-sm);
                                padding:.85rem; font-size:.82rem; color:#047857; line-height:1.6;">
                            <i class="bi bi-trophy-fill me-1"></i>
                            <strong>Selamat!</strong> Kinerja usaha Anda memenuhi kedua indikator evaluasi.
                            Pertahankan dan tingkatkan terus perkembangan usaha Anda.
                        </div>
                    @elseif ($validasi->hasil_evaluasi === 'peringatan')
                        <div
                            style="background:linear-gradient(135deg,#fffbeb,#fef3c7); border-radius:var(--radius-sm);
                                padding:.85rem; font-size:.82rem; color:#92400e; line-height:1.6;">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            <strong>Perhatian!</strong> Salah satu indikator belum terpenuhi.
                            Tingkatkan kinerja usaha Anda agar mencapai target pada periode berikutnya.
                        </div>
                    @else
                        <div
                            style="background:linear-gradient(135deg,#fff1f2,#ffe4e6); border-radius:var(--radius-sm);
                                padding:.85rem; font-size:.82rem; color:#9f1239; line-height:1.6;">
                            <i class="bi bi-heart-pulse-fill me-1"></i>
                            <strong>Perlu perhatian serius.</strong> Kedua indikator tidak terpenuhi.
                            Segera konsultasikan kondisi usaha Anda dengan pengurus koperasi.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Indikator Detail --}}
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-bar-chart-fill me-2" style="color:var(--primary);"></i>Detail Indikator
                </div>
                <div class="card-body d-flex flex-column gap-3">

                    {{-- Indikator 1: Kenaikan Omzet --}}
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div style="font-size:.83rem; font-weight:700; color:var(--text-dark);">
                                <i class="bi bi-graph-up me-1"></i> Kenaikan Omzet
                            </div>
                            <span class="badge-soft {{ $validasi->indikator_omzet ? 'tint-emerald' : 'tint-rose' }}"
                                style="font-size:.72rem;">
                                {{ $validasi->indikator_omzet ? '✓ Terpenuhi' : '✗ Tidak Terpenuhi' }}
                            </span>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <div
                                    style="background:var(--bg-soft); border-radius:var(--radius-sm); padding:.6rem; text-align:center;">
                                    <div
                                        style="font-size:.68rem; color:var(--text-muted); font-weight:600; margin-bottom:.15rem;">
                                        Omzet Periode Lalu</div>
                                    <div style="font-size:.82rem; font-weight:700; color:var(--text-dark);">
                                        @if ($validasi->omzet_sebelumnya > 0)
                                            Rp {{ number_format($validasi->omzet_sebelumnya, 0, ',', '.') }}
                                        @else
                                            <span style="color:var(--text-light); font-size:.78rem;">Pertama kali</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div
                                    style="background:var(--bg-soft); border-radius:var(--radius-sm); padding:.6rem; text-align:center;">
                                    <div
                                        style="font-size:.68rem; color:var(--text-muted); font-weight:600; margin-bottom:.15rem;">
                                        Omzet Periode Ini</div>
                                    <div style="font-size:.82rem; font-weight:700; color:#047857;">
                                        Rp {{ number_format($laporan->omzet, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center"
                            style="background:{{ $validasi->indikator_omzet ? 'linear-gradient(135deg,#ecfdf5,#d1fae5)' : 'linear-gradient(135deg,#fff1f2,#ffe4e6)' }};
                                border-radius:var(--radius-sm); padding:.55rem;
                                font-size:.9rem; font-weight:800;
                                color:{{ $validasi->indikator_omzet ? '#047857' : '#be123c' }};">
                            {{ $validasi->persentase_kenaikan_omzet >= 0 ? '+' : '' }}{{ $validasi->persentase_kenaikan_omzet }}%
                            <span style="font-size:.72rem; font-weight:600; opacity:.8;">&nbsp;(target ≥ 5%)</span>
                        </div>
                    </div>

                    {{-- Indikator 2: Rasio Biaya --}}
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div style="font-size:.83rem; font-weight:700; color:var(--text-dark);">
                                <i class="bi bi-pie-chart-fill me-1"></i> Rasio Biaya Operasional
                            </div>
                            <span class="badge-soft {{ $validasi->indikator_biaya ? 'tint-emerald' : 'tint-rose' }}"
                                style="font-size:.72rem;">
                                {{ $validasi->indikator_biaya ? '✓ Terpenuhi' : '✗ Tidak Terpenuhi' }}
                            </span>
                        </div>

                        <div class="text-center mb-2"
                            style="background:{{ $validasi->indikator_biaya ? 'linear-gradient(135deg,#ecfdf5,#d1fae5)' : 'linear-gradient(135deg,#fff1f2,#ffe4e6)' }};
                                border-radius:var(--radius-sm); padding:.6rem;
                                font-size:.9rem; font-weight:800;
                                color:{{ $validasi->indikator_biaya ? '#047857' : '#be123c' }};">
                            {{ $validasi->rasio_biaya }}% dari omzet
                            <div style="font-size:.7rem; font-weight:600; opacity:.8; margin-top:.2rem;">target &lt; 60%
                            </div>
                        </div>

                        <div class="progress-soft">
                            <div class="progress-bar"
                                style="width:{{ min($validasi->rasio_biaya, 100) }}%;
                                    background:{{ $validasi->indikator_biaya ? 'linear-gradient(90deg,#10b981,#34d399)' : 'linear-gradient(90deg,#f43f5e,#fb7185)' }};">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-1" style="font-size:.68rem; color:var(--text-light);">
                            <span>0% (Ideal)</span>
                            <span style="color:var(--warning); font-weight:700;">60% (Batas)</span>
                            <span>100%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Kolom Kanan ─────────────────────────────────────────── --}}
        <div class="col-12 col-lg-8">

            {{-- Info Laporan & Keuangan --}}
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-journal-richtext me-2" style="color:var(--primary);"></i>
                    Data Laporan Periode ke-{{ $laporan->periode_ke }}
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-3"
                        style="background:var(--bg-soft); border-radius:var(--radius-sm); padding:.6rem .9rem;
                            font-size:.8rem; color:var(--text-muted);">
                        <i class="bi bi-calendar-range me-1"></i>
                        Periode:
                        <strong>{{ \Carbon\Carbon::parse($laporan->tanggal_mulai)->translatedFormat('d F Y') }}</strong>
                        s.d.
                        <strong>{{ \Carbon\Carbon::parse($laporan->tanggal_selesai)->translatedFormat('d F Y') }}</strong>
                        (14 hari)
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-sm-4">
                            <div
                                style="background:linear-gradient(135deg,#ecfdf5,#d1fae5); border-radius:var(--radius-md); padding:1rem; text-align:center;">
                                <div
                                    style="font-size:.7rem; font-weight:700; color:#047857; text-transform:uppercase; margin-bottom:.3rem;">
                                    Omzet Periode Ini</div>
                                <div style="font-size:1.1rem; font-weight:800; color:#047857;">
                                    Rp {{ number_format($laporan->omzet, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div
                                style="background:linear-gradient(135deg,#fff1f2,#ffe4e6); border-radius:var(--radius-md); padding:1rem; text-align:center;">
                                <div
                                    style="font-size:.7rem; font-weight:700; color:#be123c; text-transform:uppercase; margin-bottom:.3rem;">
                                    Biaya Operasional</div>
                                <div style="font-size:1.1rem; font-weight:800; color:#be123c;">
                                    Rp {{ number_format($laporan->biaya_operasional, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div
                                style="background:{{ $labaPos ? 'linear-gradient(135deg,#eef2ff,#e0e7ff)' : 'linear-gradient(135deg,#fff1f2,#ffe4e6)' }};
                                    border-radius:var(--radius-md); padding:1rem; text-align:center;
                                    border:2px solid {{ $labaPos ? '#c7d2fe' : '#fecaca' }};">
                                <div
                                    style="font-size:.7rem; font-weight:700; color:{{ $labaPos ? '#4338ca' : '#be123c' }}; text-transform:uppercase; margin-bottom:.3rem;">
                                    Laba Bersih</div>
                                <div
                                    style="font-size:1.1rem; font-weight:800; color:{{ $labaPos ? '#4338ca' : '#be123c' }};">
                                    <i class="bi bi-arrow-{{ $labaPos ? 'up' : 'down' }}-circle-fill me-1"
                                        style="font-size:.9rem;"></i>
                                    Rp {{ number_format(abs($laporan->laba_bersih), 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Info Usaha --}}
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-shop-window me-2" style="color:var(--primary);"></i>Usaha
                </div>
                <div class="card-body p-0">
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

            {{-- Catatan Pengurus --}}
            @if ($validasi->catatan_pengurus)
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="bi bi-chat-left-text-fill me-2" style="color:var(--secondary);"></i>Catatan Pengurus
                    </div>
                    <div class="card-body">
                        <div
                            style="background:var(--bg-soft); border-radius:var(--radius-sm);
                                padding:.85rem; font-size:.85rem; line-height:1.7; color:var(--text-dark);
                                border-left:3px solid var(--primary);">
                            {{ $validasi->catatan_pengurus }}
                        </div>
                    </div>
                </div>
            @endif

            {{-- Info Validator --}}
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-people-fill me-2" style="color:var(--accent);"></i>Divalidasi Oleh
                </div>
                <div class="card-body p-0">
                    @if ($validasi->validator)
                        <div class="d-flex align-items-center gap-3 px-4 py-3"
                            style="border-bottom:{{ $validasi->dps ? '1px solid #f6f7fb' : 'none' }};">
                            <img src="{{ $validasi->validator->foto_url }}" class="avatar-sm" alt="">
                            <div>
                                <div
                                    style="font-size:.72rem; color:var(--text-muted); font-weight:600; text-transform:uppercase;">
                                    Pengurus</div>
                                <div style="font-size:.85rem; font-weight:700;">{{ $validasi->validator->nama }}</div>
                                @if ($validasi->tanggal_validasi_pengurus)
                                    <div style="font-size:.74rem; color:var(--text-muted);">
                                        <i class="bi bi-calendar-check me-1"></i>
                                        {{ $validasi->tanggal_validasi_pengurus->translatedFormat('d F Y, H:i') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                    @if ($validasi->dps)
                        <div class="d-flex align-items-center gap-3 px-4 py-3">
                            <img src="{{ $validasi->dps->foto_url }}" class="avatar-sm" alt="">
                            <div>
                                <div
                                    style="font-size:.72rem; color:var(--text-muted); font-weight:600; text-transform:uppercase;">
                                    Dewan Pengawas Syariah</div>
                                <div style="font-size:.85rem; font-weight:700;">{{ $validasi->dps->nama }}</div>
                                @if ($validasi->tanggal_validasi_dps)
                                    <div style="font-size:.74rem; color:var(--text-muted);">
                                        <i class="bi bi-calendar-check me-1"></i>
                                        {{ $validasi->tanggal_validasi_dps->translatedFormat('d F Y, H:i') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                    @if (!$validasi->validator && !$validasi->dps)
                        <div class="theme-empty-state" style="padding:1.6rem 1rem;">
                            <div class="empty-icon" style="width:48px; height:48px; font-size:1.2rem;"><i
                                    class="bi bi-person-x"></i></div>
                            <h6 style="font-size:.84rem;">Data validator tidak tersedia</h6>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
