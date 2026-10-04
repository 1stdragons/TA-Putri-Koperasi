@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')
@section('page-subtitle', 'Informasi akun dan data pribadi Anda')

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

{{-- ── Breadcrumb ──────────────────────────────────────────── --}}
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb" style="font-size:.82rem; margin-bottom:0; background:transparent; padding:0;">
        <li class="breadcrumb-item">
            <a href="{{ route(Auth::user()->role . '.dashboard') }}" style="color:var(--text-muted); text-decoration:none;">
                <i class="bi bi-house-door me-1"></i>Dashboard
            </a>
        </li>
        <li class="breadcrumb-item active" style="color:var(--primary); font-weight:600;" aria-current="page">
            Profil Saya
        </li>
    </ol>
</nav>

<div class="row g-3">

{{-- ═══════════════════════════════════════════════════════════
     KOLOM KIRI — Kartu Identitas
═══════════════════════════════════════════════════════════ --}}
<div class="col-12 col-lg-4">
    <div class="card mb-3" style="overflow:hidden;">
        <div style="background:linear-gradient(120deg, var(--primary) 0%, #818cf8 60%, var(--secondary) 130%);
                    padding:2rem 1.5rem; text-align:center; position:relative; overflow:hidden;">
            <div style="position:absolute; width:140px; height:140px; background:rgba(255,255,255,.1);
                        border-radius:50%; top:-60px; right:-40px;"></div>
            <div style="position:absolute; width:90px; height:90px; background:rgba(255,255,255,.08);
                        border-radius:50%; bottom:-30px; left:-20px;"></div>

            <img src="{{ $akun->foto_url }}" alt="Foto Profil"
                 style="width:96px; height:96px; border-radius:50%; object-fit:cover;
                        border:4px solid rgba(255,255,255,.5); box-shadow:0 8px 24px rgba(0,0,0,.15);
                        position:relative; z-index:1; margin-bottom:.9rem; background:#fff;">

            <h5 style="font-weight:800; color:#fff; margin-bottom:.25rem; position:relative; z-index:1;">
                {{ $akun->nama }}
            </h5>
            <div style="font-size:.8rem; color:rgba(255,255,255,.85); margin-bottom:.75rem; position:relative; z-index:1;">
                {{ $akun->email }}
            </div>

            <div class="d-flex justify-content-center gap-2 flex-wrap" style="position:relative; z-index:1;">
                <span class="badge-soft" style="background:rgba(255,255,255,.2) !important; color:#fff !important;
                             border:1px solid rgba(255,255,255,.35); font-size:.76rem;">
                    <i class="bi bi-person-badge-fill me-1"></i>{{ $akun->role_label }}
                </span>
                <span class="badge-soft tint-{{ $akun->status === 'aktif' ? 'emerald' : 'rose' }}"
                      style="font-size:.76rem;">
                    <i class="bi bi-{{ $akun->status === 'aktif' ? 'check-circle-fill' : 'x-circle-fill' }} me-1"></i>
                    {{ ucfirst($akun->status) }}
                </span>
            </div>
        </div>

        <div class="card-body d-flex flex-column gap-2">
            <a href="{{ route('profil.edit') }}" class="btn-theme-primary w-100 d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-pencil-square"></i> Edit Profil
            </a>
            <a href="{{ route('profil.password.edit') }}" class="btn-theme-light w-100 d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-shield-lock-fill"></i> Ganti Password
            </a>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     KOLOM KANAN — Detail Informasi
