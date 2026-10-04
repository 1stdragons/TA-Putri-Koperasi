@extends('layouts.app')

@section('title', 'Detail Akun')
@section('page-title', 'Detail Akun')
@section('page-subtitle', 'Informasi lengkap akun ' . $akun->nama)

@section('sidebar-menu')
    @include('admin.partials._sidebar')
@endsection

@section('content')

    <div class="mb-3">
        <a href="{{ route('admin.akun.index') }}" class="text-decoration-none"
            style="font-size:.85rem; font-weight:600; color:#6366f1;">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Akun
        </a>
    </div>

    <div class="row g-3 justify-content-center">
        <div class="col-12 col-lg-8">

            {{-- ── Header Profil ─────────────────────────────────── --}}
            <div class="akun-detail-header mb-3">
                <img src="{{ $akun->foto_url }}" alt="{{ $akun->nama }}">
                <h5>{{ $akun->nama }}</h5>
                <span class="role-pill">
                    <i class="bi bi-shield-check me-1"></i> {{ $akun->role_label }}
                </span>
            </div>

            {{-- ── Statistik Mini ────────────────────────────────── --}}
            <div class="row g-3 mb-3">
                <div class="col-6 col-md-3">
                    <div class="akun-stat-mini">
                        <div class="num">{{ $akun->aktivitas_log_count }}</div>
                        <div class="label">Aktivitas</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="akun-stat-mini">
                        <div class="num">
                            @if ($akun->status === 'aktif')
                                <i class="bi bi-check-circle-fill text-success"></i>
                            @else
                                <i class="bi bi-x-circle-fill text-danger"></i>
                            @endif
                        </div>
                        <div class="label">{{ $akun->status === 'aktif' ? 'Aktif' : 'Nonaktif' }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="akun-stat-mini">
                        <div class="num" style="font-size:1rem;">{{ $akun->created_at->format('d/m/Y') }}</div>
                        <div class="label">Bergabung</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="akun-stat-mini">
                        <div class="num" style="font-size:1rem;">
                            {{ $akun->last_login ? $akun->last_login->format('d/m/Y') : '-' }}
                        </div>
                        <div class="label">Login Terakhir</div>
                    </div>
                </div>
            </div>

            {{-- ── Informasi Detail ──────────────────────────────── --}}
            <div class="akun-detail-info">
                <h6 class="fw-bold mb-2" style="color:#1e1b4b; font-size:.9rem;">
                    <i class="bi bi-info-circle-fill text-primary me-1"></i> Informasi Akun
                </h6>

                <div class="akun-info-row">
                    <div class="akun-info-icon tint-indigo"><i class="bi bi-person-fill"></i></div>
                    <div>
                        <div class="akun-info-label">Nama Lengkap</div>
                        <div class="akun-info-value">{{ $akun->nama }}</div>
                    </div>
                </div>

                <div class="akun-info-row">
                    <div class="akun-info-icon tint-cyan"><i class="bi bi-envelope-fill"></i></div>
                    <div>
                        <div class="akun-info-label">Email</div>
                        <div class="akun-info-value">{{ $akun->email }}</div>
                    </div>
                </div>

                <div class="akun-info-row">
                    <div class="akun-info-icon tint-pink"><i class="bi bi-shield-fill"></i></div>
                    <div>
                        <div class="akun-info-label">Role</div>
                        <div class="akun-info-value">{{ $akun->role_label }}</div>
                    </div>
                </div>

                <div class="akun-info-row">
                    <div class="akun-info-icon tint-emerald"><i class="bi bi-toggle-on"></i></div>
                    <div>
                        <div class="akun-info-label">Status</div>
                        <div class="akun-info-value">{{ $akun->status === 'aktif' ? 'Aktif' : 'Nonaktif' }}</div>
                    </div>
                </div>

                <div class="akun-info-row">
                    <div class="akun-info-icon tint-amber"><i class="bi bi-calendar-plus"></i></div>
                    <div>
                        <div class="akun-info-label">Tanggal Dibuat</div>
                        <div class="akun-info-value">{{ $akun->created_at->translatedFormat('d F Y, H:i') }} WIB</div>
                    </div>
                </div>

                <div class="akun-info-row">
                    <div class="akun-info-icon tint-slate"><i class="bi bi-clock-history"></i></div>
                    <div>
                        <div class="akun-info-label">Login Terakhir</div>
                        <div class="akun-info-value">
                            {{ $akun->last_login ? $akun->last_login->translatedFormat('d F Y, H:i') . ' WIB' : 'Belum pernah login' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Aksi ───────────────────────────────────────────── --}}
            <div class="d-flex gap-2 mt-3">
                <a href="{{ route('admin.akun.edit', $akun->id) }}" class="btn-theme-primary">
                    <i class="bi bi-pencil-square me-1"></i> Edit Akun
                </a>
                <a href="{{ route('admin.akun.index') }}" class="btn-theme-light">
                    Kembali
                </a>
            </div>
        </div>
    </div>

@endsection
