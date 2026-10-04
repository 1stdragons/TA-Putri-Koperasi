{{-- Sidebar Anggota --}}

<div class="nav-section-title">Menu Utama</div>

<a href="{{ route('anggota.dashboard') }}" class="nav-link {{ request()->routeIs('anggota.dashboard') ? 'active' : '' }}">
    <i class="bi bi-grid-1x2-fill"></i>
    Dashboard
</a>

<div class="nav-section-title">Usaha Saya</div>

<a href="{{ route('anggota.usaha-saya.index') }}"
    class="nav-link {{ request()->routeIs('anggota.usaha-saya.*') ? 'active' : '' }}">
    <i class="bi bi-shop-window"></i>
    Usaha Saya
</a>

<div class="nav-section-title">Monitoring</div>

<a href="{{ route('anggota.laporan.create') }}"
    class="nav-link {{ request()->routeIs('anggota.laporan.create') ? 'active' : '' }}">
    <i class="bi bi-plus-circle-fill"></i>
    Input Monitoring
</a>

<a href="{{ route('anggota.laporan.index') }}"
    class="nav-link {{ request()->routeIs('anggota.laporan.index') ||
    request()->routeIs('anggota.laporan.show') ||
    request()->routeIs('anggota.laporan.edit')
        ? 'active'
        : '' }}">
    <i class="bi bi-journal-text"></i>
    Riwayat Laporan

    @php
        $laporanDitolak = 0;
        $anggotaData = auth()->user()?->anggota;

        if ($anggotaData) {
            $usahaIds = \App\Models\Usaha::where('anggota_id', $anggotaData->id)->pluck('id');

            $laporanDitolak = \App\Models\Laporan::whereIn('usaha_id', $usahaIds)
                ->where('status_laporan', 'ditolak')
                ->count();
        }
    @endphp

    @if ($laporanDitolak > 0)
        <span
            style="margin-left:auto;
                   background:linear-gradient(135deg,#f43f5e,#ec4899);
                   color:#fff;
                   font-size:.62rem;
                   font-weight:700;
                   padding:.18rem .5rem;
                   border-radius:20px;
                   line-height:1.4;">
            {{ $laporanDitolak > 9 ? '9+' : $laporanDitolak }}
        </span>
    @endif
</a>

<a href="{{ route('anggota.evaluasi.index') }}"
    class="nav-link {{ request()->routeIs('anggota.evaluasi.*') ? 'active' : '' }}">
    <i class="bi bi-clipboard2-data-fill"></i>
    Riwayat Evaluasi
</a>

<a href="{{ route('anggota.rekap-laporan.index') }}"
    class="nav-link {{ request()->routeIs('anggota.rekap-laporan.*') ? 'active' : '' }}">
    <i class="bi bi-file-earmark-pdf-fill"></i> Rekap Laporan Saya
</a>

<div class="nav-section-title">Lainnya</div>

<a href="{{ route('notifikasi.index') }}" class="nav-link {{ request()->routeIs('notifikasi.*') ? 'active' : '' }}">
    <i class="bi bi-bell-fill"></i>
    Notifikasi

    @php
        $jmlNotif = \App\Models\Notifikasi::where('akun_id', auth()->id())
            ->belumDibaca()
            ->count();
    @endphp

    @if ($jmlNotif > 0)
        <span
            style="margin-left:auto;
                   background:linear-gradient(135deg,#f43f5e,#ec4899);
                   color:#fff;
                   font-size:.62rem;
                   font-weight:700;
                   padding:.18rem .5rem;
                   border-radius:20px;
                   line-height:1.4;">
            {{ $jmlNotif > 9 ? '9+' : $jmlNotif }}
        </span>
    @endif
</a>
