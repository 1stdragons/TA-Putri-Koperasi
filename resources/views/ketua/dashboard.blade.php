@extends('layouts.app')

@section('title', 'Dashboard Ketua')
@section('page-title', 'Dashboard Eksekutif')
@section('page-subtitle', 'Ringkasan kondisi koperasi secara menyeluruh')

@section('sidebar-menu')
    @include('ketua.partials._sidebar')
@endsection

@section('content')

    {{-- ── Welcome Banner ─────────────────────────────────────── --}}
    <div class="welcome-banner d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h5 class="mb-1">
                <i class="bi bi-shield-fill-check me-1"></i>
                Selamat Datang, {{ Auth::user()->nama }} 👋
            </h5>
            <p class="mb-0">
                {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y — H:i') }} WIB
            </p>
        </div>
        <span class="role-pill">
            <i class="bi bi-star-fill me-1"></i> Ketua Koperasi
        </span>
    </div>

    {{-- ════════════════════════════════════════════════════════════
     BARIS 1: Statistik Anggota (4 kartu)
════════════════════════════════════════════════════════════ --}}
    <div class="section-title"><i class="bi bi-people-fill"></i> Statistik Anggota</div>
    <div class="row g-3 mb-4">

        <div class="col-6 col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Total Anggota</div>
                            <div class="stat-value text-indigo">{{ number_format($totalAnggota) }}</div>
                            <div class="stat-sub">Terdaftar di sistem</div>
                        </div>
                        <div class="stat-icon tint-indigo"><i class="bi bi-people-fill"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Anggota Aktif</div>
                            <div class="stat-value text-emerald">{{ number_format($anggotaAktif) }}</div>
                            <div class="stat-sub">Kedua indikator terpenuhi</div>
                        </div>
                        <div class="stat-icon tint-emerald"><i class="bi bi-check-circle-fill"></i></div>
                    </div>
                    @if ($totalAnggota > 0)
                        <div class="progress-soft mt-2">
                            <div class="progress-bar"
                                style="width:{{ round(($anggotaAktif / $totalAnggota) * 100) }}%;
                                    background:linear-gradient(90deg,#10b981,#34d399);">
                            </div>
                        </div>
                        <div style="font-size:.7rem; color:var(--text-muted); margin-top:.3rem;">
                            {{ round(($anggotaAktif / $totalAnggota) * 100) }}% dari total
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Anggota Peringatan</div>
                            <div class="stat-value text-amber">{{ number_format($anggotaPeringatan) }}</div>
                            <div class="stat-sub">Perlu pemantauan</div>
                        </div>
                        <div class="stat-icon tint-amber"><i class="bi bi-exclamation-circle-fill"></i></div>
                    </div>
                    @if ($totalAnggota > 0)
                        <div class="progress-soft mt-2">
                            <div class="progress-bar"
                                style="width:{{ round(($anggotaPeringatan / $totalAnggota) * 100) }}%;
                                    background:linear-gradient(90deg,#f59e0b,#fbbf24);">
                            </div>
                        </div>
                        <div style="font-size:.7rem; color:var(--text-muted); margin-top:.3rem;">
                            {{ round(($anggotaPeringatan / $totalAnggota) * 100) }}% dari total
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Anggota Tidak Aktif</div>
                            <div class="stat-value text-rose">{{ number_format($anggotaTidakAktif) }}</div>
                            <div class="stat-sub">Perlu perhatian serius</div>
                        </div>
                        <div class="stat-icon tint-rose"><i class="bi bi-x-circle-fill"></i></div>
                    </div>
                    @if ($totalAnggota > 0)
                        <div class="progress-soft mt-2">
                            <div class="progress-bar"
                                style="width:{{ round(($anggotaTidakAktif / $totalAnggota) * 100) }}%;
                                    background:linear-gradient(90deg,#f43f5e,#fb7185);">
                            </div>
                        </div>
                        <div style="font-size:.7rem; color:var(--text-muted); margin-top:.3rem;">
                            {{ round(($anggotaTidakAktif / $totalAnggota) * 100) }}% dari total
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
     BARIS 2: Statistik Operasional (4 kartu)
════════════════════════════════════════════════════════════ --}}
    <div class="section-title"><i class="bi bi-bar-chart-line-fill"></i> Statistik Operasional</div>
    <div class="row g-3 mb-4">

        <div class="col-6 col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Total Usaha Aktif</div>
                            <div class="stat-value text-cyan">{{ number_format($totalUsahaAktif) }}</div>
                            <div class="stat-sub">Usaha berstatus aktif</div>
                        </div>
                        <div class="stat-icon tint-cyan"><i class="bi bi-shop-window"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Total Laporan</div>
                            <div class="stat-value text-violet">{{ number_format($totalLaporan) }}</div>
                            <div class="stat-sub">Semua periode</div>
                        </div>
                        <div class="stat-icon tint-violet"><i class="bi bi-journal-richtext"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Sudah Dievaluasi</div>
                            <div class="stat-value text-emerald">{{ number_format($laporanDievaluasi) }}</div>
                            <div class="stat-sub">Evaluasi 14 hari selesai</div>
                        </div>
                        <div class="stat-icon tint-emerald"><i class="bi bi-clipboard2-check-fill"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Menunggu Validasi DPS</div>
                            <div class="stat-value text-amber">{{ number_format($usahaMenungguDps) }}</div>
                            <div class="stat-sub">Perlu tindak lanjut DPS</div>
                        </div>
                        <div class="stat-icon tint-amber"><i class="bi bi-hourglass-split"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
     BARIS 3: Grafik (2 kolom)
════════════════════════════════════════════════════════════ --}}
    <div class="section-title"><i class="bi bi-graph-up-arrow"></i> Grafik Monitoring</div>
    <div class="row g-3 mb-4">

        {{-- Grafik A: Perkembangan Laporan per Periode --}}
        <div class="col-12 col-lg-8">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span>
                        <i class="bi bi-bar-chart-line-fill me-2" style="color:var(--primary);"></i>
                        Perkembangan Laporan per Periode
                    </span>
                    <span style="font-size:.72rem; color:var(--text-muted);">12 periode terakhir</span>
                </div>
                <div class="card-body" style="position:relative; height:280px;">
                    <canvas id="chartLaporan"></canvas>
                </div>
            </div>
        </div>

        {{-- Grafik B: Status Anggota Doughnut --}}
        <div class="col-12 col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="bi bi-pie-chart-fill me-2" style="color:var(--secondary);"></i>
                    Status Anggota
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center" style="height:280px;">
                    <canvas id="chartStatusAnggota" style="max-width:220px; max-height:220px;"></canvas>
                    <div class="d-flex gap-3 mt-3 flex-wrap justify-content-center">
                        <div class="d-flex align-items-center gap-1">
                            <div style="width:10px; height:10px; border-radius:50%; background:#10b981; flex-shrink:0;">
                            </div>
                            <span style="font-size:.73rem; color:var(--text-muted);">Aktif
                                ({{ $grafikStatusAnggota['aktif'] }})</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <div style="width:10px; height:10px; border-radius:50%; background:#f59e0b; flex-shrink:0;">
                            </div>
                            <span style="font-size:.73rem; color:var(--text-muted);">Peringatan
                                ({{ $grafikStatusAnggota['peringatan'] }})</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <div style="width:10px; height:10px; border-radius:50%; background:#f43f5e; flex-shrink:0;">
                            </div>
                            <span style="font-size:.73rem; color:var(--text-muted);">Tidak Aktif
                                ({{ $grafikStatusAnggota['tidak_aktif'] }})</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">

        {{-- Grafik C: Distribusi Jenis Akad --}}
        <div class="col-12 col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="bi bi-pie-chart-fill me-2" style="color:#047857;"></i>
                    Distribusi Jenis Akad
                </div>
                <div class="card-body d-flex align-items-center justify-content-center" style="height:260px;">
                    <canvas id="chartJenisAkad" style="max-width:220px; max-height:220px;"></canvas>
                </div>
            </div>
        </div>

        {{-- Grafik D: Hasil Evaluasi --}}
        <div class="col-12 col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="bi bi-clipboard2-data-fill me-2" style="color:var(--accent);"></i>
                    Distribusi Hasil Evaluasi
                </div>
                <div class="card-body d-flex align-items-center justify-content-center" style="height:260px;">
                    <canvas id="chartHasilEvaluasi" style="max-width:220px; max-height:220px;"></canvas>
                </div>
            </div>
        </div>

        {{-- Grafik E: Perkembangan Omzet --}}
        <div class="col-12 col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="bi bi-graph-up-arrow me-2" style="color:var(--warning);"></i>
                    Perkembangan Omzet
                </div>
                <div class="card-body" style="position:relative; height:260px;">
                    <canvas id="chartOmzet"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
     BARIS 4: Anggota Perlu Perhatian
