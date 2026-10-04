@extends('layouts.app')

@section('title', 'Dashboard Anggota')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Pantau perkembangan usaha Anda')

@section('sidebar-menu')
    @include('anggota.partials._sidebar')
@endsection

@section('content')

    {{-- Welcome Banner --}}
    <div class="welcome-banner d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h5 class="mb-1">
                <i class="bi bi-hand-wave me-1"></i> Assalamualaikum, {{ Auth::user()->nama }} 👋
            </h5>
            <p class="mb-0">
                {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y — H:i') }} WIB
            </p>
        </div>
        <span class="role-pill">
            <i class="bi bi-person-badge-fill me-1"></i>
            @if ($anggota)
                {{ $anggota->nomor_anggota }}
            @else
                Anggota
            @endif
        </span>
    </div>

    {{-- ── Jika belum punya data anggota ──────────────────────── --}}
    @if (!$anggota)
        <div class="theme-empty-state" style="padding:4rem 1.5rem;">
            <div class="empty-icon"><i class="bi bi-person-x"></i></div>
            <h6>Data anggota belum terdaftar</h6>
            <p>Akun Anda belum memiliki data anggota. Silakan hubungi Admin untuk mendaftarkan data Anda.</p>
        </div>
    @else
        @php
            $bolehInputLaporan =
                $usahaAktif &&
                $usahaAktif->status_usaha === 'aktif' &&
                $usahaAktif->status_validasi_syariah === 'disetujui';
        @endphp

        {{-- ════════════════════════════════════════════════════════════
     Kartu Status Utama (4 kartu)
════════════════════════════════════════════════════════════ --}}
        <div class="section-title"><i class="bi bi-bar-chart-line-fill"></i> Status Keanggotaan</div>
        <div class="row g-3 mb-4">

            {{-- Status Anggota --}}
            @php
                $sTint = match ($anggota->status_anggota) {
                    'aktif' => 'emerald',
                    'peringatan' => 'amber',
                    default => 'rose',
                };
                $sIcon = match ($anggota->status_anggota) {
                    'aktif' => 'bi-check-circle-fill',
                    'peringatan' => 'bi-exclamation-circle-fill',
                    default => 'bi-x-circle-fill',
                };
            @endphp
            <div class="col-6 col-md-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="stat-label">Status Anggota</div>
                                <div class="stat-value text-{{ $sTint }}" style="font-size:1.3rem;">
                                    {{ $anggota->status_anggota_label }}
                                </div>
                                <div class="stat-sub">Keanggotaan koperasi</div>
                            </div>
                            <div class="stat-icon tint-{{ $sTint }}"><i class="bi {{ $sIcon }}"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Jenis Akad --}}
            <div class="col-6 col-md-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="stat-label">Jenis Akad</div>
                                <div class="stat-value text-violet" style="font-size:1.1rem;">
                                    {{ $usahaAktif?->jenis_akad_label ?? 'Belum Ada' }}
                                </div>
                                <div class="stat-sub">Akad syariah usaha</div>
                            </div>
                            <div class="stat-icon tint-violet"><i class="bi bi-patch-check-fill"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Laporan --}}
            <div class="col-6 col-md-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="stat-label">Total Laporan</div>
                                <div class="stat-value text-indigo">{{ number_format($totalLaporan) }}</div>
                                <div class="stat-sub">Semua periode</div>
                            </div>
                            <div class="stat-icon tint-indigo"><i class="bi bi-journal-richtext"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Periode Berikutnya --}}
            <div class="col-6 col-md-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="stat-label">Periode Berikutnya</div>
                                <div class="stat-value text-cyan">ke-{{ $periodeBerikutnya }}</div>
                                <div class="stat-sub">Laporan selanjutnya</div>
                            </div>
                            <div class="stat-icon tint-cyan"><i class="bi bi-calendar2-check-fill"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════════
     Dua Kolom Utama
