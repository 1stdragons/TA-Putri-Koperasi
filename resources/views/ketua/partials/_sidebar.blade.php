{{-- Sidebar Ketua — dipakai semua halaman Ketua via @include --}}

<div class="nav-section-title">Menu Utama</div>

<a href="{{ route('ketua.dashboard') }}" class="nav-link {{ request()->routeIs('ketua.dashboard') ? 'active' : '' }}">
    <i class="bi bi-grid-1x2-fill"></i>
    Dashboard
</a>

<div class="nav-section-title">Monitoring</div>

<a href="{{ route('ketua.monitoring.index') }}"
    class="nav-link {{ request()->routeIs('ketua.monitoring.*') ? 'active' : '' }}">
    <i class="bi bi-binoculars-fill"></i>
    Monitoring Anggota

    @php
        $totalEvaluasi = \App\Models\Validasi::where('status_validasi_pengurus', 'disetujui')
            ->whereNotNull('hasil_evaluasi')
            ->count();
    @endphp

    @if ($totalEvaluasi > 0)
        <span
            style="margin-left:auto;
                   background:linear-gradient(135deg,#6366f1,#8b5cf6);
                   color:#fff;
                   font-size:.62rem;
                   font-weight:700;
                   padding:.18rem .5rem;
                   border-radius:20px;
                   line-height:1.4;">
            {{ $totalEvaluasi > 99 ? '99+' : number_format($totalEvaluasi) }}
        </span>
    @endif
</a>

{{-- Route khusus hasil evaluasi Ketua belum dibuat --}}
<a href="#" class="nav-link" onclick="return false;" aria-disabled="true"
    title="Fitur Hasil Evaluasi belum tersedia">
    <i class="bi bi-clipboard2-data-fill"></i>
    Hasil Evaluasi
</a>

{{-- Akan diaktifkan pada Modul Rekap PDF --}}
<a href="{{ route('ketua.rekap-monitoring.index') }}"
    class="nav-link {{ request()->routeIs('ketua.rekap-monitoring.*') ? 'active' : '' }}">
    <i class="bi bi-file-earmark-pdf-fill"></i> Rekap Laporan
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