════════════════════════════════════════════════════════════ --}}
    <div class="section-title">
        <i class="bi bi-exclamation-triangle-fill" style="color:var(--danger);"></i>
        Anggota yang Memerlukan Perhatian
    </div>
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <span>
                <i class="bi bi-person-exclamation me-2" style="color:var(--danger);"></i>
                Anggota dengan Status Peringatan / Tidak Aktif
            </span>
            <a href="{{ route('ketua.monitoring.index', ['status_anggota' => 'tidak_aktif']) }}" class="btn-theme-light"
                style="padding:.35rem .85rem; font-size:.75rem;">
                Lihat Semua
            </a>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Anggota</th>
                        <th>Nama Usaha</th>
                        <th>Hasil Evaluasi Terakhir</th>
                        <th>Kenaikan Omzet</th>
                        <th>Rasio Biaya</th>
                        <th>Periode</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($anggotaPerhatianData as $data)
                        @php
                            $anggota = $data['anggota'];
                            $aTint = match ($anggota->status_anggota) {
                                'tidak_aktif' => 'rose',
                                'peringatan' => 'amber',
                                default => 'emerald',
                            };
                        @endphp
                        @foreach ($data['usaha_data'] as $usahaItem)
                            @php
                                $usaha = $usahaItem['usaha'];
                                $laporan = $usahaItem['laporan'];
                                $evaluasi = $usahaItem['evaluasi'];
                            @endphp
                            @if ($evaluasi)
                                <tr
                                    style="{{ $anggota->status_anggota === 'tidak_aktif' ? 'background:linear-gradient(90deg,rgba(244,63,94,.04),transparent);' : 'background:linear-gradient(90deg,rgba(245,158,11,.04),transparent);' }}">
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ $anggota->akun->foto_url }}" alt="" class="avatar-sm">
                                            <div>
                                                <div style="font-size:.82rem; font-weight:700;">{{ $anggota->akun->nama }}
                                                </div>
                                                <div class="d-flex align-items-center gap-1 mt-1">
                                                    <span class="code-tag"
                                                        style="font-size:.68rem;">{{ $anggota->nomor_anggota }}</span>
                                                    <span class="badge-soft tint-{{ $aTint }}"
                                                        style="font-size:.65rem;">
                                                        {{ $anggota->status_anggota_label }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size:.82rem; font-weight:700;">{{ $usaha->nama_usaha }}</div>
                                        <span class="badge-soft tint-{{ $usaha->jenis_usaha_tint }}"
                                            style="font-size:.65rem;">
                                            {{ $usaha->jenis_usaha_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge-soft tint-{{ $evaluasi->hasil_evaluasi_tint }}">
                                            {{ $evaluasi->hasil_evaluasi_label }}
                                        </span>
                                    </td>
                                    <td>
                                        @php $kOk = (float)$evaluasi->persentase_kenaikan_omzet >= 5; @endphp
                                        <div
                                            style="font-size:.82rem; font-weight:700; color:{{ $kOk ? '#047857' : '#be123c' }};">
                                            {{ $evaluasi->persentase_kenaikan_omzet }}%
                                            <i class="bi bi-{{ $kOk ? 'check' : 'x' }}-circle-fill ms-1"
                                                style="font-size:.78rem;"></i>
                                        </div>
                                    </td>
                                    <td>
                                        @php $rOk = (float)$evaluasi->rasio_biaya < 60; @endphp
                                        <div
                                            style="font-size:.82rem; font-weight:700; color:{{ $rOk ? '#047857' : '#be123c' }};">
                                            {{ $evaluasi->rasio_biaya }}%
                                            <i class="bi bi-{{ $rOk ? 'check' : 'x' }}-circle-fill ms-1"
                                                style="font-size:.78rem;"></i>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($laporan)
                                            <span class="badge-soft tint-indigo"
                                                style="font-size:.7rem;">Ke-{{ $laporan->periode_ke }}</span>
                                            <div style="font-size:.7rem; color:var(--text-muted);">
                                                {{ $laporan->tanggal_mulai->format('d/m/Y') }}
                                            </div>
                                        @else
                                            <span style="color:var(--text-light); font-size:.78rem;">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('ketua.monitoring.show', $evaluasi->id) }}"
                                            class="icon-action-btn btn-view" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="theme-empty-state" style="padding:2rem 1rem;">
                                    <div class="empty-icon"
                                        style="width:56px; height:56px; font-size:1.4rem; background:linear-gradient(135deg,#ecfdf5,#d1fae5);">
                                        <i class="bi bi-check-circle" style="color:#047857;"></i>
                                    </div>
                                    <h6 style="font-size:.9rem; color:#047857;">Semua anggota dalam kondisi baik!</h6>
                                    <p style="font-size:.8rem;">Tidak ada anggota yang memerlukan perhatian khusus saat
                                        ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
     BARIS 5: Aktivitas Terbaru (3 kolom)
════════════════════════════════════════════════════════════ --}}
    <div class="section-title"><i class="bi bi-clock-history"></i> Aktivitas Terbaru</div>
    <div class="row g-3">

        {{-- A: Evaluasi Terbaru --}}
        <div class="col-12 col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="bi bi-clipboard2-check-fill me-2" style="color:var(--success);"></i>Evaluasi Terbaru
                </div>
                <div class="card-body p-0">
                    @forelse ($evaluasiTerbaru as $ev)
                        <div class="d-flex align-items-center gap-2 px-3 py-3" style="border-bottom:1px solid #f6f7fb;">
                            <div class="notif-icon tint-{{ $ev->hasil_evaluasi_tint }}"
                                style="width:36px; height:36px; font-size:.85rem; flex-shrink:0;">
                                <i class="bi bi-clipboard2-check-fill"></i>
                            </div>
                            <div style="flex:1; min-width:0;">
                                <div
                                    style="font-size:.8rem; font-weight:700; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                    {{ $ev->laporan->usaha->anggota->akun->nama }}
                                </div>
                                <div
                                    style="font-size:.72rem; color:var(--text-muted); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                    {{ $ev->laporan->usaha->nama_usaha }}
                                    &bull; Periode ke-{{ $ev->laporan->periode_ke }}
                                </div>
                            </div>
                            <div style="text-align:right; flex-shrink:0;">
                                <span class="badge-soft tint-{{ $ev->hasil_evaluasi_tint }}"
                                    style="font-size:.68rem; display:block; margin-bottom:3px;">
                                    {{ $ev->hasil_evaluasi_label }}
                                </span>
                                <div style="font-size:.68rem; color:var(--text-muted);">
                                    {{ $ev->tanggal_validasi_pengurus?->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="theme-empty-state" style="padding:1.8rem 1rem;">
                            <div class="empty-icon" style="width:48px; height:48px; font-size:1.2rem;"><i
                                    class="bi bi-clipboard2-x"></i></div>
                            <h6 style="font-size:.84rem;">Belum ada evaluasi</h6>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- B: Validasi Syariah Terbaru --}}
        <div class="col-12 col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="bi bi-shield-check me-2" style="color:#047857;"></i>Validasi Syariah Terbaru
                </div>
                <div class="card-body p-0">
                    @forelse ($validasiSyariahTerbaru as $vs)
                        <div class="d-flex align-items-center gap-2 px-3 py-3" style="border-bottom:1px solid #f6f7fb;">
                            <div class="notif-icon tint-{{ $vs->status_tint }}"
                                style="width:36px; height:36px; font-size:.85rem; flex-shrink:0;">
                                <i class="bi {{ $vs->status_icon }}"></i>
                            </div>
                            <div style="flex:1; min-width:0;">
                                <div
                                    style="font-size:.8rem; font-weight:700; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                    {{ $vs->usaha->anggota->akun->nama ?? '-' }}
                                </div>
                                <div
                                    style="font-size:.72rem; color:var(--text-muted); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                    {{ $vs->usaha->nama_usaha }} &bull; {{ $vs->jenis_akad_divalidasi }}
                                </div>
                            </div>
                            <div style="text-align:right; flex-shrink:0;">
                                <span class="badge-soft tint-{{ $vs->status_tint }}"
                                    style="font-size:.68rem; display:block; margin-bottom:3px;">
                                    {{ $vs->status_label }}
                                </span>
                                <div style="font-size:.68rem; color:var(--text-muted);">
                                    {{ $vs->divalidasi_pada?->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="theme-empty-state" style="padding:1.8rem 1rem;">
                            <div class="empty-icon" style="width:48px; height:48px; font-size:1.2rem;"><i
                                    class="bi bi-shield-x"></i></div>
                            <h6 style="font-size:.84rem;">Belum ada validasi syariah</h6>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- C: Laporan Terbaru --}}
        <div class="col-12 col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="bi bi-journal-richtext me-2" style="color:var(--primary);"></i>Laporan Terbaru
                </div>
                <div class="card-body p-0">
                    @forelse ($laporanTerbaru as $lap)
                        <div class="d-flex align-items-center gap-2 px-3 py-3" style="border-bottom:1px solid #f6f7fb;">
                            <div class="notif-icon tint-{{ $lap->status_laporan_tint }}"
                                style="width:36px; height:36px; font-size:.85rem; flex-shrink:0;">
                                <i class="bi {{ $lap->status_laporan_icon }}"></i>
                            </div>
                            <div style="flex:1; min-width:0;">
                                <div
                                    style="font-size:.8rem; font-weight:700; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                    {{ $lap->usaha->anggota->akun->nama }}
                                </div>
                                <div
                                    style="font-size:.72rem; color:var(--text-muted); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                    {{ $lap->usaha->nama_usaha }} &bull; Periode ke-{{ $lap->periode_ke }}
                                </div>
                            </div>
                            <div style="text-align:right; flex-shrink:0;">
                                <span class="badge-soft tint-{{ $lap->status_laporan_tint }}"
                                    style="font-size:.68rem; display:block; margin-bottom:3px;">
                                    {{ $lap->status_laporan_label }}
                                </span>
                                <div style="font-size:.68rem; color:var(--text-muted);">
                                    {{ $lap->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="theme-empty-state" style="padding:1.8rem 1rem;">
                            <div class="empty-icon" style="width:48px; height:48px; font-size:1.2rem;"><i
                                    class="bi bi-journal-x"></i></div>
                            <h6 style="font-size:.84rem;">Belum ada laporan</h6>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script>
        // ── Palet warna reusable ────────────────────────────────────
        const C = {
            indigo: '#6366f1',
            emerald: '#10b981',
            amber: '#f59e0b',
            rose: '#f43f5e',
            cyan: '#06b6d4',
            violet: '#8b5cf6',
            slate: '#64748b',
            gridLine: 'rgba(226,232,240,.5)',
            textMuted: '#94a3b8',
        };

        const fontDef = {
            family: "'Inter','Segoe UI',sans-serif",
            size: 11
        };

        Chart.defaults.font = fontDef;
        Chart.defaults.color = C.textMuted;
        Chart.defaults.plugins.legend.labels.boxWidth = 10;
        Chart.defaults.plugins.legend.labels.borderRadius = 50;
        Chart.defaults.plugins.legend.labels.usePointStyle = true;

        // ── Data dari PHP ───────────────────────────────────────────
        const grafikLaporan = @json($grafikLaporan);
        const grafikOmzet = @json($grafikOmzet);
        const statusAnggota = @json($grafikStatusAnggota);
        const jenisAkad = @json($grafikJenisAkad);
        const hasilEval = @json($grafikHasilEvaluasi);

        // ════════════════════════════════════════════════
        // Grafik A: Perkembangan Laporan per Periode (bar)
        // ════════════════════════════════════════════════
        const ctxLaporan = document.getElementById('chartLaporan').getContext('2d');
        new Chart(ctxLaporan, {
            type: 'bar',
            data: {
                labels: grafikLaporan.map(d => `Periode ${d.periode_ke}`),
                datasets: [{
                        label: 'Dikirim',
                        data: grafikLaporan.map(d => d.dikirim),
                        backgroundColor: 'rgba(99,102,241,.7)',
                        borderRadius: 4,
                    },
                    {
                        label: 'Divalidasi',
                        data: grafikLaporan.map(d => d.divalidasi),
                        backgroundColor: 'rgba(16,185,129,.7)',
                        borderRadius: 4,
                    },
                    {
                        label: 'Ditolak',
                        data: grafikLaporan.map(d => d.ditolak),
                        backgroundColor: 'rgba(244,63,94,.7)',
                        borderRadius: 4,
                    },
                    {
                        label: 'Dievaluasi',
                        data: grafikLaporan.map(d => d.dievaluasi),
                        backgroundColor: 'rgba(6,182,212,.7)',
                        borderRadius: 4,
                    },
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: C.gridLine
                        },
                        ticks: {
                            font: fontDef
                        }
                    },
                    y: {
                        grid: {
                            color: C.gridLine
                        },
                        ticks: {
                            font: fontDef,
                            stepSize: 1
                        },
                        beginAtZero: true
                    },
                }
            }
        });

        // ════════════════════════════════════════════════
        // Grafik B: Status Anggota (doughnut)
        // ════════════════════════════════════════════════
        const ctxStatus = document.getElementById('chartStatusAnggota').getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Aktif', 'Peringatan', 'Tidak Aktif'],
                datasets: [{
                    data: [statusAnggota.aktif, statusAnggota.peringatan, statusAnggota.tidak_aktif],
                    backgroundColor: [C.emerald, C.amber, C.rose],
                    borderWidth: 0,
                    hoverOffset: 6,
                }]
            },
            options: {
                cutout: '68%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.label}: ${ctx.raw} anggota`
                        }
                    }
                }
            }
        });

        // ════════════════════════════════════════════════
        // Grafik C: Jenis Akad (doughnut)
        // ════════════════════════════════════════════════
        const akadLabels = Object.keys(jenisAkad);
        const akadValues = Object.values(jenisAkad);
        const akadColors = [C.indigo, C.emerald, C.amber, C.cyan, C.violet, C.rose];

        const ctxAkad = document.getElementById('chartJenisAkad').getContext('2d');
        new Chart(ctxAkad, {
            type: 'doughnut',
            data: {
                labels: akadLabels,
                datasets: [{
                    data: akadValues,
                    backgroundColor: akadColors.slice(0, akadLabels.length),
                    borderWidth: 0,
                    hoverOffset: 6,
                }]
            },
            options: {
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.label}: ${ctx.raw} usaha`
                        }
                    }
                }
            }
        });

        // ════════════════════════════════════════════════
        // Grafik D: Hasil Evaluasi (doughnut)
        // ════════════════════════════════════════════════
        const ctxEval = document.getElementById('chartHasilEvaluasi').getContext('2d');
        new Chart(ctxEval, {
            type: 'doughnut',
            data: {
                labels: ['Aktif', 'Peringatan', 'Tidak Aktif'],
                datasets: [{
                    data: [hasilEval.aktif ?? 0, hasilEval.peringatan ?? 0, hasilEval.tidak_aktif ?? 0],
                    backgroundColor: [C.emerald, C.amber, C.rose],
                    borderWidth: 0,
                    hoverOffset: 6,
                }]
            },
            options: {
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.label}: ${ctx.raw} evaluasi`
                        }
                    }
                }
            }
        });

        // ════════════════════════════════════════════════
        // Grafik E: Perkembangan Omzet (line)
        // ════════════════════════════════════════════════
        const ctxOmzet = document.getElementById('chartOmzet').getContext('2d');
        const gradientOmzet = ctxOmzet.createLinearGradient(0, 0, 0, 200);
        gradientOmzet.addColorStop(0, 'rgba(99,102,241,.25)');
        gradientOmzet.addColorStop(1, 'rgba(99,102,241,0)');

        new Chart(ctxOmzet, {
            type: 'line',
            data: {
                labels: grafikOmzet.map(d => `P${d.periode_ke}`),
                datasets: [{
                    label: 'Total Omzet',
                    data: grafikOmzet.map(d => parseFloat(d.total_omzet)),
                    borderColor: C.indigo,
                    backgroundColor: gradientOmzet,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: C.indigo,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    borderWidth: 2.5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' Rp ' + ctx.raw.toLocaleString('id-ID')
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: C.gridLine
                        },
                        ticks: {
                            font: fontDef
                        }
                    },
                    y: {
                        grid: {
                            color: C.gridLine
                        },
                        ticks: {
                            font: fontDef,
                            callback: val => 'Rp ' + (val >= 1000000 ? (val / 1000000).toFixed(1) + 'jt' : val
                                .toLocaleString('id-ID'))
                        },
                        beginAtZero: true,
                    },
                }
            }
        });
    </script>
@endpush
