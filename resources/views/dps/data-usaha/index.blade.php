@extends('layouts.app')

@section('title', 'Data Usaha')
@section('page-title', 'Data Usaha')
@section('page-subtitle', 'Informasi usaha dan perkembangan monitoring anggota koperasi')

@section('sidebar-menu')
    @include('dps.partials._sidebar')
@endsection

@section('content')
    {{-- Ringkasan statistik usaha --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card h-100 border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0"
                        style="
                            width:48px;
                            height:48px;
                            background:#eef2ff;
                            color:#4f46e5;
                            font-size:1.25rem;
                        ">
                        <i class="bi bi-shop-window"></i>
                    </div>

                    <div>
                        <div class="text-muted small">
                            Total Usaha
                        </div>

                        <div class="fs-4 fw-bold">
                            {{ number_format($totalUsaha) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card h-100 border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0"
                        style="
                            width:48px;
                            height:48px;
                            background:#ecfdf5;
                            color:#059669;
                            font-size:1.25rem;
                        ">
                        <i class="bi bi-activity"></i>
                    </div>

                    <div>
                        <div class="text-muted small">
                            Monitoring Aktif
                        </div>

                        <div class="fs-4 fw-bold">
                            {{ number_format($monitoringAktif) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card h-100 border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0"
                        style="
                            width:48px;
                            height:48px;
                            background:#fffbeb;
                            color:#d97706;
                            font-size:1.25rem;
                        ">
                        <i class="bi bi-hourglass-split"></i>
                    </div>

                    <div>
                        <div class="text-muted small">
                            Belum Aktif
                        </div>

                        <div class="fs-4 fw-bold">
                            {{ number_format($belumAktif) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card h-100 border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0"
                        style="
                            width:48px;
                            height:48px;
                            background:#fef2f2;
                            color:#dc2626;
                            font-size:1.25rem;
                        ">
                        <i class="bi bi-stop-circle"></i>
                    </div>

                    <div>
                        <div class="text-muted small">
                            Usaha Tutup
                        </div>

                        <div class="fs-4 fw-bold">
                            {{ number_format($usahaTutup) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Judul daftar --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div class="section-title mb-0">
            <i class="bi bi-shop-window"></i>
            Daftar Usaha Anggota
        </div>

        <span class="badge-soft tint-indigo">
            <i class="bi bi-eye me-1"></i>
            Akses hanya lihat
        </span>
    </div>

    {{-- Toolbar pencarian dan filter --}}
    <div class="filter-toolbar mb-3">
        <form action="{{ route('dps.data-usaha.index') }}" method="GET" id="formFilter">
            <div class="row g-2">
                <div class="col-12 col-lg-4">
                    <div class="search-box">
                        <i class="bi bi-search"></i>

                        <input type="text" name="search" value="{{ $search }}"
                            placeholder="Cari usaha, anggota, atau nomor anggota...">
                    </div>
                </div>

                <div class="col-6 col-lg-2">
                    <select name="status_monitoring" class="form-select"
                        onchange="document.getElementById('formFilter').submit()">
                        <option value="">
                            Semua Monitoring
                        </option>

                        <option value="aktif" {{ $filterMonitoring === 'aktif' ? 'selected' : '' }}>
                            Monitoring Aktif
                        </option>

                        <option value="belum_aktif" {{ $filterMonitoring === 'belum_aktif' ? 'selected' : '' }}>
                            Belum Aktif
                        </option>

                        <option value="selesai" {{ $filterMonitoring === 'selesai' ? 'selected' : '' }}>
                            Usaha Tutup
                        </option>
                    </select>
                </div>

                <div class="col-6 col-lg-2">
                    <select name="status_usaha" class="form-select"
                        onchange="document.getElementById('formFilter').submit()">
                        <option value="">
                            Semua Status Usaha
                        </option>

                        <option value="belum_aktif" {{ $filterStatus === 'belum_aktif' ? 'selected' : '' }}>
                            Belum Aktif
                        </option>

                        <option value="aktif" {{ $filterStatus === 'aktif' ? 'selected' : '' }}>
                            Aktif
                        </option>

                        <option value="vakum" {{ $filterStatus === 'vakum' ? 'selected' : '' }}>
                            Vakum
                        </option>

                        <option value="tutup" {{ $filterStatus === 'tutup' ? 'selected' : '' }}>
                            Tutup
                        </option>
                    </select>
                </div>

                <div class="col-6 col-lg-2">
                    <select name="status_syariah" class="form-select"
                        onchange="document.getElementById('formFilter').submit()">
                        <option value="">
                            Semua Status Syariah
                        </option>

                        <option value="belum_diupload" {{ $filterSyariah === 'belum_diupload' ? 'selected' : '' }}>
                            Belum Upload
                        </option>

                        <option value="menunggu" {{ $filterSyariah === 'menunggu' ? 'selected' : '' }}>
                            Menunggu DPS
                        </option>

                        <option value="disetujui" {{ $filterSyariah === 'disetujui' ? 'selected' : '' }}>
                            Disetujui
                        </option>

                        <option value="perlu_revisi" {{ $filterSyariah === 'perlu_revisi' ? 'selected' : '' }}>
                            Perlu Revisi
                        </option>
                    </select>
                </div>

                <div class="col-6 col-lg-2">
                    <div class="d-flex gap-2 h-100">
                        <button type="submit" class="btn-theme-primary flex-grow-1">
                            <i class="bi bi-funnel me-1"></i>
                            Filter
                        </button>

                        @if ($search || $filterStatus || $filterSyariah || $filterMonitoring)
                            <a href="{{ route('dps.data-usaha.index') }}" class="btn-theme-light" title="Reset filter">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Tabel data usaha --}}
    <div class="card">
        <div class="table-responsive" style="max-height:680px; overflow-y:auto;">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th style="min-width:190px;">
                            Usaha
                        </th>

                        <th style="min-width:170px;">
                            Anggota
                        </th>

                        <th>
                            Akad
                        </th>

                        <th>
                            Status Usaha
                        </th>

                        <th style="min-width:155px;">
                            Status Syariah
                        </th>

                        <th style="min-width:155px;">
                            Monitoring
                        </th>

                        <th class="text-center">
                            Laporan
                        </th>

                        <th style="min-width:130px;">
                            Aktivasi
                        </th>

                        <th class="text-center">
                            Detail
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($usahaList as $usaha)
                        @php
                            $monitoringAktifBaris =
                                $usaha->status_validasi_syariah === 'disetujui' &&
                                in_array($usaha->status_usaha, ['aktif', 'vakum'], true) &&
                                !empty($usaha->tanggal_aktivasi_monitoring);

                            $monitoringSelesai = $usaha->status_usaha === 'tutup';
                        @endphp

                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0"
                                        style="
                                            width:40px;
                                            height:40px;
                                            background:#eef2ff;
                                            color:#4f46e5;
                                        ">
                                        <i class="bi bi-shop-window"></i>
                                    </div>

                                    <div>
                                        <div class="fw-bold" style="font-size:.84rem;">
                                            {{ $usaha->nama_usaha }}
                                        </div>

                                        <div class="text-muted" style="font-size:.72rem;">
                                            {{ $usaha->jenis_usaha_label ?? ucfirst($usaha->jenis_usaha) }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $usaha->anggota->akun->foto_url }}"
                                        alt="{{ $usaha->anggota->akun->nama }}" class="avatar-sm">

                                    <div>
                                        <div class="fw-bold" style="font-size:.82rem;">
                                            {{ $usaha->anggota->akun->nama }}
                                        </div>

                                        <div class="text-muted" style="font-size:.72rem;">
                                            {{ $usaha->anggota->nomor_anggota }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                @if ($usaha->jenis_akad)
                                    <span class="badge-soft tint-{{ $usaha->jenis_akad_tint }}">
                                        {{ $usaha->jenis_akad_label }}
                                    </span>
                                @else
                                    <span class="text-muted">
                                        —
                                    </span>
                                @endif
                            </td>

                            <td>
                                <span class="badge-soft tint-{{ $usaha->status_usaha_tint }}">
                                    <i class="bi {{ $usaha->status_usaha_icon }} me-1"></i>

                                    {{ $usaha->status_usaha_label }}
                                </span>
                            </td>

                            <td>
                                <span class="badge-soft tint-{{ $usaha->status_validasi_syariah_tint }}">
                                    <i class="bi {{ $usaha->status_validasi_syariah_icon }} me-1"></i>

                                    {{ $usaha->status_validasi_syariah_label }}
                                </span>
                            </td>

                            <td>
                                @if ($monitoringSelesai)
                                    <span class="badge-soft tint-rose">
                                        <i class="bi bi-stop-circle me-1"></i>
                                        Selesai
                                    </span>
                                @elseif ($monitoringAktifBaris)
                                    <span class="badge-soft tint-emerald">
                                        <i class="bi bi-broadcast-pin me-1"></i>
                                        Aktif
                                    </span>
                                @else
                                    <span class="badge-soft tint-amber">
                                        <i class="bi bi-hourglass-split me-1"></i>
                                        Belum Aktif
                                    </span>
                                @endif
                            </td>

                            <td class="text-center">
                                <span class="badge-soft tint-indigo"
                                    style="
                                        min-width:38px;
                                        justify-content:center;
                                    ">
                                    {{ $usaha->laporan_count ?? 0 }}
                                </span>
                            </td>

                            <td>
                                @if ($usaha->tanggal_aktivasi_monitoring)
                                    <div class="fw-semibold" style="font-size:.8rem;">
                                        {{ $usaha->tanggal_aktivasi_monitoring->format('d/m/Y') }}
                                    </div>

                                    <div class="text-muted" style="font-size:.7rem;">
                                        {{ $usaha->tanggal_aktivasi_monitoring->diffForHumans() }}
                                    </div>
                                @else
                                    <span class="text-muted" style="font-size:.78rem;">
                                        —
                                    </span>
                                @endif
                            </td>

                            <td class="text-center">
                                <a href="{{ route('dps.data-usaha.show', $usaha->id) }}" class="icon-action-btn btn-view"
                                    title="Detail Data Usaha">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="theme-empty-state">
                                    <div class="empty-icon">
                                        <i class="bi bi-shop"></i>
                                    </div>

                                    <h6>
                                        Data usaha belum tersedia
                                    </h6>

                                    <p>
                                        @if ($search || $filterStatus || $filterSyariah || $filterMonitoring)
                                            Tidak ditemukan data usaha yang sesuai dengan pencarian atau filter.
                                        @else
                                            Data akan muncul setelah usaha anggota didaftarkan oleh Admin.
                                        @endif
                                    </p>

                                    @if ($search || $filterStatus || $filterSyariah || $filterMonitoring)
                                        <a href="{{ route('dps.data-usaha.index') }}" class="btn-theme-light">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                                            Reset Filter
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($usahaList->hasPages())
            <div class="card-footer d-flex flex-wrap align-items-center justify-content-between gap-2 px-3 py-3">
                <span class="text-muted" style="font-size:.78rem; font-weight:500;">
                    Menampilkan
                    {{ $usahaList->firstItem() }}–{{ $usahaList->lastItem() }}
                    dari {{ $usahaList->total() }} data usaha
                </span>

                {{ $usahaList->links() }}
            </div>
        @endif
    </div>

    {{-- Loading ketika filter dijalankan --}}
    <div class="theme-loading-overlay" id="loadingOverlay">
        <div class="theme-spinner"></div>
        <span>Memuat data usaha...</span>
    </div>
@endsection

@push('scripts')
    <script>
        const formFilter = document.getElementById('formFilter');
        const loadingOverlay = document.getElementById('loadingOverlay');

        if (formFilter && loadingOverlay) {
            formFilter.addEventListener('submit', function() {
                loadingOverlay.classList.add('show');
            });
        }
    </script>
@endpush
