{{-- Admin Sidebar --}}

<div class="nav-section-title">Menu Utama</div>

<a
    href="{{ route('admin.dashboard') }}"
    class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
>
    <i class="bi bi-grid-1x2-fill"></i>
    Dashboard
</a>

<div class="nav-section-title">Manajemen</div>

<a
    href="{{ route('admin.anggota.index') }}"
    class="nav-link {{ request()->routeIs('admin.anggota.*') ? 'active' : '' }}"
>
    <i class="bi bi-people-fill"></i>
    Data Anggota
</a>

<a
    href="{{ route('admin.akun.index') }}"
    class="nav-link {{ request()->routeIs('admin.akun.*') ? 'active' : '' }}"
>
    <i class="bi bi-person-gear"></i>
    Manajemen Akun
</a>

<a
    href="{{ route('admin.usaha.index') }}"
    class="nav-link {{ request()->routeIs('admin.usaha.*') ? 'active' : '' }}"
>
    <i class="bi bi-shop"></i>
    Data Usaha
</a>

<div class="nav-section-title">Laporan & Evaluasi</div>

<a
    href="{{ route('admin.laporan.index') }}"
    class="nav-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}"
>
    <i class="bi bi-journal-text"></i>
    Laporan Usaha
</a>

<a
    href="{{ route('admin.evaluasi.index') }}"
    class="nav-link {{ request()->routeIs('admin.evaluasi.*') ? 'active' : '' }}"
>
    <i class="bi bi-clipboard2-data-fill"></i>
    Riwayat Evaluasi
</a>

<a
    href="{{ route('admin.rekap-monitoring.index') }}"
    class="nav-link {{ request()->routeIs('admin.rekap-monitoring.*') ? 'active' : '' }}"
>
    <i class="bi bi-file-earmark-pdf-fill"></i>
    Rekap Monitoring
</a>

<div class="nav-section-title">Sistem</div>

<a
    href="{{ route('notifikasi.index') }}"
    class="nav-link {{ request()->routeIs('notifikasi.*') ? 'active' : '' }}"
>
    <i class="bi bi-bell-fill"></i>
    Notifikasi

    @php
        $jmlNotif = \App\Models\Notifikasi::where(
            'akun_id',
            auth()->id()
        )
            ->belumDibaca()
            ->count();
    @endphp

    @if ($jmlNotif > 0)
        <span
            style="
                margin-left:auto;
                background:linear-gradient(135deg,#f43f5e,#ec4899);
                color:#fff;
                font-size:.62rem;
                font-weight:700;
                padding:.18rem .5rem;
                border-radius:20px;
                line-height:1.4;
            "
        >
            {{ $jmlNotif > 9 ? '9+' : $jmlNotif }}
        </span>
    @endif
</a>

<a
    href="{{ route('admin.aktivitas-log.index') }}"
    class="nav-link {{ request()->routeIs('admin.aktivitas-log.*') ? 'active' : '' }}"
>
    <i class="bi bi-clock-history"></i>
    Log Aktivitas
</a>