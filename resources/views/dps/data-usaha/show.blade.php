@extends('layouts.app')

@section('title', 'Detail Data Usaha')
@section('page-title', 'Detail Data Usaha')
@section('page-subtitle', $usaha->nama_usaha . ' — ' . $usaha->anggota->akun->nama)

@section('sidebar-menu')
    @include('dps.partials._sidebar')
@endsection

@section('content')
    {{-- Tombol kembali dan keterangan hak akses --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <a href="{{ route('dps.data-usaha.index') }}" class="btn-theme-light" style="padding:.4rem .9rem; font-size:.83rem;">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali
        </a>

        <span class="badge-soft tint-indigo">
            <i class="bi bi-eye me-1"></i>
            Informasi read-only
        </span>
    </div>

    <div class="row g-3">
        {{-- =========================================================
            KOLOM KIRI
        ========================================================== --}}
        <div class="col-12 col-lg-4">
            {{-- Header usaha --}}
            <div class="card mb-3" style="overflow:hidden;">
                <div
                    style="
                        background:linear-gradient(
                            120deg,
                            var(--primary) 0%,
                            #818cf8 60%,
                            var(--secondary) 130%
                        );
                        padding:1.75rem;
                        text-align:center;
                        position:relative;
                        overflow:hidden;
                    ">
                    <div
                        style="
                            position:absolute;
                            width:140px;
                            height:140px;
                            background:rgba(255,255,255,.1);
                            border-radius:50%;
                            top:-60px;
                            right:-40px;
                        ">
                    </div>

                    <div
                        style="
                            width:72px;
                            height:72px;
                            border-radius:18px;
                            background:rgba(255,255,255,.18);
                            border:2px solid rgba(255,255,255,.4);
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            font-size:2rem;
                            color:#fff;
                            margin:0 auto .9rem;
                            position:relative;
                            z-index:1;
                        ">
                        <i class="bi bi-shop-window"></i>
                    </div>

                    <h5
                        style="
                            font-weight:800;
                            color:#fff;
                            margin-bottom:.25rem;
                            position:relative;
                            z-index:1;
                        ">
                        {{ $usaha->nama_usaha }}
                    </h5>

                    <div
                        style="
                            font-size:.8rem;
                            color:rgba(255,255,255,.85);
                            margin-bottom:.75rem;
                            position:relative;
                            z-index:1;
                        ">
                        {{ $usaha->anggota->akun->nama }}

                        &nbsp;&bull;&nbsp;

                        <span style="font-family:monospace;">
                            {{ $usaha->anggota->nomor_anggota }}
                        </span>
                    </div>

                    <div class="d-flex justify-content-center gap-2 flex-wrap" style="position:relative; z-index:1;">
                        <span class="badge-soft tint-{{ $usaha->status_usaha_tint }}"
                            style="
                                font-size:.76rem;
                                backdrop-filter:blur(6px);
                                background:rgba(255,255,255,.2) !important;
                                color:#fff !important;
                                border:1px solid rgba(255,255,255,.35);
                            ">
                            <i class="bi {{ $usaha->status_usaha_icon }} me-1"></i>

                            {{ $usaha->status_usaha_label }}
                        </span>
                    </div>
                </div>

                <div class="card-body py-3">
                    <div class="text-center">
                        <div
                            style="
                                font-size:.7rem;
                                font-weight:700;
                                text-transform:uppercase;
                                letter-spacing:.4px;
                                color:var(--text-muted);
                                margin-bottom:.5rem;
                            ">
                            Status Validasi Syariah
                        </div>

                        <span class="badge-soft tint-{{ $usaha->status_validasi_syariah_tint }}"
                            style="
                                font-size:.82rem;
                                padding:.45rem 1.1rem;
                            ">
                            <i class="bi {{ $usaha->status_validasi_syariah_icon }} me-1"></i>

                            {{ $usaha->status_validasi_syariah_label }}
                        </span>

                        <div
                            style="
                                font-size:.72rem;
                                color:var(--text-muted);
                                margin-top:.5rem;
                            ">
                            <i class="bi bi-info-circle me-1"></i>
                            Status akad dan monitoring ditampilkan terpisah
                        </div>
                    </div>
                </div>
            </div>

            {{-- Status monitoring --}}
            @php
                $monitoringAktif =
                    $usaha->status_validasi_syariah === 'disetujui' &&
                    in_array($usaha->status_usaha, ['aktif', 'vakum'], true) &&
                    !empty($usaha->tanggal_aktivasi_monitoring);

                $monitoringSelesai = $usaha->status_usaha === 'tutup';
            @endphp

            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-activity me-2" style="color:var(--primary);"></i>
                    Status Monitoring
                </div>

                <div class="card-body">
                    <div class="text-center mb-3">
                        @if ($monitoringSelesai)
                            <span class="badge-soft tint-rose" style="font-size:.84rem; padding:.5rem 1rem;">
                                <i class="bi bi-stop-circle me-1"></i>
                                Monitoring Selesai
                            </span>
                        @elseif ($monitoringAktif)
                            <span class="badge-soft tint-emerald" style="font-size:.84rem; padding:.5rem 1rem;">
                                <i class="bi bi-broadcast-pin me-1"></i>
                                Monitoring Aktif
                            </span>
                        @else
                            <span class="badge-soft tint-amber" style="font-size:.84rem; padding:.5rem 1rem;">
                                <i class="bi bi-hourglass-split me-1"></i>
                                Monitoring Belum Aktif
                            </span>
                        @endif
                    </div>

                    <div class="akun-info-row px-0">
                        <div class="akun-info-icon tint-indigo">
                            <i class="bi bi-calendar-check-fill"></i>
                        </div>

                        <div>
                            <div class="akun-info-label">
                                Tanggal Aktivasi
                            </div>

                            <div class="akun-info-value">
                                @if ($usaha->tanggal_aktivasi_monitoring)
                                    {{ $usaha->tanggal_aktivasi_monitoring->translatedFormat('d F Y, H:i') }}
                                @else
                                    <span style="color:var(--text-light);">
                                        Belum tersedia
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="akun-info-row px-0" style="border-bottom:none;">
                        <div class="akun-info-icon tint-violet">
                            <i class="bi bi-clock-history"></i>
                        </div>

                        <div>
                            <div class="akun-info-label">
                                Periode Monitoring
                            </div>

                            <div class="akun-info-value">
                                Setiap 14 hari
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Statistik usaha --}}
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-bar-chart-fill me-2" style="color:var(--primary);"></i>
                    Statistik Usaha
                </div>

                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="akun-stat-mini">
                                <div class="num">
                                    {{ $jumlahLaporan }}
                                </div>

                                <div class="label">
                                    Laporan
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="akun-stat-mini">
                                <div class="num"
                                    style="
                                        font-size:1rem;
                                        letter-spacing:-.3px;
                                    ">
                                    Rp
                                    {{ $totalOmzet >= 1000000
                                        ? number_format($totalOmzet / 1000000, 1, ',', '.') . 'jt'
                                        : number_format($totalOmzet, 0, ',', '.') }}
                                </div>

                                <div class="label">
                                    Total Omzet
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="akun-stat-mini">
                                <div class="num"
                                    style="
                                        font-size:1rem;
                                        letter-spacing:-.3px;
                                    ">
                                    Rp
                                    {{ $totalBiaya >= 1000000
                                        ? number_format($totalBiaya / 1000000, 1, ',', '.') . 'jt'
                                        : number_format($totalBiaya, 0, ',', '.') }}
                                </div>

                                <div class="label">
                                    Total Biaya Operasional
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Evaluasi terakhir --}}
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-clipboard2-check-fill me-2" style="color:var(--success);"></i>
                    Evaluasi 14 Hari Terakhir
                </div>

                @if ($evaluasiTerakhir)
                    @php
                        $evaluasiTint = match ($evaluasiTerakhir->hasil_evaluasi) {
                            'aktif' => 'emerald',
                            'peringatan' => 'amber',
                            'tidak_aktif' => 'rose',
                            default => 'slate',
                        };
                    @endphp

                    <div class="card-body">
                        <div class="d-flex flex-column gap-3">
                            <div>
                                <div
                                    style="
                                        font-size:.7rem;
                                        color:var(--text-muted);
                                        font-weight:700;
                                        text-transform:uppercase;
                                        letter-spacing:.3px;
                                        margin-bottom:.35rem;
                                    ">
                                    Hasil Evaluasi
                                </div>

                                <span class="badge-soft tint-{{ $evaluasiTint }}" style="font-size:.82rem;">
                                    {{ $evaluasiTerakhir->hasil_evaluasi_label }}
                                </span>
                            </div>

                            <div class="row g-2">
                                <div class="col-6">
                                    <div
                                        style="
                                            font-size:.7rem;
                                            color:var(--text-muted);
                                            font-weight:700;
                                            text-transform:uppercase;
                                            letter-spacing:.3px;
                                            margin-bottom:.25rem;
                                        ">
                                        Kenaikan Omzet
                                    </div>

                                    <div
                                        style="
                                            font-size:.88rem;
                                            font-weight:700;
                                            color:var(--text-dark);
                                        ">
                                        {{ $evaluasiTerakhir->persentase_kenaikan_omzet }}%

                                        @if ($evaluasiTerakhir->indikator_omzet)
                                            <i class="bi bi-check-circle-fill ms-1" style="color:var(--success);"></i>
                                        @else
                                            <i class="bi bi-x-circle-fill ms-1" style="color:var(--danger);"></i>
                                        @endif
                                    </div>

                                    <div
                                        style="
                                            font-size:.7rem;
                                            color:var(--text-muted);
                                        ">
                                        Target ≥ 5%
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div
                                        style="
                                            font-size:.7rem;
                                            color:var(--text-muted);
                                            font-weight:700;
                                            text-transform:uppercase;
                                            letter-spacing:.3px;
                                            margin-bottom:.25rem;
                                        ">
                                        Rasio Biaya
                                    </div>

                                    <div
                                        style="
                                            font-size:.88rem;
                                            font-weight:700;
                                            color:var(--text-dark);
                                        ">
                                        {{ $evaluasiTerakhir->rasio_biaya }}%

                                        @if ($evaluasiTerakhir->indikator_biaya)
                                            <i class="bi bi-check-circle-fill ms-1" style="color:var(--success);"></i>
                                        @else
                                            <i class="bi bi-x-circle-fill ms-1" style="color:var(--danger);"></i>
                                        @endif
                                    </div>

                                    <div
                                        style="
                                            font-size:.7rem;
                                            color:var(--text-muted);
                                        ">
                                        Target &lt; 60%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card-body p-0">
                        <div class="theme-empty-state" style="padding:1.8rem 1rem;">
                            <div class="empty-icon"
                                style="
                                    width:52px;
                                    height:52px;
                                    font-size:1.4rem;
                                ">
                                <i class="bi bi-clipboard2-x"></i>
                            </div>

                            <h6 style="font-size:.85rem;">
                                Belum ada evaluasi
                            </h6>

                            <p style="font-size:.76rem;">
                                Evaluasi muncul setelah laporan divalidasi Pengurus.
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Informasi anggota --}}
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-person-badge-fill me-2" style="color:var(--accent);"></i>
                    Informasi Anggota
                </div>

                <div class="card-body p-0">
                    @php
                        $anggota = $usaha->anggota;

                        $anggotaTint = match ($anggota->status_anggota) {
                            'aktif' => 'emerald',
                            'peringatan' => 'amber',
                            'tidak_aktif' => 'rose',
                            default => 'slate',
                        };
                    @endphp

                    <div class="akun-info-row px-3">
                        <div class="akun-info-icon tint-indigo">
                            <i class="bi bi-person-fill"></i>
                        </div>

                        <div>
                            <div class="akun-info-label">
                                Nama Anggota
                            </div>

                            <div class="akun-info-value">
                                {{ $anggota->akun->nama }}
                            </div>
                        </div>
                    </div>

                    <div class="akun-info-row px-3">
                        <div class="akun-info-icon tint-violet">
                            <i class="bi bi-hash"></i>
                        </div>

                        <div>
                            <div class="akun-info-label">
                                Nomor Anggota
                            </div>

                            <div class="akun-info-value">
                                <span class="code-tag">
                                    {{ $anggota->nomor_anggota }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="akun-info-row px-3">
                        <div class="akun-info-icon tint-slate">
                            <i class="bi bi-credit-card-fill"></i>
                        </div>

                        <div>
                            <div class="akun-info-label">
                                NIK
                            </div>

                            <div class="akun-info-value">
                                <span class="code-tag">
                                    {{ $anggota->nik }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="akun-info-row px-3">
                        <div class="akun-info-icon tint-emerald">
                            <i class="bi bi-telephone-fill"></i>
                        </div>

                        <div>
                            <div class="akun-info-label">
                                Telepon
                            </div>

                            <div class="akun-info-value">
                                {{ $anggota->telepon ?: '—' }}
                            </div>
                        </div>
                    </div>

                    <div class="akun-info-row px-3" style="border-bottom:none;">
                        <div class="akun-info-icon tint-{{ $anggotaTint }}">
                            <i class="bi bi-toggle-on"></i>
                        </div>

                        <div>
                            <div class="akun-info-label">
                                Status Anggota
                            </div>

                            <div class="akun-info-value">
                                <span class="badge-soft tint-{{ $anggotaTint }}">
                                    {{ $anggota->status_anggota_label }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- =========================================================
            KOLOM KANAN
        ========================================================== --}}
        <div class="col-12 col-lg-8">
            {{-- Informasi usaha --}}
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-info-circle-fill me-2" style="color:var(--primary);"></i>
                    Informasi Usaha
                </div>

                <div class="card-body p-0">
                    <div class="akun-info-row px-4">
                        <div class="akun-info-icon tint-cyan">
                            <i class="bi bi-grid-3x3-gap-fill"></i>
                        </div>

                        <div>
                            <div class="akun-info-label">
                                Jenis Usaha
                            </div>

                            <div class="akun-info-value">
                                <span class="badge-soft tint-{{ $usaha->jenis_usaha_tint }}">
                                    {{ $usaha->jenis_usaha_label }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="akun-info-row px-4">
                        <div class="akun-info-icon tint-violet">
                            <i class="bi bi-patch-check-fill"></i>
                        </div>

                        <div>
                            <div class="akun-info-label">
                                Jenis Akad Syariah
                            </div>

                            <div class="akun-info-value">
                                @if ($usaha->jenis_akad)
                                    <span class="badge-soft tint-{{ $usaha->jenis_akad_tint }}">
                                        {{ $usaha->jenis_akad_label }}
                                    </span>
                                @else
                                    <span style="color:var(--text-light);">
                                        Belum ditentukan
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="akun-info-row px-4">
                        <div class="akun-info-icon tint-emerald">
                            <i class="bi bi-cash-stack"></i>
                        </div>

                        <div>
                            <div class="akun-info-label">
                                Modal Awal
                            </div>

                            <div class="akun-info-value">
                                Rp {{ number_format($usaha->modal_awal, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <div class="akun-info-row px-4">
                        <div class="akun-info-icon tint-amber">
                            <i class="bi bi-calendar-event-fill"></i>
                        </div>

                        <div>
                            <div class="akun-info-label">
                                Tanggal Mulai
                            </div>

                            <div class="akun-info-value">
                                @if ($usaha->tanggal_mulai)
                                    {{ $usaha->tanggal_mulai->translatedFormat('d F Y') }}

                                    <span
                                        style="
                                            font-size:.78rem;
                                            color:var(--text-muted);
                                            font-weight:500;
                                            margin-left:.4rem;
                                        ">
                                        ({{ $usaha->tanggal_mulai->diffForHumans() }})
                                    </span>
                                @else
                                    <span style="color:var(--text-light);">
                                        —
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="akun-info-row px-4">
                        <div class="akun-info-icon tint-slate">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>

                        <div>
                            <div class="akun-info-label">
                                Alamat Usaha
                            </div>

                            <div class="akun-info-value" style="line-height:1.55;">
                                {{ $usaha->alamat_usaha ?: '—' }}
                            </div>
                        </div>
                    </div>

                    <div class="akun-info-row px-4">
                        <div class="akun-info-icon tint-blue">
                            <i class="bi bi-card-text"></i>
                        </div>

                        <div>
                            <div class="akun-info-label">
                                Deskripsi
                            </div>

                            <div class="akun-info-value"
                                style="
                                    line-height:1.6;
                                    font-weight:500;
                                ">
                                {{ $usaha->deskripsi ?: '—' }}
                            </div>
                        </div>
                    </div>

                    <div class="akun-info-row px-4" style="border-bottom:none;">
                        <div class="akun-info-icon tint-pink">
                            <i class="bi bi-sticky-fill"></i>
                        </div>

                        <div>
                            <div class="akun-info-label">
                                Catatan
                            </div>

                            <div class="akun-info-value"
                                style="
                                    line-height:1.6;
                                    font-weight:500;
                                ">
                                {{ $usaha->catatan ?: '—' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Dokumen akad --}}
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center justify-content-between gap-2">
                    <span>
                        <i class="bi bi-file-earmark-text-fill me-2" style="color:#047857;"></i>
                        Dokumen Ketentuan Akad
                    </span>

                    <span class="badge-soft tint-{{ $usaha->status_validasi_syariah_tint }}" style="font-size:.72rem;">
                        <i class="bi {{ $usaha->status_validasi_syariah_icon }} me-1"></i>

                        {{ $usaha->status_validasi_syariah_label }}
                    </span>
                </div>

                <div class="card-body">
                    @if ($usaha->has_file_akad)
                        <div class="d-flex align-items-center gap-3"
                            style="
                                background:var(--bg-soft);
                                border-radius:var(--radius-md);
                                padding:1rem 1.1rem;
                            ">
                            <i class="bi {{ $usaha->file_akad_icon }}"
                                style="
                                    font-size:2.2rem;
                                    flex-shrink:0;
                                    color:
                                    {{ $usaha->file_akad_extension === 'pdf' ? '#dc2626' : '#2563eb' }};
                                "></i>

                            <div style="flex:1; min-width:0;">
                                <div
                                    style="
                                        font-size:.85rem;
                                        font-weight:700;
                                        color:var(--text-dark);
                                        overflow:hidden;
                                        text-overflow:ellipsis;
                                        white-space:nowrap;
                                    ">
                                    {{ $usaha->file_akad }}
                                </div>

                                <div
                                    style="
                                        font-size:.74rem;
                                        color:var(--text-muted);
                                        margin-top:.2rem;
                                    ">
                                    <span class="badge-soft tint-slate" style="font-size:.68rem;">
                                        {{ strtoupper($usaha->file_akad_extension) }}
                                    </span>
                                </div>
                            </div>

                            <a href="{{ $usaha->file_akad_url }}" target="_blank" rel="noopener noreferrer"
                                class="btn-theme-primary"
                                style="
                                    padding:.45rem 1rem;
                                    font-size:.8rem;
                                    flex-shrink:0;
                                ">
                                <i class="bi bi-eye me-1"></i>
                                Lihat Dokumen
                            </a>
                        </div>
                    @else
                        <div class="theme-empty-state" style="padding:2rem 1rem;">
                            <div class="empty-icon"
                                style="
                                    width:60px;
                                    height:60px;
                                    font-size:1.6rem;
                                ">
                                <i class="bi bi-file-earmark-x"></i>
                            </div>

                            <h6 style="font-size:.88rem;">
                                Dokumen akad belum diunggah
                            </h6>

                            <p style="font-size:.78rem;">
                                Dokumen ketentuan akad belum tersedia pada data usaha ini.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Riwayat laporan --}}
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span>
                        <i class="bi bi-journal-richtext me-2" style="color:var(--primary);"></i>
                        Laporan Usaha 14 Hari
                    </span>

                    <span class="badge-soft tint-indigo" style="font-size:.72rem;">
                        {{ $jumlahLaporan }} laporan
                    </span>
                </div>

                <div class="card-body p-0">
                    @forelse ($usaha->laporan
                                    ->sortByDesc('periode_ke')
                                    ->take(5)
                                as $laporan)
                        @php
                            $laporanTint = match ($laporan->status_laporan) {
                                'divalidasi' => 'emerald',
                                'dikirim' => 'blue',
                                'ditolak' => 'rose',
                                default => 'slate',
                            };
                        @endphp

                        <div class="d-flex align-items-center gap-3 px-4 py-3" style="border-bottom:1px solid #f6f7fb;">
                            <div
                                style="
                                    width:38px;
                                    height:38px;
                                    border-radius:10px;
                                    flex-shrink:0;
                                    background:linear-gradient(
                                        135deg,
                                        #eef2ff,
                                        #e0e7ff
                                    );
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    font-size:.85rem;
                                    color:var(--primary);
                                    font-weight:800;
                                ">
                                {{ $laporan->periode_ke }}
                            </div>

                            <div style="flex:1; min-width:0;">
                                <div
                                    style="
                                        font-size:.82rem;
                                        font-weight:700;
                                        color:var(--text-dark);
                                    ">
                                    Periode ke-{{ $laporan->periode_ke }}
                                </div>

                                <div
                                    style="
                                        font-size:.72rem;
                                        color:var(--text-muted);
                                    ">
                                    {{ $laporan->tanggal_mulai->format('d/m/Y') }}
                                    &mdash;
                                    {{ $laporan->tanggal_selesai->format('d/m/Y') }}
                                </div>

                                <div
                                    style="
                                        font-size:.72rem;
                                        color:var(--text-muted);
                                        margin-top:.15rem;
                                    ">
                                    Biaya operasional:
                                    Rp {{ number_format($laporan->biaya_operasional, 0, ',', '.') }}
                                </div>
                            </div>

                            <div
                                style="
                                    text-align:right;
                                    flex-shrink:0;
                                ">
                                <div
                                    style="
                                        font-size:.82rem;
                                        font-weight:700;
                                        color:var(--text-dark);
                                    ">
                                    Rp {{ number_format($laporan->omzet, 0, ',', '.') }}
                                </div>

                                <span class="badge-soft tint-{{ $laporanTint }}" style="font-size:.68rem;">
                                    {{ $laporan->status_laporan_label }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="theme-empty-state" style="padding:2rem 1rem;">
                            <div class="empty-icon"
                                style="
                                    width:60px;
                                    height:60px;
                                    font-size:1.5rem;
                                ">
                                <i class="bi bi-journal-x"></i>
                            </div>

                            <h6 style="font-size:.88rem;">
                                Belum ada laporan
                            </h6>

                            <p style="font-size:.78rem;">
                                Laporan usaha 14 hari akan muncul setelah anggota mengirim laporan.
                            </p>
                        </div>
                    @endforelse

                    @if ($jumlahLaporan > 5)
                        <div class="card-footer text-center">
                            <span
                                style="
                                    font-size:.78rem;
                                    color:var(--text-muted);
                                ">
                                Menampilkan 5 laporan terbaru dari
                                {{ $jumlahLaporan }} laporan.
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Riwayat validasi akad --}}
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span>
                        <i class="bi bi-shield-check me-2" style="color:#047857;"></i>
                        Riwayat Validasi Akad Syariah
                    </span>

                    <span class="badge-soft tint-slate" style="font-size:.72rem;">
                        {{ $usaha->validasiAkad->count() }} iterasi
                    </span>
                </div>

                <div class="card-body p-0">
                    @forelse ($usaha->validasiAkad->sortByDesc('iterasi')
                                as $validasiAkad)
                        @php
                            $validasiTint = match ($validasiAkad->status) {
                                'disetujui' => 'emerald',
                                'perlu_revisi' => 'rose',
                                default => 'amber',
                            };

                            $validasiIcon = match ($validasiAkad->status) {
                                'disetujui' => 'bi-patch-check-fill',
                                'perlu_revisi' => 'bi-arrow-repeat',
                                default => 'bi-hourglass-split',
                            };
                        @endphp

                        <div class="px-4 py-3" style="border-bottom:1px solid #f6f7fb;">
                            <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div
                                        style="
                                            width:32px;
                                            height:32px;
                                            border-radius:8px;
                                            flex-shrink:0;
                                            background:linear-gradient(
                                                135deg,
                                                #eef2ff,
                                                #e0e7ff
                                            );
                                            display:flex;
                                            align-items:center;
                                            justify-content:center;
                                            font-size:.78rem;
                                            color:var(--primary);
                                            font-weight:800;
                                        ">
                                        #{{ $validasiAkad->iterasi }}
                                    </div>

                                    <div>
                                        <div
                                            style="
                                                font-size:.82rem;
                                                font-weight:700;
                                                color:var(--text-dark);
                                            ">
                                            Iterasi ke-{{ $validasiAkad->iterasi }}
                                        </div>

                                        <div
                                            style="
                                                font-size:.72rem;
                                                color:var(--text-muted);
                                            ">
                                            {{ $validasiAkad->created_at->translatedFormat('d F Y, H:i') }}
                                        </div>
                                    </div>
                                </div>

                                <span class="badge-soft tint-{{ $validasiTint }}" style="flex-shrink:0;">
                                    <i class="bi {{ $validasiIcon }} me-1"></i>

                                    {{ $validasiAkad->status_label }}
                                </span>
                            </div>

                            @if ($validasiAkad->dps)
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <img src="{{ $validasiAkad->dps->foto_url }}" class="avatar-sm"
                                        alt="{{ $validasiAkad->dps->nama }}">

                                    <div>
                                        <div
                                            style="
                                                font-size:.78rem;
                                                font-weight:700;
                                                color:var(--text-dark);
                                            ">
                                            {{ $validasiAkad->dps->nama }}
                                        </div>

                                        <div
                                            style="
                                                font-size:.7rem;
                                                color:var(--text-muted);
                                            ">
                                            Dewan Pengawas Syariah
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($validasiAkad->divalidasi_pada)
                                <div
                                    style="
                                        font-size:.74rem;
                                        color:var(--text-muted);
                                        margin-bottom:.4rem;
                                    ">
                                    <i class="bi bi-calendar-check me-1"></i>

                                    Divalidasi:
                                    {{ $validasiAkad->divalidasi_pada->translatedFormat('d F Y, H:i') }}
                                </div>
                            @endif

                            @if ($validasiAkad->catatan_dps)
                                <div
                                    style="
                                        background:var(--bg-soft);
                                        border-radius:var(--radius-sm);
                                        padding:.55rem .75rem;
                                        font-size:.78rem;
                                        color:var(--text-dark);
                                        line-height:1.5;
                                        border-left:3px solid
                                        {{ $validasiAkad->status === 'disetujui'
                                            ? '#10b981'
                                            : ($validasiAkad->status === 'perlu_revisi'
                                                ? '#f43f5e'
                                                : '#f59e0b') }};
                                    ">
                                    <div
                                        style="
                                            font-size:.68rem;
                                            font-weight:700;
                                            text-transform:uppercase;
                                            letter-spacing:.3px;
                                            color:var(--text-muted);
                                            margin-bottom:.25rem;
                                        ">
                                        Catatan DPS
                                    </div>

                                    {{ $validasiAkad->catatan_dps }}
                                </div>
                            @endif

                            @if ($validasiAkad->file_akad_divalidasi)
                                <div class="mt-2">
                                    <a href="{{ asset('uploads/akad/' . $validasiAkad->file_akad_divalidasi) }}"
                                        target="_blank" rel="noopener noreferrer"
                                        style="
                                            font-size:.76rem;
                                            color:var(--primary);
                                            font-weight:600;
                                            text-decoration:none;
                                            display:inline-flex;
                                            align-items:center;
                                            gap:.3rem;
                                        ">
                                        <i class="bi bi-file-earmark-text"></i>
                                        Lihat dokumen akad iterasi ini
                                    </a>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="theme-empty-state" style="padding:2rem 1rem;">
                            <div class="empty-icon"
                                style="
                                    width:60px;
                                    height:60px;
                                    font-size:1.5rem;
                                ">
                                <i class="bi bi-shield-x"></i>
                            </div>

                            <h6 style="font-size:.88rem;">
                                Belum ada riwayat validasi
                            </h6>

                            <p style="font-size:.78rem;">
                                Riwayat validasi akan muncul setelah dokumen akad diunggah.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
