@extends('layouts.app')

@section('title', 'Detail Usaha')
@section('page-title', 'Detail Usaha Saya')
@section('page-subtitle', $usaha->nama_usaha)

@section('sidebar-menu')
    @include('anggota.partials._sidebar')
@endsection

@section('content')

    <div class="mb-3 d-flex gap-2">
        <a href="{{ route('anggota.usaha-saya.index') }}" class="btn-theme-light"
            style="padding:.4rem .9rem; font-size:.83rem;">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        @if ($usaha->status_usaha === 'aktif')
            <a href="{{ route('anggota.laporan.create', ['usaha_id' => $usaha->id]) }}" class="btn-theme-primary"
                style="padding:.4rem .9rem; font-size:.83rem;">
                <i class="bi bi-plus-lg me-1"></i> Input Laporan
            </a>
        @endif
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-4">

            {{-- Profile Card Usaha --}}
            <div class="card mb-3" style="overflow:hidden;">
                <div
                    style="background:linear-gradient(120deg, var(--primary), #818cf8 60%, var(--secondary) 130%);
                        padding:1.75rem; text-align:center; position:relative; overflow:hidden;">
                    <div
                        style="position:absolute; width:120px; height:120px; background:rgba(255,255,255,.1);
                            border-radius:50%; top:-50px; right:-30px;">
                    </div>
                    <div
                        style="width:64px; height:64px; border-radius:16px; background:rgba(255,255,255,.18);
                            border:2px solid rgba(255,255,255,.4);
                            display:flex; align-items:center; justify-content:center;
                            font-size:1.75rem; color:#fff; margin:0 auto .75rem; position:relative; z-index:1;">
                        <i class="bi bi-shop-window"></i>
                    </div>
                    <h5
                        style="font-weight:800; color:#fff; font-size:1rem; margin-bottom:.25rem; position:relative; z-index:1;">
                        {{ $usaha->nama_usaha }}
                    </h5>
                    <div
                        style="font-size:.78rem; color:rgba(255,255,255,.85); margin-bottom:.7rem; position:relative; z-index:1;">
                        {{ $usaha->jenis_usaha_label }}
                    </div>
                    <div
                        style="position:relative; z-index:1; display:flex; justify-content:center; gap:.4rem; flex-wrap:wrap;">
                        <span class="badge-soft"
                            style="background:rgba(255,255,255,.2) !important; color:#fff !important; border:1px solid rgba(255,255,255,.3);">
                            <i class="bi {{ $usaha->status_usaha_icon }} me-1"></i>{{ $usaha->status_usaha_label }}
                        </span>
                    </div>
                </div>

                {{-- Statistik mini --}}
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="akun-stat-mini">
                                <div class="num">{{ $jumlahLaporan }}</div>
                                <div class="label">Laporan</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="akun-stat-mini">
                                <div class="num" style="font-size:.95rem;">
                                    Rp {{ number_format($usaha->modal_awal / 1000000, 1) }}jt
                                </div>
                                <div class="label">Modal Awal</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Validasi Syariah --}}
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-shield-check me-2" style="color:#047857;"></i>Status Syariah
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <span class="badge-soft tint-{{ $usaha->status_validasi_syariah_tint }}"
                            style="font-size:.85rem; padding:.45rem 1.1rem;">
                            <i class="bi {{ $usaha->status_validasi_syariah_icon }} me-1"></i>
                            {{ $usaha->status_validasi_syariah_label }}
                        </span>
                    </div>
                    @if ($usaha->has_file_akad)
                        <div class="d-flex align-items-center gap-2 p-2"
                            style="background:var(--bg-soft); border-radius:var(--radius-sm);">
                            <i class="bi {{ $usaha->file_akad_icon }}"
                                style="font-size:1.4rem; flex-shrink:0; color:{{ $usaha->file_akad_extension === 'pdf' ? '#dc2626' : '#2563eb' }};"></i>
                            <div style="flex:1; min-width:0;">
                                <div
                                    style="font-size:.76rem; font-weight:700; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                    Dokumen Akad
                                </div>
                            </div>
                            <a href="{{ $usaha->file_akad_url }}" target="_blank" class="btn-theme-light"
                                style="padding:.25rem .6rem; font-size:.74rem; flex-shrink:0;">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    @endif
                    @if ($usaha->validasiAkadTerakhir)
                        @php $va = $usaha->validasiAkadTerakhir; @endphp
                        <div class="mt-2" style="font-size:.76rem; color:var(--text-muted);">
                            <div>Iterasi ke-{{ $va->iterasi }}</div>
                            @if ($va->catatan_dps)
                                <div class="mt-1 p-2"
                                    style="background:var(--bg-soft); border-radius:var(--radius-sm);
                                        border-left:3px solid {{ $va->status === 'disetujui' ? '#10b981' : '#f59e0b' }};
                                        font-size:.74rem; color:var(--text-dark); line-height:1.5;">
                                    <strong>Catatan DPS:</strong> {{ $va->catatan_dps }}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">

            {{-- Informasi Lengkap Usaha --}}
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-info-circle-fill me-2" style="color:var(--primary);"></i>Informasi Usaha
                </div>
                <div class="card-body p-0">
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
                            <div class="akun-info-label">Jenis Akad Syariah</div>
                            <div class="akun-info-value">
                                @if ($usaha->jenis_akad)
                                    <span
                                        class="badge-soft tint-{{ $usaha->jenis_akad_tint }}">{{ $usaha->jenis_akad }}</span>
                                @else
                                    <span style="color:var(--text-light);">Belum ditentukan</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="akun-info-row px-4">
                        <div class="akun-info-icon tint-emerald"><i class="bi bi-cash-stack"></i></div>
                        <div>
                            <div class="akun-info-label">Modal Awal</div>
                            <div class="akun-info-value">Rp {{ number_format($usaha->modal_awal, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    @if ($usaha->tanggal_mulai)
                        <div class="akun-info-row px-4">
                            <div class="akun-info-icon tint-amber"><i class="bi bi-calendar-event-fill"></i></div>
                            <div>
                                <div class="akun-info-label">Tanggal Mulai</div>
                                <div class="akun-info-value">{{ $usaha->tanggal_mulai->translatedFormat('d F Y') }}</div>
                            </div>
                        </div>
                    @endif
                    @if ($usaha->alamat_usaha)
                        <div class="akun-info-row px-4">
                            <div class="akun-info-icon tint-slate"><i class="bi bi-geo-alt-fill"></i></div>
                            <div>
                                <div class="akun-info-label">Alamat Usaha</div>
                                <div class="akun-info-value" style="font-weight:500; line-height:1.55;">
                                    {{ $usaha->alamat_usaha }}</div>
                            </div>
                        </div>
                    @endif
                    @if ($usaha->deskripsi)
                        <div class="akun-info-row px-4">
                            <div class="akun-info-icon tint-blue"><i class="bi bi-card-text"></i></div>
                            <div>
                                <div class="akun-info-label">Deskripsi</div>
                                <div class="akun-info-value" style="font-weight:500; line-height:1.6;">
                                    {{ $usaha->deskripsi }}</div>
                            </div>
                        </div>
                    @endif
                    @if ($usaha->catatan)
                        <div class="akun-info-row px-4" style="border-bottom:none;">
                            <div class="akun-info-icon tint-pink"><i class="bi bi-sticky-fill"></i></div>
                            <div>
                                <div class="akun-info-label">Catatan</div>
                                <div class="akun-info-value" style="font-weight:500; line-height:1.6;">
                                    {{ $usaha->catatan }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Laporan Terbaru --}}
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span>
                        <i class="bi bi-journal-richtext me-2" style="color:var(--primary);"></i>Laporan Terbaru
                    </span>
                    <a href="{{ route('anggota.laporan.index') }}" class="btn-theme-light"
                        style="padding:.3rem .75rem; font-size:.76rem;">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body p-0">
                    @forelse ($usaha->laporan->take(5) as $laporan)
                        <div class="d-flex align-items-center gap-3 px-4 py-3" style="border-bottom:1px solid #f6f7fb;">
                            <div
                                style="width:40px; height:40px; border-radius:10px; flex-shrink:0;
                                    background:linear-gradient(135deg,#eef2ff,#e0e7ff);
                                    display:flex; align-items:center; justify-content:center;
                                    font-size:.85rem; color:var(--primary); font-weight:800;">
                                {{ $laporan->periode_ke }}
                            </div>
                            <div style="flex:1; min-width:0;">
                                <div style="font-size:.82rem; font-weight:700; color:var(--text-dark);">
                                    Periode ke-{{ $laporan->periode_ke }}
                                </div>
                                <div style="font-size:.72rem; color:var(--text-muted);">
                                    {{ $laporan->tanggal_mulai->format('d/m/Y') }} –
                                    {{ $laporan->tanggal_selesai->format('d/m/Y') }}
                                </div>
                            </div>
                            <div style="text-align:right; flex-shrink:0;">
                                <div style="font-size:.82rem; font-weight:700; color:#047857;">
                                    Rp {{ number_format($laporan->omzet, 0, ',', '.') }}
                                </div>
                                <span class="badge-soft tint-{{ $laporan->status_laporan_tint }}"
                                    style="font-size:.68rem;">
                                    {{ $laporan->status_laporan_label }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="theme-empty-state" style="padding:2rem 1rem;">
                            <div class="empty-icon" style="width:52px; height:52px; font-size:1.4rem;"><i
                                    class="bi bi-journal-x"></i></div>
                            <h6 style="font-size:.86rem;">Belum ada laporan</h6>
                            <p style="font-size:.76rem;">Mulai input laporan monitoring pertama.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

@endsection
