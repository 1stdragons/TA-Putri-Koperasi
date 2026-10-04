@extends('layouts.app')

@section('title', 'Usaha Saya')
@section('page-title', 'Usaha Saya')
@section('page-subtitle', 'Daftar usaha yang Anda miliki')

@section('sidebar-menu')
    @include('anggota.partials._sidebar')
@endsection

@section('content')

    {{-- Header halaman --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div class="section-title mb-0">
            <i class="bi bi-shop-window"></i>
            Daftar Usaha Anda
        </div>

        @if ($bolehTambahUsaha)
            <a href="{{ route('anggota.usaha-saya.create') }}"
                class="btn-theme-primary d-flex align-items-center justify-content-center"
                style="padding:.55rem 1rem; font-size:.82rem;">
                <i class="bi bi-plus-circle-fill me-1"></i>
                Tambah Usaha Baru
            </a>
        @endif
    </div>

    {{-- Informasi aturan penambahan usaha --}}
    @if (!$bolehTambahUsaha && $usahaList->isNotEmpty())
        <div class="alert border-0 mb-3"
            style="background:linear-gradient(135deg,#fffbeb,#fef3c7);
                color:#92400e; border-radius:var(--radius-md);">
            <div class="d-flex align-items-start gap-2">
                <i class="bi bi-info-circle-fill mt-1"></i>
                <div style="font-size:.82rem; line-height:1.55;">
                    Anda dapat menambahkan usaha berikutnya setelah memiliki minimal satu usaha aktif
                    yang akadnya sudah disetujui DPS.
                </div>
            </div>
        </div>
    @endif

    @if ($usahaList->isEmpty())
        <div class="card">
            <div class="card-body">
                <div class="theme-empty-state">
                    <div class="empty-icon">
                        <i class="bi bi-shop"></i>
                    </div>
                    <h6>Belum ada data usaha</h6>
                    <p>
                        Usaha pertama harus didaftarkan oleh Admin.
                        Silakan hubungi Admin agar data usaha dan dokumen akad Anda dapat didaftarkan.
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach ($usahaList as $usaha)
                @php
                    $sTint = $usaha->status_usaha_tint;
                    $vTint = $usaha->status_validasi_syariah_tint;

                    $bolehInputLaporan =
                        $usaha->status_usaha === 'aktif' && $usaha->status_validasi_syariah === 'disetujui';
                @endphp

                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card h-100" style="transition:transform .2s, box-shadow .2s;"
                        onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 16px 32px rgba(99,102,241,.14)';"
                        onmouseout="this.style.transform=''; this.style.boxShadow='';">

                        {{-- Header kartu usaha --}}
                        <div
                            style="background:linear-gradient(120deg, var(--primary) 0%, #818cf8 70%, var(--secondary) 130%);
                                border-radius:var(--radius-lg) var(--radius-lg) 0 0;
                                padding:1.2rem 1.25rem; position:relative; overflow:hidden;">
                            <div
                                style="position:absolute; width:80px; height:80px; background:rgba(255,255,255,.1);
                                    border-radius:50%; top:-30px; right:-20px;">
                            </div>

                            <div
                                style="font-weight:800; color:#fff; font-size:.95rem;
                                    position:relative; z-index:1;">
                                {{ $usaha->nama_usaha }}
                            </div>

                            <div
                                style="font-size:.76rem; color:rgba(255,255,255,.85);
                                    position:relative; z-index:1; margin-top:.2rem;">
                                {{ $usaha->jenis_usaha_label }}
                            </div>

                            <div
                                style="position:relative; z-index:1; margin-top:.6rem;
                                    display:flex; gap:.4rem; flex-wrap:wrap;">
                                <span class="badge-soft tint-{{ $sTint }}"
                                    style="background:rgba(255,255,255,.2) !important;
                                        color:#fff !important;
                                        border:1px solid rgba(255,255,255,.3);
                                        font-size:.68rem;">
                                    <i class="bi {{ $usaha->status_usaha_icon }} me-1"></i>
                                    {{ $usaha->status_usaha_label }}
                                </span>
                            </div>
                        </div>

                        <div class="card-body">
                            {{-- Jenis akad --}}
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-patch-check-fill" style="color:var(--primary); font-size:.9rem;"></i>

                                <div>
                                    <div
                                        style="font-size:.7rem; color:var(--text-muted);
                                            font-weight:600;">
                                        Jenis Akad
                                    </div>

                                    <div
                                        style="font-size:.82rem; font-weight:700;
                                            color:var(--text-dark);">
                                        {{ $usaha->jenis_akad_label }}
                                    </div>
                                </div>
                            </div>

                            {{-- Status validasi syariah --}}
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-shield-check" style="color:var(--success); font-size:.9rem;"></i>

                                <div>
                                    <div
                                        style="font-size:.7rem; color:var(--text-muted);
                                            font-weight:600;">
                                        Validasi Syariah
                                    </div>

                                    <span class="badge-soft tint-{{ $vTint }}"
                                        style="font-size:.72rem; margin-top:2px;
                                            display:inline-block;">
                                        <i class="bi {{ $usaha->status_validasi_syariah_icon }} me-1"></i>
                                        {{ $usaha->status_validasi_syariah_label }}
                                    </span>
                                </div>
                            </div>

                            {{-- Dokumen akad --}}
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-file-earmark-text" style="color:var(--text-muted); font-size:.9rem;"></i>

                                <div style="min-width:0;">
                                    <div
                                        style="font-size:.7rem; color:var(--text-muted);
                                            font-weight:600;">
                                        Dokumen Akad
                                    </div>

                                    @if ($usaha->has_file_akad)
                                        <a href="{{ $usaha->file_akad_url }}" target="_blank" rel="noopener noreferrer"
                                            style="font-size:.78rem; color:var(--primary);
                                                font-weight:600; text-decoration:none;">
                                            <i class="bi {{ $usaha->file_akad_icon }} me-1"></i>
                                            Lihat Dokumen
                                        </a>
                                    @else
                                        <div
                                            style="font-size:.78rem;
                                                color:var(--text-light);">
                                            Belum diunggah
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Modal dan jumlah laporan --}}
                            <div class="row g-2 mt-1">
                                <div class="col-6">
                                    <div
                                        style="background:var(--bg-soft);
                                            border-radius:var(--radius-sm);
                                            padding:.6rem;">
                                        <div
                                            style="font-size:.68rem;
                                                color:var(--text-muted);
                                                font-weight:600;">
                                            Modal Awal
                                        </div>

                                        <div
                                            style="font-size:.82rem;
                                                font-weight:700;
                                                color:var(--text-dark);">
                                            Rp {{ number_format($usaha->modal_awal, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div
                                        style="background:var(--bg-soft);
                                            border-radius:var(--radius-sm);
                                            padding:.6rem;">
                                        <div
                                            style="font-size:.68rem;
                                                color:var(--text-muted);
                                                font-weight:600;">
                                            Total Laporan
                                        </div>

                                        <div
                                            style="font-size:.82rem;
                                                font-weight:700;
                                                color:var(--primary);">
                                            {{ $usaha->laporan_count }} laporan
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Informasi jika belum bisa monitoring --}}
                            @if (!$bolehInputLaporan)
                                <div class="mt-3 p-2"
                                    style="background:var(--bg-soft);
                                        border-radius:var(--radius-sm);
                                        font-size:.72rem;
                                        color:var(--text-muted);
                                        line-height:1.45;">
                                    <i class="bi bi-lock-fill me-1"></i>
                                    Laporan dapat dibuat setelah akad disetujui DPS
                                    dan usaha berstatus aktif.
                                </div>
                            @endif
                        </div>

                        <div class="card-footer d-flex gap-2">
                            <a href="{{ route('anggota.usaha-saya.show', $usaha->id) }}"
                                class="btn-theme-primary flex-fill d-flex align-items-center justify-content-center"
                                style="padding:.5rem; font-size:.8rem;">
                                <i class="bi bi-eye me-1"></i>
                                Detail
                            </a>

                            @if ($bolehInputLaporan)
                                <a href="{{ route('anggota.laporan.create', ['usaha_id' => $usaha->id]) }}"
                                    class="btn-theme-light flex-fill d-flex align-items-center justify-content-center"
                                    style="padding:.5rem; font-size:.8rem;">
                                    <i class="bi bi-plus-circle me-1"></i>
                                    Laporan
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

@endsection
