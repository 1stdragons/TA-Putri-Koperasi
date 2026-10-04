@extends('layouts.app')

@section('title', 'Dashboard DPS')
@section('page-title', 'Dashboard DPS')
@section('page-subtitle', 'Dewan Pengawas Syariah — Koperasi Poltas Kreatif Mandiri')

@section('sidebar-menu')
    @include('dps.partials._sidebar')
@endsection

@push('styles')
    <style>
        .dps-dashboard-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.55fr) minmax(320px, .95fr);
            gap: 1rem;
        }

        .dps-summary-chart {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
            min-height: 210px;
        }

        .dps-donut {
            position: relative;
            width: 148px;
            height: 148px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .dps-donut::after {
            content: '';
            position: absolute;
            inset: 27px;
            border-radius: 50%;
            background: #fff;
            box-shadow: inset 0 0 0 1px #eef0f7;
        }

        .dps-donut-content {
            position: absolute;
            inset: 0;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .dps-donut-content span {
            color: var(--text-muted);
            font-size: .69rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .35px;
        }

        .dps-donut-content strong {
            color: var(--text-dark);
            font-size: 1.55rem;
            line-height: 1.1;
        }

        .dps-chart-legend {
            display: flex;
            flex: 1;
            flex-direction: column;
            gap: .85rem;
        }

        .dps-legend-row {
            display: grid;
            grid-template-columns: 12px 1fr auto;
            align-items: center;
            gap: .55rem;
            font-size: .78rem;
        }

        .dps-legend-dot {
            width: 11px;
            height: 11px;
            border-radius: 3px;
        }

        .dps-legend-name {
            color: var(--text-dark);
            font-weight: 650;
        }

        .dps-legend-value {
            color: var(--text-muted);
            text-align: right;
            white-space: nowrap;
        }

        .akad-progress-row+.akad-progress-row {
            margin-top: .9rem;
        }

        .akad-progress-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            margin-bottom: .35rem;
            font-size: .76rem;
        }

        .akad-progress-name {
            color: var(--text-dark);
            font-weight: 700;
        }

        .akad-progress-value {
            color: var(--text-muted);
            white-space: nowrap;
        }

        .akad-progress-track {
            width: 100%;
            height: 9px;
            overflow: hidden;
            border-radius: 999px;
            background: #eef1f7;
        }

        .akad-progress-fill {
            height: 100%;
            min-width: 0;
            border-radius: inherit;
            background: linear-gradient(90deg,
                    var(--primary),
                    #8b86fb);
        }

        .validation-process {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .85rem;
        }

        .validation-step {
            position: relative;
            border: 1px solid #eceef6;
            border-radius: 14px;
            background: var(--bg-soft);
            padding: 1rem;
        }

        .validation-step-number {
            position: absolute;
            top: .65rem;
            right: .7rem;
            width: 23px;
            height: 23px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: var(--primary);
            color: #fff;
            font-size: .68rem;
            font-weight: 800;
        }

        .validation-step-icon {
            width: 47px;
            height: 47px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: .75rem;
            border-radius: 13px;
            background: #eef2ff;
            color: var(--primary);
            font-size: 1.25rem;
        }

        .validation-step h6 {
            margin-bottom: .3rem;
            color: var(--text-dark);
            font-size: .79rem;
            font-weight: 800;
        }

        .validation-step p {
            margin: 0;
            color: var(--text-muted);
            font-size: .7rem;
            line-height: 1.55;
        }

        .validation-results {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: .55rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #edf0f6;
        }

        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: .75rem;
            padding: .95rem 1rem;
            border-bottom: 1px solid #f1f2f7;
        }

        .activity-item:last-child {
            border-bottom: 0;
        }

        .activity-content {
            flex: 1;
            min-width: 0;
        }

        .activity-title {
            color: var(--text-dark);
            font-size: .79rem;
            font-weight: 750;
        }

        .activity-description {
            margin-top: .18rem;
            color: var(--text-muted);
            font-size: .7rem;
            line-height: 1.45;
        }

        .activity-time {
            color: var(--text-muted);
            font-size: .67rem;
            white-space: nowrap;
        }

        @media (max-width: 1199.98px) {
            .dps-dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767.98px) {
            .dps-summary-chart {
                flex-direction: column;
            }

            .dps-chart-legend {
                width: 100%;
            }

            .validation-process {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    {{-- =========================================================
        BANNER SELAMAT DATANG
    ========================================================== --}}
    <div class="welcome-banner d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h5 class="mb-1">
                <i class="bi bi-shield-check me-1"></i>

                Selamat Datang,
                {{ Auth::user()->nama }} 👋
            </h5>

            <p class="mb-0">
                Lakukan validasi akad usaha anggota sesuai dengan
                prinsip syariah.
            </p>
        </div>

        <div class="text-end">
            <span class="role-pill">
                <i class="bi bi-patch-check-fill me-1"></i>
                Dewan Pengawas Syariah
            </span>

            <div
                style="
                    color:rgba(255,255,255,.8);
                    font-size:.72rem;
                    margin-top:.45rem;
                ">
                {{ now('Asia/Jakarta')->translatedFormat('l, d F Y — H:i') }} WIB
            </div>
        </div>
    </div>

    {{-- =========================================================
        STATISTIK UTAMA
    ========================================================== --}}
    <div class="section-title">
        <i class="bi bi-bar-chart-line-fill"></i>
        Statistik Validasi Syariah
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div>
                            <div class="stat-label">
                                Menunggu Validasi
                            </div>

                            <div class="stat-value text-amber">
                                {{ number_format($totalMenunggu) }}
                            </div>

                            <div class="stat-sub">
                                Perlu segera diperiksa
                            </div>
                        </div>

                        <div class="stat-icon tint-amber">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div>
                            <div class="stat-label">
                                Disetujui
                            </div>

                            <div class="stat-value text-emerald">
                                {{ number_format($totalDisetujui) }}
                            </div>

                            <div class="stat-sub">
                                Akad sesuai syariah
                            </div>
                        </div>

                        <div class="stat-icon tint-emerald">
                            <i class="bi bi-patch-check-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div>
                            <div class="stat-label">
                                Perlu Revisi
                            </div>

                            <div class="stat-value text-rose">
                                {{ number_format($totalPerluRevisi) }}
                            </div>

                            <div class="stat-sub">
                                Dokumen perlu diperbaiki
                            </div>
                        </div>

                        <div class="stat-icon tint-rose">
                            <i class="bi bi-arrow-repeat"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div>
                            <div class="stat-label">
                                Total Diproses
                            </div>

                            <div class="stat-value text-indigo">
                                {{ number_format($totalDiproses) }}
                            </div>

                            <div class="stat-sub">
                                Telah mendapat keputusan
                            </div>
                        </div>

                        <div class="stat-icon tint-indigo">
                            <i class="bi bi-clipboard2-check-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- =========================================================
        BARIS UTAMA
    ========================================================== --}}
    <div class="dps-dashboard-grid mb-4">
        {{-- Tabel usaha menunggu validasi --}}
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between gap-2">
                <span>
                    <i class="bi bi-hourglass-split me-2" style="color:#d97706;"></i>
                    Validasi Syariah Menunggu Tindakan
                </span>

                <a href="{{ route('dps.validasi-syariah.index') }}" class="btn-theme-light"
                    style="padding:.35rem .75rem; font-size:.73rem;">
                    Lihat Semua
                    <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Anggota</th>
                            <th>Usaha</th>
                            <th>Akad</th>
                            <th>Pengajuan</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($usahaMenunggu as $usaha)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $usaha->anggota->akun->foto_url }}"
                                            alt="{{ $usaha->anggota->akun->nama }}" class="avatar-sm">

                                        <div>
                                            <div
                                                style="
                                                    font-size:.79rem;
                                                    font-weight:750;
                                                ">
                                                {{ $usaha->anggota->akun->nama }}
                                            </div>

                                            <div
                                                style="
                                                    font-size:.68rem;
                                                    color:var(--text-muted);
                                                ">
                                                {{ $usaha->anggota->nomor_anggota }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div
                                        style="
                                            font-size:.79rem;
                                            font-weight:750;
                                        ">
                                        {{ $usaha->nama_usaha }}
                                    </div>

                                    <span class="badge-soft tint-{{ $usaha->jenis_usaha_tint }}" style="font-size:.65rem;">
                                        {{ $usaha->jenis_usaha_label }}
                                    </span>
                                </td>

                                <td>
                                    @if ($usaha->jenis_akad)
                                        <span class="badge-soft tint-{{ $usaha->jenis_akad_tint }}"
                                            style="font-size:.68rem;">
                                            {{ $usaha->jenis_akad_label }}
                                        </span>
                                    @else
                                        <span
                                            style="
                                                color:var(--text-light);
                                                font-size:.75rem;
                                            ">
                                            —
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <div
                                        style="
                                            font-size:.73rem;
                                            color:var(--text-dark);
                                            font-weight:650;
                                        ">
                                        {{ $usaha->updated_at->format('d/m/Y') }}
                                    </div>

                                    <div
                                        style="
                                            font-size:.67rem;
                                            color:var(--text-muted);
                                        ">
                                        {{ $usaha->updated_at->diffForHumans() }}
                                    </div>
                                </td>

                                <td>
                                    <span class="badge-soft tint-{{ $usaha->status_validasi_syariah_tint }}"
                                        style="font-size:.67rem;">
                                        <i class="bi {{ $usaha->status_validasi_syariah_icon }} me-1"></i>

                                        {{ $usaha->status_validasi_syariah_label }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    <a href="{{ route('dps.validasi-syariah.show', $usaha->id) }}"
                                        class="icon-action-btn btn-view" title="Periksa dan validasi">
                                        <i class="bi bi-shield-check"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="theme-empty-state" style="padding:2.5rem 1rem;">
                                        <div class="empty-icon"
                                            style="
                                                width:58px;
                                                height:58px;
                                                font-size:1.4rem;
                                            ">
                                            <i class="bi bi-patch-check"></i>
                                        </div>

                                        <h6 style="font-size:.87rem;">
                                            Tidak ada validasi tertunda
                                        </h6>

                                        <p style="font-size:.76rem;">
                                            Seluruh akad yang diajukan telah diproses.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Ringkasan status validasi --}}
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pie-chart-fill me-2" style="color:var(--primary);"></i>
                Ringkasan Validasi Syariah
            </div>

            <div class="card-body">
                @php
                    $batasDisetujui = $persentaseDisetujui;

                    $batasRevisi = $persentaseDisetujui + $persentasePerluRevisi;
                @endphp

                <div class="dps-summary-chart">
                    <div class="dps-donut"
                        style="
                            background:conic-gradient(
                                #10b981 0%
                                {{ $batasDisetujui }}%,
                                #f43f5e
                                {{ $batasDisetujui }}%
                                {{ $batasRevisi }}%,
                                #f59e0b
                                {{ $batasRevisi }}%
                                100%
                            );
                        ">
                        <div class="dps-donut-content">
                            <span>Total</span>
                            <strong>
                                {{ number_format($totalRingkasan) }}
                            </strong>
                            <span>Usaha</span>
                        </div>
                    </div>

                    <div class="dps-chart-legend">
                        <div class="dps-legend-row">
                            <span class="dps-legend-dot" style="background:#10b981;"></span>

                            <span class="dps-legend-name">
                                Disetujui
                            </span>

                            <span class="dps-legend-value">
                                {{ $totalDisetujui }}
                                ({{ number_format($persentaseDisetujui, 1, ',', '.') }}%)
                            </span>
                        </div>

                        <div class="dps-legend-row">
                            <span class="dps-legend-dot" style="background:#f43f5e;"></span>

                            <span class="dps-legend-name">
                                Perlu Revisi
                            </span>

                            <span class="dps-legend-value">
                                {{ $totalPerluRevisi }}
                                ({{ number_format($persentasePerluRevisi, 1, ',', '.') }}%)
                            </span>
                        </div>

                        <div class="dps-legend-row">
                            <span class="dps-legend-dot" style="background:#f59e0b;"></span>

                            <span class="dps-legend-name">
                                Menunggu
                            </span>

                            <span class="dps-legend-value">
                                {{ $totalMenunggu }}
                                ({{ number_format($persentaseMenunggu, 1, ',', '.') }}%)
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- =========================================================
        DISTRIBUSI AKAD
    ========================================================== --}}
    <div class="dps-dashboard-grid mb-4">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between gap-2">
                <span>
                    <i class="bi bi-bar-chart-steps me-2" style="color:var(--primary);"></i>
                    Distribusi Jenis Akad
                </span>

                <span class="badge-soft tint-indigo" style="font-size:.7rem;">
                    Total {{ number_format($totalAkad) }} akad
                </span>
            </div>

            <div class="card-body">
                <div class="akad-progress-row">
                    <div class="akad-progress-head">
                        <span class="akad-progress-name">
                            Mudharabah
                        </span>

                        <span class="akad-progress-value">
                            {{ $jumlahMudharabah }} usaha
                            ·
                            {{ number_format($persentaseMudharabah, 1, ',', '.') }}%
                        </span>
                    </div>

                    <div class="akad-progress-track">
                        <div class="akad-progress-fill" style="width:{{ $persentaseMudharabah }}%;"></div>
                    </div>
                </div>

                <div class="akad-progress-row">
                    <div class="akad-progress-head">
                        <span class="akad-progress-name">
                            Musyarakah
                        </span>

                        <span class="akad-progress-value">
                            {{ $jumlahMusyarakah }} usaha
                            ·
                            {{ number_format($persentaseMusyarakah, 1, ',', '.') }}%
                        </span>
                    </div>

                    <div class="akad-progress-track">
                        <div class="akad-progress-fill" style="width:{{ $persentaseMusyarakah }}%;"></div>
                    </div>
                </div>

                <div class="akad-progress-row">
                    <div class="akad-progress-head">
                        <span class="akad-progress-name">
                            Murabahah
                        </span>

                        <span class="akad-progress-value">
                            {{ $jumlahMurabahah }} usaha
                            ·
                            {{ number_format($persentaseMurabahah, 1, ',', '.') }}%
                        </span>
                    </div>

                    <div class="akad-progress-track">
                        <div class="akad-progress-fill" style="width:{{ $persentaseMurabahah }}%;"></div>
                    </div>
                </div>

                <div class="akad-progress-row">
                    <div class="akad-progress-head">
                        <span class="akad-progress-name">
                            Ijarah
                        </span>

                        <span class="akad-progress-value">
                            {{ $jumlahIjarah }} usaha
                            ·
                            {{ number_format($persentaseIjarah, 1, ',', '.') }}%
                        </span>
                    </div>

                    <div class="akad-progress-track">
                        <div class="akad-progress-fill" style="width:{{ $persentaseIjarah }}%;"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Aktivitas DPS terbaru --}}
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between gap-2">
                <span>
                    <i class="bi bi-bell-fill me-2" style="color:var(--primary);"></i>
                    Aktivitas Validasi Terbaru
                </span>

                <a href="{{ route('dps.rekap-syariah.index') }}" class="btn-theme-light"
                    style="padding:.3rem .7rem; font-size:.7rem;">
                    Lihat Rekap
                </a>
            </div>

            <div class="card-body p-0">
                @forelse ($riwayatTerbaru as $riwayat)
                    <div class="activity-item">
                        <div class="notif-icon tint-{{ $riwayat->status_tint }}">
                            <i class="bi {{ $riwayat->status_icon }}"></i>
                        </div>

                        <div class="activity-content">
                            <div class="activity-title">
                                {{ $riwayat->usaha->nama_usaha ?? 'Usaha tidak tersedia' }}
                            </div>

                            <div class="activity-description">
                                {{ $riwayat->usaha->anggota->akun->nama ?? 'Anggota tidak tersedia' }}

                                &bull;

                                {{ $riwayat->jenis_akad_divalidasi ? ucfirst($riwayat->jenis_akad_divalidasi) : 'Akad tidak tersedia' }}

                                &bull;

                                Iterasi ke-{{ $riwayat->iterasi }}
                            </div>

                            @if ($riwayat->catatan_dps)
                                <div class="activity-description fst-italic">
                                    “{{ \Illuminate\Support\Str::limit($riwayat->catatan_dps, 75) }}”
                                </div>
                            @endif
                        </div>

                        <div class="text-end">
                            <span class="badge-soft tint-{{ $riwayat->status_tint }}" style="font-size:.65rem;">
                                {{ $riwayat->status_label }}
                            </span>

                            <div class="activity-time mt-1">
                                {{ $riwayat->divalidasi_pada
                                    ? $riwayat->divalidasi_pada->diffForHumans()
                                    : $riwayat->updated_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="theme-empty-state" style="padding:2.5rem 1rem;">
                        <div class="empty-icon"
                            style="
                                width:58px;
                                height:58px;
                                font-size:1.4rem;
                            ">
                            <i class="bi bi-shield-x"></i>
                        </div>

                        <h6 style="font-size:.87rem;">
                            Belum ada aktivitas validasi
                        </h6>

                        <p style="font-size:.76rem;">
                            Aktivitas akan muncul setelah DPS memproses akad.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- =========================================================
        PROSES VALIDASI SYARIAH
    ========================================================== --}}
    <div class="card">
        <div class="card-header">
            <i class="bi bi-diagram-3-fill me-2" style="color:var(--primary);"></i>
            Proses Validasi Syariah
        </div>

        <div class="card-body">
            <p
                style="
                    color:var(--text-muted);
                    font-size:.78rem;
                    margin-bottom:1rem;
                ">
                Validasi akad dilakukan melalui tiga tahap pemeriksaan
                utama sebelum monitoring usaha diaktifkan.
            </p>

            <div class="validation-process">
                <div class="validation-step">
                    <span class="validation-step-number">
                        1
                    </span>

                    <div class="validation-step-icon">
                        <i class="bi bi-file-earmark-check"></i>
                    </div>

                    <h6>
                        Pemeriksaan Dokumen Akad
                    </h6>

                    <p>
                        DPS memeriksa dokumen dan jenis akad yang
                        digunakan oleh usaha anggota.
                    </p>
                </div>

                <div class="validation-step">
                    <span class="validation-step-number">
                        2
                    </span>

                    <div class="validation-step-icon">
                        <i class="bi bi-patch-check"></i>
                    </div>

                    <h6>
                        Kesesuaian Prinsip Syariah
                    </h6>

                    <p>
                        DPS memastikan ketentuan akad tidak bertentangan
                        dengan prinsip dan ketentuan syariah.
                    </p>
                </div>

                <div class="validation-step">
                    <span class="validation-step-number">
                        3
                    </span>

                    <div class="validation-step-icon">
                        <i class="bi bi-broadcast-pin"></i>
                    </div>

                    <h6>
                        Aktivasi Monitoring
                    </h6>

                    <p>
                        Ketika akad disetujui, usaha menjadi aktif dan
                        periode monitoring 14 hari otomatis dimulai.
                    </p>
                </div>
            </div>

            <div class="validation-results">
                <span
                    style="
                        color:var(--text-muted);
                        font-size:.75rem;
                        font-weight:700;
                        margin-right:.2rem;
                    ">
                    Hasil akhir:
                </span>

                <span class="badge-soft tint-emerald">
                    <i class="bi bi-check-circle-fill me-1"></i>
                    Disetujui
                </span>

                <span class="badge-soft tint-rose">
                    <i class="bi bi-arrow-repeat me-1"></i>
                    Perlu Revisi
                </span>

                <span class="badge-soft tint-amber">
                    <i class="bi bi-hourglass-split me-1"></i>
                    Menunggu
                </span>
            </div>
        </div>
    </div>
@endsection
