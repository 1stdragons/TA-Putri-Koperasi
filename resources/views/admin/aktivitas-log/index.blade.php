@extends('layouts.app')

@section('title', 'Log Aktivitas')
@section('page-title', 'Log Aktivitas')
@section('page-subtitle', 'Riwayat aktivitas seluruh pengguna sistem')

@section('sidebar-menu')
    @include('admin.partials._sidebar')
@endsection

@section('content')

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-house-door me-1"></i>Dashboard
                </a>
            </li>
            <li class="breadcrumb-item active">
                Log Aktivitas
            </li>
        </ol>
    </nav>

    {{-- Ringkasan --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stat-label">Total Ditampilkan</div>
                            <div class="stat-value text-indigo">
                                {{ number_format($aktivitasLogs->total()) }}
                            </div>
                            <div class="stat-sub">
                                Berdasarkan filter saat ini
                            </div>
                        </div>

                        <div class="stat-icon tint-indigo">
                            <i class="bi bi-clock-history"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stat-label">Halaman Aktif</div>
                            <div class="stat-value text-cyan">
                                {{ $aktivitasLogs->currentPage() }}
                            </div>
                            <div class="stat-sub">
                                Dari {{ $aktivitasLogs->lastPage() }} halaman
                            </div>
                        </div>

                        <div class="stat-icon tint-cyan">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stat-label">Jenis Aktivitas</div>
                            <div class="stat-value text-emerald">
                                {{ number_format($daftarAktivitas->count()) }}
                            </div>
                            <div class="stat-sub">
                                Aktivitas berbeda tercatat
                            </div>
                        </div>

                        <div class="stat-icon tint-emerald">
                            <i class="bi bi-activity"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card mb-4">
        <div class="card-header">
            <span>
                <i class="bi bi-funnel-fill me-2" style="color:var(--primary);"></i>
                Filter Log Aktivitas
            </span>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.aktivitas-log.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-12 col-lg-4">
                        <label for="search" class="form-label">
                            Cari Data
                        </label>

                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>

                            <input type="text" name="search" id="search" class="form-control"
                                value="{{ $search }}" placeholder="Nama, email, aktivitas, atau IP...">
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-3">
                        <label for="aktivitas" class="form-label">
                            Jenis Aktivitas
                        </label>

                        <select name="aktivitas" id="aktivitas" class="form-select">
                            <option value="">Semua Aktivitas</option>

                            @foreach ($daftarAktivitas as $aktivitas)
                                <option value="{{ $aktivitas }}" @selected($filterAktivitas === $aktivitas)>
                                    {{ $aktivitas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-6 col-lg-2">
                        <label for="role" class="form-label">
                            Role
                        </label>

                        <select name="role" id="role" class="form-select">
                            <option value="">Semua Role</option>
                            <option value="admin" @selected($filterRole === 'admin')>
                                Administrator
                            </option>
                            <option value="anggota" @selected($filterRole === 'anggota')>
                                Anggota
                            </option>
                            <option value="pengurus" @selected($filterRole === 'pengurus')>
                                Pengurus
                            </option>
                            <option value="dps" @selected($filterRole === 'dps')>
                                DPS
                            </option>
                            <option value="ketua" @selected($filterRole === 'ketua')>
                                Ketua
                            </option>
                        </select>
                    </div>

                    <div class="col-12 col-md-6 col-lg-3">
                        <label for="tanggal" class="form-label">
                            Tanggal Aktivitas
                        </label>

                        <input type="date" name="tanggal" id="tanggal" class="form-control"
                            value="{{ $filterTanggal }}">
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-3">
                    <button type="submit" class="btn-theme-primary">
                        <i class="bi bi-search me-1"></i>
                        Terapkan Filter
                    </button>

                    <a href="{{ route('admin.aktivitas-log.index') }}" class="btn-theme-light">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Log --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span>
                <i class="bi bi-list-check me-2" style="color:var(--primary);"></i>
                Daftar Log Aktivitas
            </span>

            <span class="badge-soft tint-indigo">
                {{ number_format($aktivitasLogs->total()) }} data
            </span>
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th style="width:60px;">No.</th>
                        <th>Pengguna</th>
                        <th>Role</th>
                        <th>Aktivitas</th>
                        <th>Keterangan</th>
                        <th>IP Address</th>
                        <th>Waktu</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($aktivitasLogs as $log)
                        @php
                            $roleTint = match ($log->akun->role ?? '') {
                                'admin' => 'indigo',
                                'anggota' => 'emerald',
                                'pengurus' => 'amber',
                                'dps' => 'cyan',
                                'ketua' => 'pink',
                                default => 'slate',
                            };

                            [$aktivitasIcon, $aktivitasTint] = match ($log->aktivitas) {
                                'Login' => ['bi-box-arrow-in-right', 'emerald'],
                                'Logout' => ['bi-box-arrow-left', 'slate'],
                                'Perbarui Profil' => ['bi-person-check', 'indigo'],
                                'Ganti Password' => ['bi-shield-lock', 'amber'],
                                'Hapus Foto Profil' => ['bi-person-x', 'rose'],
                                default => ['bi-activity', 'cyan'],
                            };
                        @endphp

                        <tr>
                            <td>
                                <span style="color:var(--text-muted); font-size:.78rem;">
                                    {{ $aktivitasLogs->firstItem() + $loop->index }}
                                </span>
                            </td>

                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $log->akun?->foto_url ?? asset('images/default-avatar.png') }}"
                                        alt="Foto pengguna" class="avatar-sm">

                                    <div style="min-width:0;">
                                        <div style="font-size:.82rem; font-weight:700; color:var(--text-dark);">
                                            {{ $log->akun?->nama ?? 'Akun tidak tersedia' }}
                                        </div>

                                        <div style="font-size:.72rem; color:var(--text-muted);">
                                            {{ $log->akun?->email ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="badge-soft tint-{{ $roleTint }}">
                                    {{ $log->akun?->role_label ?? '-' }}
                                </span>
                            </td>

                            <td>
                                <span class="badge-soft tint-{{ $aktivitasTint }}">
                                    <i class="bi {{ $aktivitasIcon }} me-1"></i>
                                    {{ $log->aktivitas }}
                                </span>
                            </td>

                            <td style="min-width:240px;">
                                <div style="font-size:.78rem; color:var(--text-muted); line-height:1.55;">
                                    {{ $log->keterangan ?: '-' }}
                                </div>
                            </td>

                            <td>
                                <span class="code-tag">
                                    {{ $log->ip_address ?: '-' }}
                                </span>
                            </td>

                            <td style="white-space:nowrap;">
                                <div style="font-size:.8rem; font-weight:600; color:var(--text-dark);">
                                    {{ $log->created_at?->format('d/m/Y H:i') ?? '-' }}
                                </div>

                                <div style="font-size:.7rem; color:var(--text-muted);">
                                    {{ $log->created_at?->diffForHumans() ?? '-' }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="theme-empty-state">
                                    <div class="empty-icon">
                                        <i class="bi bi-clock-history"></i>
                                    </div>

                                    <h6>Log aktivitas tidak ditemukan</h6>

                                    <p>
                                        Belum ada aktivitas atau tidak ada data
                                        yang sesuai dengan filter.
                                    </p>

                                    <a href="{{ route('admin.aktivitas-log.index') }}" class="btn-theme-light mt-2">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                                        Reset Filter
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($aktivitasLogs->hasPages())
            <div class="card-footer">
                {{ $aktivitasLogs->links() }}
            </div>
        @endif
    </div>

@endsection
