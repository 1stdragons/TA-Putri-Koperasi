@extends('layouts.app')

@section('title', 'Detail Notifikasi')
@section('page-title', 'Detail Notifikasi')
@section('page-subtitle', $notifikasi->judul)

@section('sidebar-menu')
    @php $role = Auth::user()->role; @endphp
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

    <div class="mb-3">
        <a href="{{ route('notifikasi.index') }}" class="btn-theme-light" style="padding:.4rem .9rem; font-size:.83rem;">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Notifikasi
        </a>
    </div>

    <div class="card" style="max-width:700px;">
        <div class="card-body">
            <div class="d-flex align-items-start gap-3 mb-3">
                <div class="notif-icon tint-{{ $notifikasi->tipe_tint }}"
                    style="width:48px; height:48px; font-size:1.2rem; flex-shrink:0;">
                    <i class="bi {{ $notifikasi->tipe_icon }}"></i>
                </div>
                <div>
                    <div style="font-size:1.05rem; font-weight:800; color:var(--text-dark);">
                        {{ $notifikasi->judul }}
                    </div>
                    <div style="font-size:.78rem; color:var(--text-muted); margin-top:.2rem;">
                        <i class="bi bi-clock me-1"></i>
                        {{ $notifikasi->created_at->translatedFormat('d F Y, H:i') }} WIB
                        &bull; {{ $notifikasi->waktu_label }}
                    </div>
                </div>
            </div>

            <div
                style="background:var(--bg-soft); border-radius:var(--radius-md); padding:1rem 1.2rem;
                    font-size:.9rem; line-height:1.75; color:var(--text-dark);
                    border-left:3px solid var(--{{ $notifikasi->tipe_tint === 'rose' ? 'danger' : ($notifikasi->tipe_tint === 'amber' ? 'warning' : ($notifikasi->tipe_tint === 'emerald' ? 'success' : 'primary')) }});">
                {{ $notifikasi->pesan }}
            </div>

            <div class="mt-3">
                <span class="badge-soft tint-{{ $notifikasi->dibaca ? 'emerald' : 'amber' }}">
                    <i class="bi bi-{{ $notifikasi->dibaca ? 'check2-circle' : 'circle' }} me-1"></i>
                    {{ $notifikasi->dibaca ? 'Sudah Dibaca' : 'Belum Dibaca' }}
                </span>
            </div>
        </div>
    </div>

@endsection