═══════════════════════════════════════════════════════════ --}}
<div class="col-12 col-lg-8">

    {{-- Informasi Akun --}}
    <div class="card mb-3">
        <div class="card-header">
            <i class="bi bi-person-vcard-fill me-2" style="color:var(--primary);"></i>Informasi Akun
        </div>
        <div class="card-body p-0">

            <div class="akun-info-row px-4">
                <div class="akun-info-icon tint-indigo"><i class="bi bi-person-fill"></i></div>
                <div>
                    <div class="akun-info-label">Nama Lengkap</div>
                    <div class="akun-info-value">{{ $akun->nama }}</div>
                </div>
            </div>

            <div class="akun-info-row px-4">
                <div class="akun-info-icon tint-cyan"><i class="bi bi-envelope-fill"></i></div>
                <div>
                    <div class="akun-info-label">Email</div>
                    <div class="akun-info-value">{{ $akun->email }}</div>
                </div>
            </div>

            <div class="akun-info-row px-4">
                <div class="akun-info-icon tint-violet"><i class="bi bi-person-badge-fill"></i></div>
                <div>
                    <div class="akun-info-label">Role</div>
                    <div class="akun-info-value">
                        <span class="badge-soft tint-violet">{{ $akun->role_label }}</span>
                    </div>
                </div>
            </div>

            <div class="akun-info-row px-4">
                <div class="akun-info-icon tint-{{ $akun->status === 'aktif' ? 'emerald' : 'rose' }}">
                    <i class="bi bi-toggle-on"></i>
                </div>
                <div>
                    <div class="akun-info-label">Status Akun</div>
                    <div class="akun-info-value">
                        <span class="badge-soft tint-{{ $akun->status === 'aktif' ? 'emerald' : 'rose' }}">
                            {{ ucfirst($akun->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="akun-info-row px-4" style="border-bottom:none;">
                <div class="akun-info-icon tint-amber"><i class="bi bi-clock-history"></i></div>
                <div>
                    <div class="akun-info-label">Login Terakhir</div>
                    <div class="akun-info-value">
                        @if ($akun->last_login)
                            {{ $akun->last_login->translatedFormat('d F Y, H:i') }} WIB
                            <span style="font-size:.76rem; color:var(--text-muted); font-weight:500;">
                                ({{ $akun->last_login->diffForHumans() }})
                            </span>
                        @else
                            <span style="color:var(--text-light);">Belum pernah tercatat</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Informasi Anggota (khusus role anggota) --}}
    @if ($akun->role === 'anggota')
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span>
                    <i class="bi bi-person-lines-fill me-2" style="color:var(--success);"></i>Data Anggota
                </span>
                <span class="badge-soft tint-slate" style="font-size:.7rem;">
                    <i class="bi bi-lock-fill me-1"></i>Read Only
                </span>
            </div>

            @if ($anggota)
                <div class="card-body p-0">
                    <div class="akun-info-row px-4">
                        <div class="akun-info-icon tint-indigo"><i class="bi bi-hash"></i></div>
                        <div>
                            <div class="akun-info-label">Nomor Anggota</div>
                            <div class="akun-info-value"><span class="code-tag">{{ $anggota->nomor_anggota }}</span></div>
                        </div>
                    </div>

                    <div class="akun-info-row px-4">
                        <div class="akun-info-icon tint-slate"><i class="bi bi-credit-card-fill"></i></div>
                        <div>
                            <div class="akun-info-label">NIK</div>
                            <div class="akun-info-value"><span class="code-tag">{{ $anggota->nik }}</span></div>
                        </div>
                    </div>

                    <div class="akun-info-row px-4">
                        <div class="akun-info-icon tint-emerald"><i class="bi bi-telephone-fill"></i></div>
                        <div>
                            <div class="akun-info-label">Telepon</div>
                            <div class="akun-info-value">{{ $anggota->telepon ?: '—' }}</div>
                        </div>
                    </div>

                    <div class="akun-info-row px-4">
                        <div class="akun-info-icon tint-cyan"><i class="bi bi-gender-ambiguous"></i></div>
                        <div>
                            <div class="akun-info-label">Jenis Kelamin</div>
                            <div class="akun-info-value">
                                {{ $anggota->jenis_kelamin === 'L' ? 'Laki-laki' : ($anggota->jenis_kelamin === 'P' ? 'Perempuan' : '—') }}
                            </div>
                        </div>
                    </div>

                    <div class="akun-info-row px-4">
                        <div class="akun-info-icon tint-amber"><i class="bi bi-geo-alt-fill"></i></div>
                        <div>
                            <div class="akun-info-label">Alamat</div>
                            <div class="akun-info-value" style="line-height:1.55;">
                                {{ $anggota->alamat ?: '—' }}
                                @if ($anggota->kelurahan || $anggota->kecamatan || $anggota->kabupaten || $anggota->provinsi)
                                    <div style="font-size:.78rem; color:var(--text-muted); margin-top:.2rem;">
                                        {{ collect([
                                            $anggota->rt ? 'RT ' . $anggota->rt : null,
                                            $anggota->rw ? 'RW ' . $anggota->rw : null,
                                            $anggota->kelurahan,
                                            $anggota->kecamatan,
                                            $anggota->kabupaten,
                                            $anggota->provinsi,
                                        ])->filter()->implode(', ') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($anggota->pendidikan_terakhir)
                        <div class="akun-info-row px-4">
                            <div class="akun-info-icon tint-violet"><i class="bi bi-mortarboard-fill"></i></div>
                            <div>
                                <div class="akun-info-label">Pendidikan Terakhir</div>
                                <div class="akun-info-value">{{ $anggota->pendidikan_terakhir }}</div>
                            </div>
                        </div>
                    @endif

                    @if ($anggota->pekerjaan_sampingan)
                        <div class="akun-info-row px-4">
                            <div class="akun-info-icon tint-cyan"><i class="bi bi-briefcase-fill"></i></div>
                            <div>
                                <div class="akun-info-label">Pekerjaan Sampingan</div>
                                <div class="akun-info-value">{{ $anggota->pekerjaan_sampingan }}</div>
                            </div>
                        </div>
                    @endif

                    <div class="akun-info-row px-4">
                        <div class="akun-info-icon tint-amber"><i class="bi bi-calendar-event-fill"></i></div>
                        <div>
                            <div class="akun-info-label">Tanggal Bergabung</div>
                            <div class="akun-info-value">
                                {{ $anggota->tanggal_bergabung ? \Carbon\Carbon::parse($anggota->tanggal_bergabung)->translatedFormat('d F Y') : '—' }}
                            </div>
                        </div>
                    </div>

                    @php
                        $sTint = match($anggota->status_anggota) { 'aktif'=>'emerald','peringatan'=>'amber', default=>'rose' };
                        $sLabel = match($anggota->status_anggota) { 'aktif'=>'Aktif','peringatan'=>'Peringatan', default=>'Tidak Aktif' };
                    @endphp
                    <div class="akun-info-row px-4" style="border-bottom:none;">
                        <div class="akun-info-icon tint-{{ $sTint }}"><i class="bi bi-toggle-on"></i></div>
                        <div>
                            <div class="akun-info-label">Status Keanggotaan</div>
                            <div class="akun-info-value">
                                <span class="badge-soft tint-{{ $sTint }}">{{ $sLabel }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="card-body">
                    <div class="theme-empty-state" style="padding:1.8rem 1rem;">
                        <div class="empty-icon" style="width:52px; height:52px; font-size:1.4rem;">
                            <i class="bi bi-person-x"></i>
                        </div>
                        <h6 style="font-size:.86rem;">Data anggota belum tersedia</h6>
                        <p style="font-size:.78rem;">
                            Akun Anda belum memiliki data anggota terdaftar. Silakan hubungi Admin.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    @endif

</div>{{-- End kolom kanan --}}
</div>{{-- End row --}}

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if (session('success'))
        Swal.fire({
            icon: 'success', title: 'Berhasil!', text: '{{ session('success') }}',
            timer: 4000, timerProgressBar: true, buttonsStyling: false,
            customClass: { popup:'swal-theme-popup', title:'swal-theme-title',
                           htmlContainer:'swal-theme-html', confirmButton:'swal-theme-confirm' }
        });
    @endif

    @if (session('error'))
        Swal.fire({
            icon: 'error', title: 'Gagal!', text: '{{ session('error') }}',
            buttonsStyling: false,
            customClass: { popup:'swal-theme-popup', title:'swal-theme-title',
                           htmlContainer:'swal-theme-html', confirmButton:'swal-theme-deny' }
        });
    @endif
</script>
@endpush