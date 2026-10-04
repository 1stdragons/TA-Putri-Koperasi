@extends('layouts.app')

@section('title', 'Detail Monitoring')
@section('page-title', 'Detail Monitoring Evaluasi')
@section('page-subtitle', $validasi->laporan->usaha->nama_usaha . ' — Periode ke-' . $validasi->laporan->periode_ke)

@section('sidebar-menu')
    @include('ketua.partials._sidebar')
@endsection

@section('content')

    <div class="mb-3">
        <a href="{{ route('ketua.monitoring.index') }}" class="btn-theme-light" style="padding:.4rem .9rem; font-size:.83rem;">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Monitoring
        </a>
    </div>

    @php
        $laporan = $validasi->laporan;
        $usaha = $laporan->usaha;
        $anggota = $usaha->anggota;
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
        $aTint = match ($anggota->status_anggota) {
            'aktif' => 'emerald',
            'peringatan' => 'amber',
            default => 'rose',
        };

        $validasiAkadTerakhir = $usaha->validasiAkadTerakhir;
    @endphp

    <div class="row g-3">

        {{-- ═══════════════════════════════════════════════════════════
     KOLOM KIRI
═══════════════════════════════════════════════════════════ --}}
        <div class="col-12 col-lg-4">

            {{-- F. Hasil Evaluasi --}}
            <div class="card mb-3" style="overflow:hidden;">
                <div
                    style="background:{{ $eGradient }}; padding:1.75rem; text-align:center; position:relative; overflow:hidden;">
                    <div
                        style="position:absolute; width:110px; height:110px; background:rgba(255,255,255,.1);
                        border-radius:50%; top:-45px; right:-25px;">
                    </div>
                    <div style="font-size:2.8rem; margin-bottom:.4rem; position:relative; z-index:1;">{{ $emoji }}
                    </div>
                    <div style="font-weight:900; color:#fff; font-size:1.3rem; position:relative; z-index:1;">
                        {{ $validasi->hasil_evaluasi_label }}
                    </div>
                    <div
                        style="font-size:.78rem; color:rgba(255,255,255,.85); margin-top:.3rem; position:relative; z-index:1;">
                        Hasil Evaluasi 14 Hari — Periode ke-{{ $laporan->periode_ke }}
                    </div>
                </div>

                <div class="card-body d-flex flex-column gap-3">

                    {{-- Omzet Sebelumnya vs Sekarang --}}
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <div style="font-size:.82rem; font-weight:700; color:var(--text-dark);">
                                <i class="bi bi-graph-up me-1"></i> Kenaikan Omzet
                            </div>
                            <span class="badge-soft {{ $validasi->indikator_omzet ? 'tint-emerald' : 'tint-rose' }}"
                                style="font-size:.72rem;">
                                {{ $validasi->indikator_omzet ? '✓ Memenuhi' : '✗ Tidak Memenuhi' }}
                            </span>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <div
                                    style="background:var(--bg-soft); border-radius:var(--radius-sm); padding:.55rem; text-align:center;">
                                    <div
                                        style="font-size:.67rem; color:var(--text-muted); font-weight:600; margin-bottom:.15rem;">
                                        Periode Lalu</div>
                                    <div style="font-size:.8rem; font-weight:700; color:var(--text-dark);">
                                        @if ((float) $validasi->omzet_sebelumnya > 0)
                                            Rp {{ number_format($validasi->omzet_sebelumnya, 0, ',', '.') }}
                                        @else
                                            <span style="font-size:.72rem; color:var(--text-light);">Pertama kali</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div
                                    style="background:var(--bg-soft); border-radius:var(--radius-sm); padding:.55rem; text-align:center;">
                                    <div
                                        style="font-size:.67rem; color:var(--text-muted); font-weight:600; margin-bottom:.15rem;">
                                        Periode Ini</div>
                                    <div style="font-size:.8rem; font-weight:700; color:#047857;">
                                        Rp {{ number_format($laporan->omzet, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center"
                            style="background:{{ $validasi->indikator_omzet ? 'linear-gradient(135deg,#ecfdf5,#d1fae5)' : 'linear-gradient(135deg,#fff1f2,#ffe4e6)' }};
                            border-radius:var(--radius-sm); padding:.5rem;
                            font-size:.9rem; font-weight:800;
                            color:{{ $validasi->indikator_omzet ? '#047857' : '#be123c' }};">
                            {{ $validasi->persentase_kenaikan_omzet >= 0 ? '+' : '' }}{{ $validasi->persentase_kenaikan_omzet }}%
                            <span style="font-size:.7rem; font-weight:600; opacity:.8;">&nbsp;(target ≥ 5%)</span>
                        </div>
                    </div>

                    {{-- Rasio Biaya --}}
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <div style="font-size:.82rem; font-weight:700; color:var(--text-dark);">
                                <i class="bi bi-pie-chart-fill me-1"></i> Rasio Biaya
                            </div>
                            <span class="badge-soft {{ $validasi->indikator_biaya ? 'tint-emerald' : 'tint-rose' }}"
                                style="font-size:.72rem;">
                                {{ $validasi->indikator_biaya ? '✓ Memenuhi' : '✗ Tidak Memenuhi' }}
                            </span>
                        </div>
                        <div class="text-center mb-2"
                            style="background:{{ $validasi->indikator_biaya ? 'linear-gradient(135deg,#ecfdf5,#d1fae5)' : 'linear-gradient(135deg,#fff1f2,#ffe4e6)' }};
                            border-radius:var(--radius-sm); padding:.55rem;
                            font-size:.9rem; font-weight:800;
                            color:{{ $validasi->indikator_biaya ? '#047857' : '#be123c' }};">
                            {{ $validasi->rasio_biaya }}% dari omzet
                            <div style="font-size:.7rem; font-weight:600; opacity:.8; margin-top:.15rem;">target &lt; 60%
                            </div>
                        </div>
                        <div class="progress-soft">
                            <div class="progress-bar"
                                style="width:{{ min($validasi->rasio_biaya, 100) }}%;
                                background:{{ $validasi->indikator_biaya ? 'linear-gradient(90deg,#10b981,#34d399)' : 'linear-gradient(90deg,#f43f5e,#fb7185)' }};">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-1" style="font-size:.67rem; color:var(--text-light);">
                            <span>0%</span>
                            <span style="color:var(--warning); font-weight:700;">60% (batas)</span>
                            <span>100%</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- A. Informasi Anggota --}}
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
                            <div class="akun-info-value"><span class="code-tag">{{ $anggota->nomor_anggota }}</span></div>
                        </div>
                    </div>
                    <div class="akun-info-row px-3">
                        <div class="akun-info-icon tint-slate"><i class="bi bi-credit-card-fill"></i></div>
                        <div>
                            <div class="akun-info-label">NIK</div>
                            <div class="akun-info-value"><span class="code-tag">{{ $anggota->nik }}</span></div>
                        </div>
                    </div>
                    <div class="akun-info-row px-3">
                        <div class="akun-info-icon tint-emerald"><i class="bi bi-telephone-fill"></i></div>
                        <div>
                            <div class="akun-info-label">Telepon</div>
                            <div class="akun-info-value">{{ $anggota->telepon }}</div>
                        </div>
                    </div>
                    <div class="akun-info-row px-3">
                        <div class="akun-info-icon tint-amber"><i class="bi bi-calendar-event-fill"></i></div>
                        <div>
                            <div class="akun-info-label">Tanggal Bergabung</div>
                            <div class="akun-info-value">
                                {{ $anggota->tanggal_bergabung?->translatedFormat('d F Y') ?? '—' }}
                            </div>
                        </div>
                    </div>
                    <div class="akun-info-row px-3" style="border-bottom:none;">
                        <div class="akun-info-icon tint-{{ $aTint }}"><i class="bi bi-toggle-on"></i></div>
                        <div>
                            <div class="akun-info-label">Status Anggota</div>
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
     KOLOM KANAN
═══════════════════════════════════════════════════════════ --}}
        <div class="col-12 col-lg-8">

            {{-- B. Informasi Usaha --}}
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-shop-window me-2" style="color:var(--primary);"></i>Informasi Usaha
                </div>
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-12 col-md-6">
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
                            <div class="akun-info-row px-4">
                                <div class="akun-info-icon tint-violet"><i class="bi bi-patch-check-fill"></i></div>
                                <div>
                                    <div class="akun-info-label">Jenis Akad</div>
                                    <div class="akun-info-value">
                                        @if ($usaha->jenis_akad)
                                            <span
                                                class="badge-soft tint-{{ $usaha->jenis_akad_tint }}">{{ $usaha->jenis_akad }}</span>
                                        @else
                                            <span style="color:var(--text-light);">—</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="akun-info-row px-4">
                                <div class="akun-info-icon tint-{{ $usaha->status_usaha_tint }}"><i
                                        class="bi {{ $usaha->status_usaha_icon }}"></i></div>
                                <div>
                                    <div class="akun-info-label">Status Usaha</div>
                                    <div class="akun-info-value">
                                        <span
                                            class="badge-soft tint-{{ $usaha->status_usaha_tint }}">{{ $usaha->status_usaha_label }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="akun-info-row px-4">
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
                            <div class="akun-info-row px-4">
                                <div class="akun-info-icon tint-amber"><i class="bi bi-calendar-event-fill"></i></div>
                                <div>
                                    <div class="akun-info-label">Tanggal Mulai Usaha</div>
                                    <div class="akun-info-value">
                                        {{ $usaha->tanggal_mulai?->translatedFormat('d F Y') ?? '—' }}
                                    </div>
                                </div>
                            </div>
                            <div class="akun-info-row px-4">
                                <div class="akun-info-icon tint-emerald"><i class="bi bi-cash-stack"></i></div>
                                <div>
                                    <div class="akun-info-label">Modal Awal</div>
                                    <div class="akun-info-value">Rp {{ number_format($usaha->modal_awal, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                            <div class="akun-info-row px-4">
                                <div class="akun-info-icon tint-slate"><i class="bi bi-geo-alt-fill"></i></div>
                                <div>
                                    <div class="akun-info-label">Alamat Usaha</div>
                                    <div class="akun-info-value" style="font-weight:500; line-height:1.5;">
                                        {{ $usaha->alamat_usaha ?: '—' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- C. Informasi Laporan --}}
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-journal-richtext me-2" style="color:var(--primary);"></i>
                    Data Laporan Periode ke-{{ $laporan->periode_ke }}
                </div>
                <div class="card-body">

                    {{-- Periode --}}
                    <div class="d-flex align-items-center gap-2 mb-3"
                        style="background:var(--bg-soft); border-radius:var(--radius-sm); padding:.6rem .9rem; font-size:.8rem;">
                        <i class="bi bi-calendar-range me-1" style="color:var(--primary);"></i>
                        <strong>{{ $laporan->tanggal_mulai->translatedFormat('d F Y') }}</strong>
                        &nbsp;s.d.&nbsp;
                        <strong>{{ $laporan->tanggal_selesai->translatedFormat('d F Y') }}</strong>
                        <span style="color:var(--text-muted);">(14 hari)</span>
                    </div>

                    {{-- Keuangan --}}
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-4">
                            <div
                                style="background:linear-gradient(135deg,#ecfdf5,#d1fae5); border-radius:var(--radius-md); padding:.9rem; text-align:center;">
                                <div
                                    style="font-size:.68rem; font-weight:700; color:#047857; text-transform:uppercase; margin-bottom:.25rem;">
                                    Omzet</div>
                                <div style="font-size:1rem; font-weight:800; color:#047857;">Rp
                                    {{ number_format($laporan->omzet, 0, ',', '.') }}</div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div
                                style="background:linear-gradient(135deg,#fff1f2,#ffe4e6); border-radius:var(--radius-md); padding:.9rem; text-align:center;">
                                <div
                                    style="font-size:.68rem; font-weight:700; color:#be123c; text-transform:uppercase; margin-bottom:.25rem;">
                                    Biaya Operasional</div>
                                <div style="font-size:1rem; font-weight:800; color:#be123c;">Rp
                                    {{ number_format($laporan->biaya_operasional, 0, ',', '.') }}</div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div
                                style="background:{{ $labaPos ? 'linear-gradient(135deg,#eef2ff,#e0e7ff)' : 'linear-gradient(135deg,#fff1f2,#ffe4e6)' }};
                                border-radius:var(--radius-md); padding:.9rem; text-align:center;
                                border:2px solid {{ $labaPos ? '#c7d2fe' : '#fecaca' }};">
                                <div
                                    style="font-size:.68rem; font-weight:700; color:{{ $labaPos ? '#4338ca' : '#be123c' }}; text-transform:uppercase; margin-bottom:.25rem;">
                                    Laba Bersih</div>
                                <div
                                    style="font-size:1rem; font-weight:800; color:{{ $labaPos ? '#4338ca' : '#be123c' }};">
                                    <i class="bi bi-arrow-{{ $labaPos ? 'up' : 'down' }}-circle-fill me-1"
                                        style="font-size:.85rem;"></i>
                                    Rp {{ number_format(abs($laporan->laba_bersih), 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Bukti Laporan --}}
                    <div class="mb-3">
                        <div
                            style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.3px; color:var(--text-muted); margin-bottom:.4rem;">
                            Bukti Laporan
                        </div>
                        @if ($laporan->has_file_bukti)
                            <div class="d-flex align-items-center gap-2"
                                style="background:var(--bg-soft); border-radius:var(--radius-sm); padding:.6rem .9rem;">
                                <i class="bi {{ $laporan->file_bukti_icon }}"
                                    style="font-size:1.4rem; flex-shrink:0;
                                  color:{{ in_array($laporan->file_bukti_extension, ['jpg', 'jpeg', 'png']) ? '#7c3aed' : '#dc2626' }};"></i>
                                <div style="flex:1; min-width:0;">
                                    <div
                                        style="font-size:.78rem; font-weight:700; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                        {{ $laporan->file_bukti }}
                                    </div>
                                </div>
                                <a href="{{ $laporan->file_bukti_url }}" target="_blank" class="btn-theme-light"
                                    style="padding:.3rem .7rem; font-size:.76rem; flex-shrink:0;">
                                    <i class="bi bi-eye me-1"></i>Lihat
                                </a>
                            </div>
                        @else
                            <span style="font-size:.8rem; color:var(--text-light);">Tidak ada bukti laporan.</span>
                        @endif
                    </div>

                    {{-- Catatan Anggota --}}
                    @if ($laporan->keterangan)
                        <div>
                            <div
                                style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.3px; color:var(--text-muted); margin-bottom:.4rem;">
                                Catatan Anggota
                            </div>
                            <div
                                style="background:var(--bg-soft); border-radius:var(--radius-sm); padding:.75rem; font-size:.84rem; line-height:1.6; color:var(--text-dark);">
                                {{ $laporan->keterangan }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- D. Validasi Pengurus --}}
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-journal-check me-2" style="color:var(--primary);"></i>Validasi Administrasi (Pengurus)
                </div>
                <div class="card-body">
                    <div class="row g-3 align-items-start">
                        <div class="col-12 col-sm-6">
                            <div
                                style="font-size:.72rem; color:var(--text-muted); font-weight:700; text-transform:uppercase; margin-bottom:.4rem;">
                                Status</div>
                            <span
                                class="badge-soft {{ $validasi->status_validasi_pengurus === 'disetujui' ? 'tint-emerald' : 'tint-rose' }}">
                                <i
                                    class="bi bi-{{ $validasi->status_validasi_pengurus === 'disetujui' ? 'check' : 'x' }}-circle-fill me-1"></i>
                                {{ $validasi->status_pengurus_label }}
                            </span>
                        </div>
                        @if ($validasi->validator)
                            <div class="col-12 col-sm-6">
                                <div
                                    style="font-size:.72rem; color:var(--text-muted); font-weight:700; text-transform:uppercase; margin-bottom:.4rem;">
                                    Validator</div>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $validasi->validator->foto_url }}" class="avatar-sm" alt="">
                                    <div>
                                        <div style="font-size:.82rem; font-weight:700;">{{ $validasi->validator->nama }}
                                        </div>
                                        @if ($validasi->tanggal_validasi_pengurus)
                                            <div style="font-size:.72rem; color:var(--text-muted);">
                                                {{ $validasi->tanggal_validasi_pengurus->translatedFormat('d F Y, H:i') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if ($validasi->catatan_pengurus)
                            <div class="col-12">
                                <div
                                    style="font-size:.72rem; color:var(--text-muted); font-weight:700; text-transform:uppercase; margin-bottom:.4rem;">
                                    Catatan Pengurus</div>
                                <div
                                    style="background:var(--bg-soft); border-radius:var(--radius-sm); padding:.75rem; font-size:.84rem; line-height:1.6; color:var(--text-dark); border-left:3px solid var(--primary);">
                                    {{ $validasi->catatan_pengurus }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- E. Validasi DPS --}}
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-shield-check me-2" style="color:#047857;"></i>Validasi Syariah (DPS)
                </div>
                <div class="card-body">
                    <div class="row g-3 align-items-start">
                        <div class="col-12 col-sm-6">
                            <div
                                style="font-size:.72rem; color:var(--text-muted); font-weight:700; text-transform:uppercase; margin-bottom:.4rem;">
                                Status Validasi Syariah</div>
                            <span class="badge-soft tint-{{ $usaha->status_validasi_syariah_tint }}">
                                <i class="bi {{ $usaha->status_validasi_syariah_icon }} me-1"></i>
                                {{ $usaha->status_validasi_syariah_label }}
                            </span>
                        </div>
                        @if ($validasiAkadTerakhir?->dps)
                            <div class="col-12 col-sm-6">
                                <div
                                    style="font-size:.72rem; color:var(--text-muted); font-weight:700; text-transform:uppercase; margin-bottom:.4rem;">
                                    Dewan Pengawas Syariah</div>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $validasiAkadTerakhir->dps->foto_url }}" class="avatar-sm"
                                        alt="">
                                    <div>
                                        <div style="font-size:.82rem; font-weight:700;">
                                            {{ $validasiAkadTerakhir->dps->nama }}</div>
                                        @if ($validasiAkadTerakhir->divalidasi_pada)
                                            <div style="font-size:.72rem; color:var(--text-muted);">
                                                {{ $validasiAkadTerakhir->divalidasi_pada->translatedFormat('d F Y, H:i') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if ($validasiAkadTerakhir?->catatan_dps)
                            <div class="col-12">
                                <div
                                    style="font-size:.72rem; color:var(--text-muted); font-weight:700; text-transform:uppercase; margin-bottom:.4rem;">
                                    Catatan DPS</div>
                                <div
                                    style="background:var(--bg-soft); border-radius:var(--radius-sm); padding:.75rem; font-size:.84rem; line-height:1.6; color:var(--text-dark); border-left:3px solid var(--success);">
                                    {{ $validasiAkadTerakhir->catatan_dps }}
                                </div>
                            </div>
                        @endif
                        @if ($usaha->has_file_akad)
                            <div class="col-12">
                                <div
                                    style="font-size:.72rem; color:var(--text-muted); font-weight:700; text-transform:uppercase; margin-bottom:.4rem;">
                                    Dokumen Akad</div>
                                <div class="d-flex align-items-center gap-2"
                                    style="background:var(--bg-soft); border-radius:var(--radius-sm); padding:.6rem .9rem;">
                                    <i class="bi {{ $usaha->file_akad_icon }}"
                                        style="font-size:1.4rem; flex-shrink:0; color:{{ $usaha->file_akad_extension === 'pdf' ? '#dc2626' : '#2563eb' }};"></i>
                                    <div style="flex:1; min-width:0;">
                                        <div
                                            style="font-size:.78rem; font-weight:700; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                            {{ $usaha->file_akad }}
                                        </div>
                                    </div>
                                    <a href="{{ $usaha->file_akad_url }}" target="_blank" class="btn-theme-light"
                                        style="padding:.3rem .7rem; font-size:.76rem; flex-shrink:0;">
                                        <i class="bi bi-eye me-1"></i>Lihat
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>{{-- End kolom kanan --}}
    </div>{{-- End row --}}

@endsection