════════════════════════════════════════════════════════════ --}}
        <div class="row g-3 mb-4">

            {{-- ── Kinerja Usaha Terkini ─────────────────────────────── --}}
            <div class="col-12 col-lg-7">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <span>
                            <i class="bi bi-graph-up-arrow me-2" style="color:var(--primary);"></i>
                            Kinerja Usaha Terkini
                        </span>
                        @if ($usahaAktif)
                            <span class="badge-soft tint-{{ $usahaAktif->jenis_usaha_tint }}" style="font-size:.72rem;">
                                {{ $usahaAktif->nama_usaha }}
                            </span>
                        @endif
                    </div>
                    <div class="card-body">
                        @if ($laporanTerakhir)
                            <div class="row g-3 mb-3">
                                <div class="col-4">
                                    <div
                                        style="background:linear-gradient(135deg,#ecfdf5,#d1fae5); border-radius:var(--radius-md); padding:.9rem; text-align:center;">
                                        <div
                                            style="font-size:.68rem; font-weight:700; text-transform:uppercase; color:#047857; margin-bottom:.3rem;">
                                            Omzet</div>
                                        <div style="font-size:.95rem; font-weight:800; color:#047857;">
                                            Rp {{ number_format($laporanTerakhir->omzet / 1000000, 1) }}jt
                                        </div>
                                        <div style="font-size:.68rem; color:#047857; opacity:.7;">Periode
                                            ke-{{ $laporanTerakhir->periode_ke }}</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        style="background:linear-gradient(135deg,#fff1f2,#ffe4e6); border-radius:var(--radius-md); padding:.9rem; text-align:center;">
                                        <div
                                            style="font-size:.68rem; font-weight:700; text-transform:uppercase; color:#be123c; margin-bottom:.3rem;">
                                            Biaya</div>
                                        <div style="font-size:.95rem; font-weight:800; color:#be123c;">
                                            Rp {{ number_format($laporanTerakhir->biaya_operasional / 1000000, 1) }}jt
                                        </div>
                                        <div style="font-size:.68rem; color:#be123c; opacity:.7;">Operasional</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    @php $labaPos = (float) $laporanTerakhir->laba_bersih >= 0; @endphp
                                    <div
                                        style="background:{{ $labaPos ? 'linear-gradient(135deg,#eef2ff,#e0e7ff)' : 'linear-gradient(135deg,#fff1f2,#ffe4e6)' }}; border-radius:var(--radius-md); padding:.9rem; text-align:center; border:2px solid {{ $labaPos ? '#c7d2fe' : '#fecaca' }};">
                                        <div
                                            style="font-size:.68rem; font-weight:700; text-transform:uppercase; color:{{ $labaPos ? '#4338ca' : '#be123c' }}; margin-bottom:.3rem;">
                                            Laba</div>
                                        <div
                                            style="font-size:.95rem; font-weight:800; color:{{ $labaPos ? '#4338ca' : '#be123c' }};">
                                            <i class="bi bi-arrow-{{ $labaPos ? 'up' : 'down' }}-circle-fill me-1"
                                                style="font-size:.8rem;"></i>
                                            Rp {{ number_format(abs($laporanTerakhir->laba_bersih) / 1000000, 1) }}jt
                                        </div>
                                        <div
                                            style="font-size:.68rem; color:{{ $labaPos ? '#4338ca' : '#be123c' }}; opacity:.7;">
                                            Bersih</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Status laporan & evaluasi --}}
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                <div>
                                    <div
                                        style="font-size:.68rem; color:var(--text-muted); font-weight:600; margin-bottom:.3rem;">
                                        Status Laporan</div>
                                    <span class="badge-soft tint-{{ $laporanTerakhir->status_laporan_tint }}">
                                        <i class="bi {{ $laporanTerakhir->status_laporan_icon }} me-1"></i>
                                        {{ $laporanTerakhir->status_laporan_label }}
                                    </span>
                                </div>
                                @if ($evaluasiTerakhir)
                                    <div>
                                        <div
                                            style="font-size:.68rem; color:var(--text-muted); font-weight:600; margin-bottom:.3rem;">
                                            Hasil Evaluasi</div>
                                        <span class="badge-soft tint-{{ $evaluasiTerakhir->hasil_evaluasi_tint }}">
                                            {{ $evaluasiTerakhir->hasil_evaluasi_label }}
                                        </span>
                                    </div>
                                @endif
                                @if ($usahaAktif)
                                    <div>
                                        <div
                                            style="font-size:.68rem; color:var(--text-muted); font-weight:600; margin-bottom:.3rem;">
                                            Status Syariah</div>
                                        <span class="badge-soft tint-{{ $usahaAktif->status_validasi_syariah_tint }}">
                                            <i class="bi {{ $usahaAktif->status_validasi_syariah_icon }} me-1"></i>
                                            {{ $usahaAktif->status_validasi_syariah_label }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="theme-empty-state" style="padding:2rem 1rem;">
                                <div class="empty-icon" style="width:56px; height:56px; font-size:1.4rem;">
                                    <i class="bi bi-journal-x"></i>
                                </div>
                                <h6 style="font-size:.88rem;">Belum ada laporan</h6>
                                <p style="font-size:.78rem;">Mulai input laporan monitoring 14 hari pertama Anda.</p>
                                @if ($bolehInputLaporan)
                                    <a href="{{ route('anggota.laporan.create', ['usaha_id' => $usahaAktif->id]) }}"
                                        class="btn-theme-primary" style="padding:.4rem .9rem; font-size:.8rem;">
                                        <i class="bi bi-plus-lg me-1"></i> Input Laporan Pertama
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ── Aksi Cepat ───────────────────────────────────────── --}}
            <div class="col-12 col-lg-5">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="bi bi-lightning-charge-fill me-2" style="color:var(--accent);"></i>Aksi Cepat
                    </div>
                    <div class="card-body">
                        <div class="row g-2">

                            <div class="col-6">
                                @if ($bolehInputLaporan)
                                    <a href="{{ route('anggota.laporan.create', ['usaha_id' => $usahaAktif->id]) }}"
                                        style="display:flex; flex-direction:column; align-items:center; justify-content:center;
                                      text-decoration:none; background:linear-gradient(135deg,var(--primary-light),#ede9fe);
                                      border:1.5px solid #c7d2fe; border-radius:var(--radius-md);
                                      padding:1.2rem .9rem; gap:.5rem; text-align:center;
                                      transition:transform .2s, box-shadow .2s;"
                                        onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 20px rgba(99,102,241,.2)';"
                                        onmouseout="this.style.transform=''; this.style.boxShadow='';">
                                        <div
                                            style="width:44px; height:44px; border-radius:12px;
                                            background:linear-gradient(135deg,var(--primary),var(--secondary));
                                            display:flex; align-items:center; justify-content:center;
                                            font-size:1.2rem; color:#fff;
                                            box-shadow:0 6px 14px rgba(99,102,241,.3);">
                                            <i class="bi bi-pencil-square"></i>
                                        </div>
                                        <div style="font-size:.8rem; font-weight:700; color:var(--primary-dark);">
                                            Input Monitoring
                                        </div>
                                    </a>
                                @else
                                    <a href="{{ route('anggota.usaha-saya.index') }}"
                                        title="Usaha harus aktif dan akad disetujui DPS"
                                        style="display:flex; flex-direction:column; align-items:center; justify-content:center;
                                      text-decoration:none; background:var(--bg-soft);
                                      border:1.5px dashed var(--border-soft); border-radius:var(--radius-md);
                                      padding:1.2rem .9rem; gap:.5rem; text-align:center;">
                                        <div
                                            style="width:44px; height:44px; border-radius:12px;
                                            background:#e2e8f0; display:flex; align-items:center;
                                            justify-content:center; font-size:1.2rem; color:#64748b;">
                                            <i class="bi bi-lock-fill"></i>
                                        </div>
                                        <div style="font-size:.8rem; font-weight:700; color:var(--text-muted);">
                                            Monitoring Terkunci
                                        </div>
                                    </a>
                                @endif
                            </div>

                            <div class="col-6">
                                <a href="{{ route('anggota.usaha-saya.index') }}"
                                    style="display:flex; flex-direction:column; align-items:center; justify-content:center;
                                  text-decoration:none; background:linear-gradient(135deg,#ecfdf5,#d1fae5);
                                  border:1.5px solid #a7f3d0; border-radius:var(--radius-md);
                                  padding:1.2rem .9rem; gap:.5rem; text-align:center;
                                  transition:transform .2s, box-shadow .2s;"
                                    onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 20px rgba(16,185,129,.2)';"
                                    onmouseout="this.style.transform=''; this.style.boxShadow='';">
                                    <div
                                        style="width:44px; height:44px; border-radius:12px;
                                        background:linear-gradient(135deg,#10b981,#34d399);
                                        display:flex; align-items:center; justify-content:center;
                                        font-size:1.2rem; color:#fff;
                                        box-shadow:0 6px 14px rgba(16,185,129,.3);">
                                        <i class="bi bi-shop-window"></i>
                                    </div>
                                    <div style="font-size:.8rem; font-weight:700; color:#047857;">Usaha Saya</div>
                                </a>
                            </div>

                            <div class="col-6">
                                <a href="{{ route('anggota.laporan.index') }}"
                                    style="display:flex; flex-direction:column; align-items:center; justify-content:center;
                                  text-decoration:none; background:linear-gradient(135deg,#fffbeb,#fef3c7);
                                  border:1.5px solid #fde68a; border-radius:var(--radius-md);
                                  padding:1.2rem .9rem; gap:.5rem; text-align:center;
                                  transition:transform .2s, box-shadow .2s;"
                                    onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 20px rgba(245,158,11,.2)';"
                                    onmouseout="this.style.transform=''; this.style.boxShadow='';">
                                    <div
                                        style="width:44px; height:44px; border-radius:12px;
                                        background:linear-gradient(135deg,#f59e0b,#fbbf24);
                                        display:flex; align-items:center; justify-content:center;
                                        font-size:1.2rem; color:#fff;
                                        box-shadow:0 6px 14px rgba(245,158,11,.3);">
                                        <i class="bi bi-journal-text"></i>
                                    </div>
                                    <div style="font-size:.8rem; font-weight:700; color:#b45309;">Riwayat Laporan</div>
                                </a>
                            </div>

                            <div class="col-6">
                                <a href="{{ route('notifikasi.index') }}"
                                    style="display:flex; flex-direction:column; align-items:center; justify-content:center;
                                  text-decoration:none; background:linear-gradient(135deg,#fdf2f8,#fce7f3);
                                  border:1.5px solid #f9a8d4; border-radius:var(--radius-md);
                                  padding:1.2rem .9rem; gap:.5rem; text-align:center;
                                  transition:transform .2s, box-shadow .2s;"
                                    onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 20px rgba(236,72,153,.2)';"
                                    onmouseout="this.style.transform=''; this.style.boxShadow='';">
                                    <div
                                        style="width:44px; height:44px; border-radius:12px;
                                        background:linear-gradient(135deg,#ec4899,#f472b6);
                                        display:flex; align-items:center; justify-content:center;
                                        font-size:1.2rem; color:#fff; position:relative;
                                        box-shadow:0 6px 14px rgba(236,72,153,.3);">
                                        <i class="bi bi-bell-fill"></i>
                                        @if ($notifBelumDibaca > 0)
                                            <span
                                                style="position:absolute; top:-4px; right:-4px;
                                                 background:#ef4444; color:#fff; font-size:.55rem;
                                                 width:16px; height:16px; border-radius:50%;
                                                 display:flex; align-items:center; justify-content:center;
                                                 font-weight:800;">
                                                {{ $notifBelumDibaca > 9 ? '9+' : $notifBelumDibaca }}
                                            </span>
                                        @endif
                                    </div>
                                    <div style="font-size:.8rem; font-weight:700; color:#be185d;">Notifikasi</div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (!$bolehInputLaporan)
            <div class="alert border-0 mb-4"
                style="background:linear-gradient(135deg,#fffbeb,#fef3c7);
                    color:#92400e; border-radius:var(--radius-md);">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-info-circle-fill mt-1"></i>
                    <div style="font-size:.8rem; line-height:1.55;">
                        Input monitoring belum tersedia. Pastikan usaha berstatus aktif
                        dan dokumen akad sudah disetujui DPS.
                    </div>
                </div>
            </div>
        @endif

        {{-- ── Info Validasi Syariah (jika ada usaha aktif) ─────────── --}}
        @if ($usahaAktif)
            <div class="section-title"><i class="bi bi-shield-check"></i> Status Validasi Syariah</div>
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3 align-items-center">
                        <div class="col-12 col-md-6">
                            <div class="d-flex align-items-center gap-3">
                                <div class="notif-icon tint-{{ $usahaAktif->status_validasi_syariah_tint }}"
                                    style="width:48px; height:48px; font-size:1.2rem;">
                                    <i class="bi {{ $usahaAktif->status_validasi_syariah_icon }}"></i>
                                </div>
                                <div>
                                    <div style="font-size:.85rem; font-weight:700; color:var(--text-dark);">
                                        {{ $usahaAktif->nama_usaha }}
                                    </div>
                                    <span class="badge-soft tint-{{ $usahaAktif->status_validasi_syariah_tint }}"
                                        style="margin-top:4px; display:inline-block;">
                                        {{ $usahaAktif->status_validasi_syariah_label }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div style="font-size:.8rem; color:var(--text-muted); line-height:1.6;">
                                @if ($usahaAktif->status_validasi_syariah === 'belum_diupload')
                                    <i class="bi bi-exclamation-circle-fill me-1" style="color:var(--warning);"></i>
                                    Dokumen akad belum tersedia. Hubungi Admin untuk usaha pertama atau lengkapi pengajuan
                                    usaha tambahan.
                                @elseif ($usahaAktif->status_validasi_syariah === 'menunggu')
                                    <i class="bi bi-hourglass-split me-1" style="color:var(--warning);"></i>
                                    Dokumen akad sedang dalam review oleh Dewan Pengawas Syariah.
                                @elseif ($usahaAktif->status_validasi_syariah === 'disetujui')
                                    <i class="bi bi-patch-check-fill me-1" style="color:var(--success);"></i>
                                    Dokumen akad usaha Anda telah disetujui oleh DPS. Sistem monitoring berjalan normal.
                                @elseif ($usahaAktif->status_validasi_syariah === 'perlu_revisi')
                                    <i class="bi bi-arrow-repeat me-1" style="color:var(--danger);"></i>
                                    DPS meminta revisi dokumen akad. Periksa notifikasi Anda untuk detail catatan DPS.
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    @endif {{-- end if $anggota --}}

@endsection
