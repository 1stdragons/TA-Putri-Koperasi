@extends('layouts.app')

@section('title', 'Riwayat Evaluasi Saya')
@section('page-title', 'Riwayat Evaluasi Saya')
@section('page-subtitle', 'Hasil evaluasi 14 hari perkembangan usaha Anda')

@section('sidebar-menu')
    @include('anggota.partials._sidebar')
@endsection

@section('content')

    {{-- ── Statistik Pribadi ────────────────────────────────────── --}}
    @if ($stats && $stats->total > 0)
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="stat-label">Total Evaluasi</div>
                                <div class="stat-value text-indigo">{{ number_format($stats->total) }}</div>
                                <div class="stat-sub">Semua periode</div>
                            </div>
                            <div class="stat-icon tint-indigo"><i class="bi bi-clipboard2-data-fill"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="stat-label">Aktif</div>
                                <div class="stat-value text-emerald">{{ number_format($stats->aktif) }}</div>
                                <div class="stat-sub">Kedua indikator ✓</div>
                            </div>
                            <div class="stat-icon tint-emerald"><i class="bi bi-check-circle-fill"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="stat-label">Peringatan</div>
                                <div class="stat-value text-amber">{{ number_format($stats->peringatan) }}</div>
                                <div class="stat-sub">Perlu ditingkatkan</div>
                            </div>
                            <div class="stat-icon tint-amber"><i class="bi bi-exclamation-circle-fill"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="stat-label">Tidak Aktif</div>
                                <div class="stat-value text-rose">{{ number_format($stats->tidak_aktif) }}</div>
                                <div class="stat-sub">Butuh perhatian</div>
                            </div>
                            <div class="stat-icon tint-rose"><i class="bi bi-x-circle-fill"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Box Metode Evaluasi --}}
        <div class="mb-3 p-3 d-flex align-items-start gap-2"
            style="background:linear-gradient(135deg,var(--primary-light),#ede9fe);
            border-radius:var(--radius-md); border-left:4px solid var(--primary);">
            <i class="bi bi-info-circle-fill"
                style="color:var(--primary); font-size:1rem; flex-shrink:0; margin-top:2px;"></i>
            <div style="font-size:.82rem; color:var(--primary-dark); line-height:1.6;">
                <strong>Metode Evaluasi 14 Hari:</strong>
                Evaluasi dihitung otomatis setelah laporan disetujui Pengurus <strong>dan</strong> akad disetujui DPS.
                <br>
                ✅ <strong>Aktif</strong>: Kenaikan omzet ≥ 5% <em>DAN</em> Rasio biaya &lt; 60% omzet. &nbsp;
                ⚠️ <strong>Peringatan</strong>: Salah satu tidak terpenuhi. &nbsp;
                ❌ <strong>Tidak Aktif</strong>: Kedua indikator tidak terpenuhi.
            </div>
        </div>
    @endif

    {{-- ── Filter ───────────────────────────────────────────────── --}}
    <div class="filter-toolbar mb-3">
        <form action="{{ route('anggota.evaluasi.index') }}" method="GET" id="formFilter">
            <div class="row g-2">
                <div class="col-12 col-md-5">
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama usaha...">
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <select name="hasil" class="form-select" onchange="document.getElementById('formFilter').submit()">
                        <option value="">Semua Hasil</option>
                        <option value="aktif" {{ $filterHasil === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="peringatan" {{ $filterHasil === 'peringatan' ? 'selected' : '' }}>Peringatan
                        </option>
                        <option value="tidak_aktif" {{ $filterHasil === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif
                        </option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="periode" class="form-select" onchange="document.getElementById('formFilter').submit()">
                        <option value="">Semua Periode</option>
                        @foreach ($periodeList as $val => $label)
                            <option value="{{ $val }}" {{ $filterPeriode === $val ? 'selected' : '' }}>
                                {{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit"
                        class="btn-theme-primary flex-fill d-flex align-items-center justify-content-center">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    @if ($search || $filterHasil || $filterPeriode)
                        <a href="{{ route('anggota.evaluasi.index') }}" class="btn-theme-light" title="Reset">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- ── Daftar Evaluasi (Card Style) ───────────────────────── --}}
    @if ($evaluasiList->isEmpty())
        <div class="card">
            <div class="card-body">
                <div class="theme-empty-state">
                    <div class="empty-icon"><i class="bi bi-clipboard2-x"></i></div>
                    <h6>Belum ada riwayat evaluasi</h6>
                    <p>
                        @if ($search || $filterHasil || $filterPeriode)
                            Tidak ditemukan evaluasi yang sesuai filter Anda.
                        @else
                            Evaluasi akan muncul setelah laporan Anda disetujui pengurus dan akad usaha disetujui DPS.
                        @endif
                    </p>
                    @if ($search || $filterHasil || $filterPeriode)
                        <a href="{{ route('anggota.evaluasi.index') }}" class="btn-theme-light">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                        </a>
                    @else
                        <a href="{{ route('anggota.laporan.create') }}" class="btn-theme-primary">
                            <i class="bi bi-plus-lg me-1"></i> Input Laporan Sekarang
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach ($evaluasiList as $ev)
                @php
                    $eTint = $ev->hasil_evaluasi_tint;
                    $eGradient = match ($ev->hasil_evaluasi) {
                        'aktif' => 'linear-gradient(120deg, #047857, #10b981)',
                        'peringatan' => 'linear-gradient(120deg, #b45309, #f59e0b)',
                        'tidak_aktif' => 'linear-gradient(120deg, #be123c, #f43f5e)',
                        default => 'linear-gradient(120deg, var(--primary), #818cf8)',
                    };
                    $emoji = match ($ev->hasil_evaluasi) {
                        'aktif' => '✅',
                        'peringatan' => '⚠️',
                        'tidak_aktif' => '❌',
                        default => '—',
                    };
                @endphp
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card h-100" style="transition:transform .2s, box-shadow .2s;"
                        onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 16px 32px rgba(99,102,241,.14)';"
                        onmouseout="this.style.transform=''; this.style.boxShadow='';">

                        {{-- Header Gradient --}}
                        <div
                            style="background:{{ $eGradient }}; border-radius:var(--radius-lg) var(--radius-lg) 0 0;
                                padding:1.25rem; position:relative; overflow:hidden;">
                            <div
                                style="position:absolute; width:80px; height:80px; background:rgba(255,255,255,.1);
                                    border-radius:50%; top:-30px; right:-20px;">
                            </div>
                            <div class="d-flex align-items-center justify-content-between"
                                style="position:relative; z-index:1;">
                                <div>
                                    <div style="font-size:.72rem; color:rgba(255,255,255,.8); font-weight:600;">
                                        {{ $ev->nama_usaha }}
                                    </div>
                                    <div style="font-weight:800; color:#fff; font-size:1rem; margin:.15rem 0;">
                                        {{ $emoji }} {{ $ev->hasil_evaluasi_label }}
                                    </div>
                                    <div style="font-size:.72rem; color:rgba(255,255,255,.85);">
                                        Periode ke-{{ $ev->periode_ke }}
                                        &bull;
                                        {{ \Carbon\Carbon::parse($ev->tanggal_mulai)->format('d/m/Y') }}
                                        – {{ \Carbon\Carbon::parse($ev->tanggal_selesai)->format('d/m/Y') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row g-2 mb-3">
                                {{-- Kenaikan Omzet --}}
                                <div class="col-6">
                                    <div
                                        style="background:{{ $ev->indikator_omzet ? 'linear-gradient(135deg,#ecfdf5,#d1fae5)' : 'linear-gradient(135deg,#fff1f2,#ffe4e6)' }};
                                            border-radius:var(--radius-sm); padding:.65rem; text-align:center;">
                                        <div
                                            style="font-size:.66rem; font-weight:700; text-transform:uppercase; letter-spacing:.3px;
                                                color:{{ $ev->indikator_omzet ? '#047857' : '#be123c' }}; margin-bottom:.2rem;">
                                            Kenaikan Omzet
                                        </div>
                                        <div
                                            style="font-size:.9rem; font-weight:800;
                                                color:{{ $ev->indikator_omzet ? '#047857' : '#be123c' }};">
                                            {{ $ev->persentase_kenaikan_omzet }}%
                                        </div>
                                        <div
                                            style="font-size:.66rem; color:{{ $ev->indikator_omzet ? '#047857' : '#be123c' }}; opacity:.75;">
                                            {{ $ev->indikator_omzet ? '✓ ≥ 5%' : '✗ &lt; 5%' }}
                                        </div>
                                    </div>
                                </div>

                                {{-- Rasio Biaya --}}
                                <div class="col-6">
                                    <div
                                        style="background:{{ $ev->indikator_biaya ? 'linear-gradient(135deg,#ecfdf5,#d1fae5)' : 'linear-gradient(135deg,#fff1f2,#ffe4e6)' }};
                                            border-radius:var(--radius-sm); padding:.65rem; text-align:center;">
                                        <div
                                            style="font-size:.66rem; font-weight:700; text-transform:uppercase; letter-spacing:.3px;
                                                color:{{ $ev->indikator_biaya ? '#047857' : '#be123c' }}; margin-bottom:.2rem;">
                                            Rasio Biaya
                                        </div>
                                        <div
                                            style="font-size:.9rem; font-weight:800;
                                                color:{{ $ev->indikator_biaya ? '#047857' : '#be123c' }};">
                                            {{ $ev->rasio_biaya }}%
                                        </div>
                                        <div
                                            style="font-size:.66rem; color:{{ $ev->indikator_biaya ? '#047857' : '#be123c' }}; opacity:.75;">
                                            {{ $ev->indikator_biaya ? '✓ &lt; 60%' : '✗ ≥ 60%' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Ringkasan Keuangan --}}
                            <div class="row g-1 mb-3" style="font-size:.78rem;">
                                <div class="col-4 text-center">
                                    <div
                                        style="color:var(--text-muted); font-size:.67rem; font-weight:600; margin-bottom:.15rem;">
                                        Omzet</div>
                                    <div style="font-weight:700; color:#047857;">
                                        Rp {{ number_format($ev->omzet_sekarang / 1000000, 1) }}jt
                                    </div>
                                </div>
                                <div class="col-4 text-center">
                                    <div
                                        style="color:var(--text-muted); font-size:.67rem; font-weight:600; margin-bottom:.15rem;">
                                        Biaya</div>
                                    <div style="font-weight:700; color:#be123c;">
                                        Rp {{ number_format($ev->biaya_operasional / 1000000, 1) }}jt
                                    </div>
                                </div>
                                <div class="col-4 text-center">
                                    <div
                                        style="color:var(--text-muted); font-size:.67rem; font-weight:600; margin-bottom:.15rem;">
                                        Sebelumnya</div>
                                    <div style="font-weight:700; color:var(--text-dark);">
                                        @if ($ev->omzet_sebelumnya > 0)
                                            Rp {{ number_format($ev->omzet_sebelumnya / 1000000, 1) }}jt
                                        @else
                                            <span style="color:var(--text-light); font-size:.74rem;">Pertama</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Validator --}}
                            @if ($ev->validator)
                                <div style="font-size:.74rem; color:var(--text-muted);">
                                    <i class="bi bi-person-check me-1"></i>
                                    Divalidasi: {{ $ev->validator->nama }}
                                    @if ($ev->tanggal_validasi_pengurus)
                                        — {{ \Carbon\Carbon::parse($ev->tanggal_validasi_pengurus)->format('d/m/Y') }}
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="card-footer">
                            <a href="{{ route('anggota.evaluasi.show', $ev->id) }}"
                                class="btn-theme-primary w-100 d-flex align-items-center justify-content-center"
                                style="padding:.5rem; font-size:.82rem;">
                                <i class="bi bi-eye me-1"></i> Lihat Detail Evaluasi
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if ($evaluasiList->hasPages())
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3">
                <span style="font-size:.78rem; color:var(--text-muted); font-weight:500;">
                    Menampilkan {{ $evaluasiList->firstItem() }}–{{ $evaluasiList->lastItem() }}
                    dari {{ $evaluasiList->total() }} evaluasi
                </span>
                {{ $evaluasiList->links() }}
            </div>
        @endif
    @endif

    <div class="theme-loading-overlay" id="loadingOverlay">
        <div class="theme-spinner"></div>
        <span>Memuat data...</span>
    </div>

@endsection

@push('scripts')
    <script>
        document.getElementById('formFilter').addEventListener('submit', () => {
            document.getElementById('loadingOverlay').classList.add('show');
        });
    </script>
@endpush
