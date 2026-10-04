@extends('layouts.app')

@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')
@section('page-subtitle', 'Seluruh pemberitahuan untuk akun Anda')

@section('sidebar-menu')
    @php
        $role = Auth::user()->role;
    @endphp
    @if ($role === 'admin')
        @include('admin.partials._sidebar')
    @elseif ($role === 'anggota')
        @include('anggota.partials._sidebar')
    @elseif ($role === 'pengurus')
        @include('pengurus.partials._sidebar')
    @elseif ($role === 'dps')
        @include('dps.partials._sidebar')
    @elseif ($role === 'ketua')
        @include('ketua.partials._sidebar')
    @endif
@endsection

@section('content')

{{-- ── Header & Aksi ────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div class="section-title mb-0">
        <i class="bi bi-bell-fill"></i> Notifikasi Anda
        @if ($jumlahBelumDibaca > 0)
            <span class="badge-soft tint-rose" style="font-size:.72rem; margin-left:.5rem;">
                {{ $jumlahBelumDibaca }} belum dibaca
            </span>
        @endif
    </div>
    @if ($jumlahBelumDibaca > 0)
        <form action="{{ route('notifikasi.read-all') }}" method="POST" id="formReadAll">
            @csrf
            <button type="submit" class="btn-theme-light" style="padding:.4rem .9rem; font-size:.82rem;">
                <i class="bi bi-check2-all me-1"></i> Tandai Semua Dibaca
            </button>
        </form>
    @endif
</div>

{{-- ── Filter & Search ─────────────────────────────────────── --}}
<div class="filter-toolbar mb-3">
    <form action="{{ route('notifikasi.index') }}" method="GET" id="formFilter">
        <div class="row g-2">
            <div class="col-12 col-md-6">
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Cari isi notifikasi...">
                </div>
            </div>
            <div class="col-8 col-md-4">
                <select name="filter" class="form-select" onchange="document.getElementById('formFilter').submit()">
                    <option value="semua"         {{ $filter === 'semua'         ? 'selected' : '' }}>Semua</option>
                    <option value="belum_dibaca"  {{ $filter === 'belum_dibaca'  ? 'selected' : '' }}>Belum Dibaca</option>
                    <option value="sudah_dibaca"  {{ $filter === 'sudah_dibaca'  ? 'selected' : '' }}>Sudah Dibaca</option>
                    <option value="hari_ini"      {{ $filter === 'hari_ini'      ? 'selected' : '' }}>Hari Ini</option>
                    <option value="minggu_ini"    {{ $filter === 'minggu_ini'    ? 'selected' : '' }}>Minggu Ini</option>
                </select>
            </div>
            <div class="col-4 col-md-2 d-flex gap-2">
                <button type="submit" class="btn-theme-primary flex-fill d-flex align-items-center justify-content-center">
                    <i class="bi bi-funnel"></i>
                </button>
                @if ($search || $filter !== 'semua')
                    <a href="{{ route('notifikasi.index') }}" class="btn-theme-light" title="Reset">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

{{-- ── Daftar Notifikasi ───────────────────────────────────── --}}
<div class="card">
    <div class="card-body p-0">
        @forelse ($notifikasiList as $n)
            <div class="d-flex align-items-start gap-3 px-4 py-3"
                 style="border-bottom:1px solid #f6f7fb; {{ ! $n->dibaca ? 'background:linear-gradient(90deg,rgba(99,102,241,.05),transparent);' : '' }}">

                <div class="notif-icon tint-{{ $n->tipe_tint }}" style="flex-shrink:0;">
                    <i class="bi {{ $n->tipe_icon }}"></i>
                </div>

                <div style="flex:1; min-width:0;">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div style="font-size:.86rem; font-weight:700; color:var(--text-dark);">
                            {{ $n->judul }}
                        </div>
                        @if (! $n->dibaca)
                            <span style="width:8px; height:8px; border-radius:50%; background:var(--primary); flex-shrink:0;"></span>
                        @endif
                    </div>
                    <div style="font-size:.82rem; color:var(--text-muted); margin-top:.25rem; line-height:1.55;">
                        {{ \Illuminate\Support\Str::limit($n->pesan, 160) }}
                    </div>
                    <div style="font-size:.72rem; color:var(--text-light); margin-top:.4rem;">
                        <i class="bi bi-clock me-1"></i>{{ $n->waktu_label }}
                    </div>
                </div>

                <div class="d-flex flex-column gap-2 flex-shrink-0" style="min-width:110px;">
                    <a href="{{ route('notifikasi.show', $n->id) }}"
                       class="btn-theme-light d-flex align-items-center justify-content-center gap-1"
                       style="padding:.35rem .7rem; font-size:.74rem;">
                        <i class="bi bi-eye"></i> Detail
                    </a>
                    @if (! $n->dibaca)
                        <form action="{{ route('notifikasi.read', $n->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="btn-theme-primary w-100 d-flex align-items-center justify-content-center gap-1"
                                    style="padding:.35rem .7rem; font-size:.74rem;">
                                <i class="bi bi-check2"></i> Sudah Dibaca
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="theme-empty-state" style="padding:3rem 1rem;">
                <div class="empty-icon"><i class="bi bi-bell-slash"></i></div>
                <h6>Tidak ada notifikasi</h6>
                <p>
                    @if ($search || $filter !== 'semua')
                        Tidak ditemukan notifikasi yang sesuai filter Anda.
                    @else
                        Anda belum memiliki notifikasi apa pun.
                    @endif
                </p>
                @if ($search || $filter !== 'semua')
                    <a href="{{ route('notifikasi.index') }}" class="btn-theme-light">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    @if ($notifikasiList->hasPages())
        <div class="card-footer d-flex flex-wrap align-items-center justify-content-between gap-2 px-3 py-3">
            <span style="font-size:.78rem; color:var(--text-muted); font-weight:500;">
                Menampilkan {{ $notifikasiList->firstItem() }}–{{ $notifikasiList->lastItem() }}
                dari {{ $notifikasiList->total() }} notifikasi
            </span>
            {{ $notifikasiList->links() }}
        </div>
    @endif
</div>

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
    const formReadAll = document.getElementById('formReadAll');
    if (formReadAll) {
        formReadAll.addEventListener('submit', () => {
            document.getElementById('loadingOverlay').classList.add('show');
        });
    }
</script>
@endpush
