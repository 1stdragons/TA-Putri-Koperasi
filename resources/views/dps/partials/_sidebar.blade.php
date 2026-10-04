{{-- Sidebar DPS — dipakai semua halaman DPS via @include --}}

<div class="nav-section-title">Menu Utama</div>

<a href="{{ route('dps.dashboard') }}" class="nav-link {{ request()->routeIs('dps.dashboard') ? 'active' : '' }}">
    <i class="bi bi-grid-1x2-fill"></i>
    Dashboard
</a>

<div class="nav-section-title">Validasi Syariah</div>

<a href="{{ route('dps.validasi-syariah.index') }}"
    class="nav-link {{ request()->routeIs('dps.validasi-syariah.*') ? 'active' : '' }}">
    <i class="bi bi-shield-check"></i>
    Validasi Akad

    @php
        $jumlahPendingDps = \App\Models\Usaha::where('status_validasi_syariah', 'menunggu')
            ->whereHas('laporan', fn($query) => $query->where('status_laporan', 'divalidasi'))
            ->count();
    @endphp

    @if ($jumlahPendingDps > 0)
        <span
            style="margin-left:auto;
                   background:linear-gradient(135deg,#f43f5e,#ec4899);
                   color:#fff;
                   font-size:.62rem;
                   font-weight:700;
                   padding:.18rem .5rem;
                   border-radius:20px;
                   line-height:1.4;">
            {{ $jumlahPendingDps > 9 ? '9+' : $jumlahPendingDps }}
        </span>
    @endif
</a>

<div class="nav-section-title">Informasi</div>

<li class="nav-item">
    <a href="{{ route('dps.data-usaha.index') }}"
        class="nav-link {{ request()->routeIs('dps.data-usaha.*') ? 'active' : '' }}">
        <i class="bi bi-shop-window"></i>

        <span>Data Usaha</span>
    </a>
</li>

{{-- Route riwayat validasi DPS belum dibuat --}}
<a href="{{ route('dps.rekap-syariah.index') }}"
    class="nav-link {{ request()->routeIs('dps.rekap-syariah.*') ? 'active' : '' }}">
    <i class="bi bi-file-earmark-pdf-fill"></i> Rekap Validasi Syariah
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
