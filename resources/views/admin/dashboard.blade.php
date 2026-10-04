@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Administrator')
@section('page-subtitle', 'Ringkasan sistem koperasi syariah')

@section('sidebar-menu')
    @include('admin.partials._sidebar')
@endsection

@section('content')

    {{-- ── Welcome Banner ──────────────────────────────────────── --}}
    <div class="welcome-banner d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h5 class="mb-1">
                <i class="bi bi-stars me-1"></i> Selamat Datang, {{ Auth::user()->nama }} 👋
            </h5>
            <p class="mb-0">
                {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y — H:i') }} WIB
            </p>
        </div>
        <span class="role-pill">
            <i class="bi bi-shield-check me-1"></i> Administrator
        </span>
    </div>

    {{-- ── Statistik Utama ─────────────────────────────────────── --}}
    <div class="section-title"><i class="bi bi-bar-chart-line-fill"></i> Statistik Utama</div>
    <div class="row g-3 mb-4">

        <div class="col-6 col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Total Akun</div>
                            <div class="stat-value text-indigo">{{ number_format($totalAkun) }}</div>
                            <div class="stat-sub">Semua role pengguna</div>
                        </div>
                        <div class="stat-icon tint-indigo">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Total Anggota</div>
                            <div class="stat-value text-cyan">{{ number_format($totalAnggota) }}</div>
                            <div class="stat-sub">Terdaftar di koperasi</div>
                        </div>
                        <div class="stat-icon tint-cyan">
                            <i class="bi bi-person-badge-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Usaha Aktif</div>
                            <div class="stat-value text-emerald">{{ number_format($totalUsahaAktif) }}</div>
                            <div class="stat-sub">Unit usaha berjalan</div>
                        </div>
                        <div class="stat-icon tint-emerald">
                            <i class="bi bi-shop-window"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Total Laporan</div>
                            <div class="stat-value text-pink">{{ number_format($totalLaporan) }}</div>
                            <div class="stat-sub">Semua periode</div>
                        </div>
                        <div class="stat-icon tint-pink">
                            <i class="bi bi-journal-richtext"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Status Laporan ──────────────────────────────────────── --}}
    <div class="section-title"><i class="bi bi-journal-check"></i> Status Laporan</div>
    <div class="row g-3 mb-4">

        <div class="col-6 col-md-3">
            <div class="card mini-stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="mini-stat-icon tint-slate">
                            <i class="bi bi-file-earmark"></i>
                        </div>
                        <div>
                            <div class="stat-value text-slate">{{ number_format($totalLaporanDraft) }}</div>
                            <div class="stat-label text-slate">Draft</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card mini-stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="mini-stat-icon tint-blue">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div>
                            <div class="stat-value text-blue">{{ number_format($totalLaporanPending) }}</div>
                            <div class="stat-label text-blue">Menunggu</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card mini-stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="mini-stat-icon tint-emerald">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <div>
                            <div class="stat-value text-emerald">{{ number_format($totalLaporanDivalidasi) }}</div>
                            <div class="stat-label text-emerald">Divalidasi</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card mini-stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="mini-stat-icon tint-rose">
                            <i class="bi bi-x-circle-fill"></i>
                        </div>
                        <div>
                            <div class="stat-value text-rose">{{ number_format($totalLaporanDitolak) }}</div>
                            <div class="stat-label text-rose">Ditolak</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Status Keanggotaan ──────────────────────────────────── --}}
    <div class="section-title"><i class="bi bi-person-lines-fill"></i> Status Keanggotaan</div>
    <div class="row g-3 mb-4">

        <div class="col-12 col-md-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Anggota Aktif</div>
                            <div class="stat-value text-emerald">{{ number_format($totalAnggotaAktif) }}</div>
                            <div class="stat-sub">
                                {{ $totalAnggota > 0 ? round(($totalAnggotaAktif / $totalAnggota) * 100, 1) : 0 }}%
                                dari total
                            </div>
                        </div>
                        <div class="stat-icon tint-emerald">
                            <i class="bi bi-person-check-fill"></i>
                        </div>
                    </div>
                    <div class="progress-soft">
                        <div class="progress-bar"
                            style="background:linear-gradient(90deg,#10b981,#34d399);
                                width:{{ $totalAnggota > 0 ? ($totalAnggotaAktif / $totalAnggota) * 100 : 0 }}%">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Peringatan</div>
                            <div class="stat-value text-amber">{{ number_format($totalAnggotaPeringatan) }}</div>
                            <div class="stat-sub">
                                {{ $totalAnggota > 0 ? round(($totalAnggotaPeringatan / $totalAnggota) * 100, 1) : 0 }}%
                                dari total
                            </div>
                        </div>
                        <div class="stat-icon tint-amber">
                            <i class="bi bi-person-exclamation"></i>
                        </div>
                    </div>
                    <div class="progress-soft">
                        <div class="progress-bar"
                            style="background:linear-gradient(90deg,#f59e0b,#fbbf24);
                                width:{{ $totalAnggota > 0 ? ($totalAnggotaPeringatan / $totalAnggota) * 100 : 0 }}%">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Tidak Aktif</div>
                            <div class="stat-value text-rose">{{ number_format($totalAnggotaTidakAktif) }}</div>
                            <div class="stat-sub">
                                {{ $totalAnggota > 0 ? round(($totalAnggotaTidakAktif / $totalAnggota) * 100, 1) : 0 }}%
                                dari total
                            </div>
                        </div>
                        <div class="stat-icon tint-rose">
                            <i class="bi bi-person-dash-fill"></i>
                        </div>
                    </div>
                    <div class="progress-soft">
                        <div class="progress-bar"
                            style="background:linear-gradient(90deg,#f43f5e,#fb7185);
                                width:{{ $totalAnggota > 0 ? ($totalAnggotaTidakAktif / $totalAnggota) * 100 : 0 }}%">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Grafik ───────────────────────────────────────────────── --}}
    <div class="section-title"><i class="bi bi-graph-up-arrow"></i> Grafik & Analisis</div>
    <div class="row g-3 mb-4">

        {{-- Doughnut --}}
        <div class="col-12 col-lg-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span>
                        <i class="bi bi-pie-chart-fill me-2" style="color:var(--primary);"></i>Status Anggota
                    </span>
                    <span class="badge-soft tint-indigo">Total: {{ $totalAnggota }}</span>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center" style="min-height:240px;">
                    @if ($totalAnggota > 0)
                        <div style="width:100%; max-width:230px;">
                            <canvas id="chartStatusAnggota"></canvas>
                        </div>
                    @else
                        <div class="theme-empty-state">
                            <div class="empty-icon"><i class="bi bi-pie-chart"></i></div>
                            <h6>Belum ada data</h6>
                            <p>Data anggota akan tampil sebagai grafik di sini.</p>
                        </div>
                    @endif
                </div>
                @if ($totalAnggota > 0)
                    <div class="card-footer pb-3 pt-2">
                        <div class="d-flex justify-content-center gap-3 flex-wrap"
                            style="font-size:.74rem; font-weight:600; color:var(--text-muted);">
                            <span class="d-flex align-items-center gap-1">
                                <span
                                    style="width:8px;height:8px;border-radius:50%;background:#10b981;display:inline-block;"></span>
                                Aktif ({{ $totalAnggotaAktif }})
                            </span>
                            <span class="d-flex align-items-center gap-1">
                                <span
                                    style="width:8px;height:8px;border-radius:50%;background:#f59e0b;display:inline-block;"></span>
                                Peringatan ({{ $totalAnggotaPeringatan }})
                            </span>
                            <span class="d-flex align-items-center gap-1">
                                <span
                                    style="width:8px;height:8px;border-radius:50%;background:#f43f5e;display:inline-block;"></span>
                                Tidak Aktif ({{ $totalAnggotaTidakAktif }})
                            </span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Bar --}}
        <div class="col-12 col-lg-8">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span>
                        <i class="bi bi-bar-chart-fill me-2" style="color:#be185d;"></i>Laporan per Periode
                    </span>
                    <span class="badge-soft tint-slate">12 periode terakhir</span>
                </div>
                <div class="card-body" style="min-height:240px;">
                    @if (count($grafikLaporanPeriode['labels']) > 0)
                        <canvas id="chartLaporanPeriode" style="max-height:230px;"></canvas>
                    @else
                        <div class="theme-empty-state">
                            <div class="empty-icon"><i class="bi bi-bar-chart"></i></div>
                            <h6>Belum ada laporan</h6>
                            <p>Grafik laporan per periode akan tampil di sini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── Tabel Laporan + Panel Notifikasi ───────────────────── --}}
    <div class="row g-3 mb-4">

        <div class="col-12 col-xl-8">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span>
                        <i class="bi bi-journal-text me-2" style="color:var(--primary);"></i>Laporan Terbaru
                    </span>
                    <a href="{{ route('admin.aktivitas-log.index') }}" class="btn-theme-light"
                        style="padding:.35rem .85rem; font-size:.74rem;">
                        Lihat Semua
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Anggota</th>
                                <th>Usaha</th>
                                <th>Periode</th>
                                <th>Omzet</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($laporanTerbaru as $i => $laporan)
                                <tr>
                                    <td style="color:var(--text-light); font-size:.78rem;">{{ $i + 1 }}</td>
                                    <td>
                                        <div style="font-size:.82rem; font-weight:700;">
                                            {{ $laporan->usaha->anggota->akun->nama ?? '-' }}
                                        </div>
                                        <div style="font-size:.72rem; color:var(--text-muted);">
                                            {{ $laporan->usaha->anggota->nomor_anggota ?? '-' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size:.82rem; font-weight:600;">
                                            {{ $laporan->usaha->nama_usaha ?? '-' }}
                                        </div>
                                        <div style="font-size:.72rem; color:var(--text-muted);">
                                            {{ $laporan->usaha->jenis_usaha_label ?? '-' }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge-soft tint-slate">Ke-{{ $laporan->periode_ke }}</span>
                                        <div style="font-size:.7rem; color:var(--text-muted); margin-top:3px;">
                                            {{ $laporan->tanggal_mulai->format('d/m/Y') }}
                                            – {{ $laporan->tanggal_selesai->format('d/m/Y') }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size:.82rem; font-weight:700; color:var(--text-dark);">
                                            Rp {{ number_format($laporan->omzet, 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $tintLaporan = match ($laporan->status_laporan) {
                                                'divalidasi' => 'emerald',
                                                'dikirim' => 'blue',
                                                'ditolak' => 'rose',
                                                default => 'slate',
                                            };
                                        @endphp
                                        <span class="badge-soft tint-{{ $tintLaporan }}">
                                            {{ $laporan->status_laporan_label }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="theme-empty-state">
                                            <div class="empty-icon"><i class="bi bi-inbox"></i></div>
                                            <h6>Belum ada laporan masuk</h6>
                                            <p>Laporan usaha 14 hari dari anggota akan muncul di sini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Panel Notifikasi --}}
        <div class="col-12 col-xl-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span>
                        <i class="bi bi-bell-fill me-2" style="color:var(--warning);"></i>Notifikasi
                    </span>
                    @if ($jumlahNotifikasiBelumDibaca > 0)
                        <span class="badge-soft tint-rose">
                            {{ $jumlahNotifikasiBelumDibaca }} baru
                        </span>
                    @endif
                </div>
                <div class="card-body p-0 scroll-thin" style="max-height:420px; overflow-y:auto;">
                    @forelse ($notifikasiTerbaru as $notif)
                        <div class="notif-item {{ $notif->dibaca ? '' : 'unread' }}">
                            <div
                                class="notif-icon tint-{{ match ($notif->tipe) {
                                    'sukses' => 'emerald',
                                    'peringatan' => 'amber',
                                    'bahaya' => 'rose',
                                    default => 'blue',
                                } }}">
                                <i class="bi {{ $notif->tipe_icon }}"></i>
                            </div>
                            <div style="flex:1; min-width:0;">
                                <div style="font-size:.8rem; font-weight:700; color:var(--text-dark);">
                                    {{ $notif->judul }}
                                    @if (!$notif->dibaca)
                                        <span class="badge-soft tint-indigo ms-1"
                                            style="font-size:.58rem; padding:.1rem .4rem;">Baru</span>
                                    @endif
                                </div>
                                <div style="font-size:.74rem; color:var(--text-muted); line-height:1.4;">
                                    {{ \Illuminate\Support\Str::limit($notif->pesan, 65) }}
                                </div>
                                <div style="font-size:.68rem; color:var(--text-light); margin-top:3px;">
                                    <i class="bi bi-clock me-1"></i>{{ $notif->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="theme-empty-state">
                            <div class="empty-icon"><i class="bi bi-bell-slash"></i></div>
                            <h6>Tidak ada notifikasi</h6>
                            <p>Notifikasi baru akan muncul di sini.</p>
                        </div>
                    @endforelse
                </div>
                @if ($notifikasiTerbaru->isNotEmpty())
                    <div class="card-footer text-center">
                        <a href="#"
                            style="font-size:.78rem; color:var(--primary); font-weight:700; text-decoration:none;">
                            Lihat semua notifikasi
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Log Aktivitas Terbaru ───────────────────────────────── --}}
    <div class="section-title"><i class="bi bi-clock-history"></i> Aktivitas Login Terbaru</div>
    <div class="card mb-2">
        <div class="card-header d-flex align-items-center justify-content-between">
            <span>
                <i class="bi bi-activity me-2" style="color:var(--success);"></i>Log Aktivitas
            </span>
            <a href="#" class="btn-theme-light" style="padding:.35rem .85rem; font-size:.74rem;">
                Lihat Semua
            </a>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Pengguna</th>
                        <th>Role</th>
                        <th>Aktivitas</th>
                        <th>IP Address</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($aktivitasTerbaru as $i => $log)
                        <tr>
                            <td style="color:var(--text-light); font-size:.78rem;">{{ $i + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $log->akun->foto_url ?? asset('images/default-avatar.png') }}"
                                        alt="" class="avatar-sm">
                                    <div>
                                        <div style="font-size:.82rem; font-weight:700;">
                                            {{ $log->akun->nama ?? '-' }}
                                        </div>
                                        <div style="font-size:.72rem; color:var(--text-muted);">
                                            {{ $log->akun->email ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $roleTint = match ($log->akun->role ?? '') {
                                        'admin' => 'indigo',
                                        'anggota' => 'emerald',
                                        'pengurus' => 'amber',
                                        'dps' => 'cyan',
                                        'ketua' => 'pink',
                                        default => 'slate',
                                    };
                                @endphp
                                <span class="badge-soft tint-{{ $roleTint }}">
                                    {{ $log->akun->role_label ?? '-' }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $isLogin = $log->aktivitas === 'Login';
                                    $aktColor = $isLogin ? '#10b981' : '#94a3b8';
                                    $aktIcon = $isLogin ? 'bi-box-arrow-in-right' : 'bi-box-arrow-left';
                                @endphp
                                <span style="font-size:.82rem; font-weight:600; color:{{ $aktColor }};">
                                    <i class="bi {{ $aktIcon }} me-1"></i>{{ $log->aktivitas }}
                                </span>
                            </td>
                            <td><span class="code-tag">{{ $log->ip_address ?? '-' }}</span></td>
                            <td>
                                <div style="font-size:.8rem; font-weight:500;">
                                    {{ $log->created_at->format('d/m/Y H:i') }}
                                </div>
                                <div style="font-size:.7rem; color:var(--text-muted);">
                                    {{ $log->created_at->diffForHumans() }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="theme-empty-state">
                                    <div class="empty-icon"><i class="bi bi-clock-history"></i></div>
                                    <h6>Belum ada aktivitas tercatat</h6>
                                    <p>Riwayat login dan logout pengguna akan tampil di sini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        const statusAnggota = @json($grafikStatusAnggota);
        const laporanPeriode = @json($grafikLaporanPeriode);

        Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
        Chart.defaults.font.size = 11;
        Chart.defaults.color = '#64748B';

        const ctxPie = document.getElementById('chartStatusAnggota');
        if (ctxPie) {
            new Chart(ctxPie, {
                type: 'doughnut',
                data: {
                    labels: statusAnggota.labels,
                    datasets: [{
                        data: statusAnggota.data,
                        backgroundColor: ['#10b981', '#f59e0b', '#f43f5e'],
                        borderColor: '#fff',
                        borderWidth: 4,
                        hoverOffset: 10,
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#1e1b4b',
                            padding: 10,
                            cornerRadius: 10,
                            callbacks: {
                                label: (ctx) => {
                                    const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                    const pct = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                                    return ` ${ctx.label}: ${ctx.parsed} (${pct}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        const ctxBar = document.getElementById('chartLaporanPeriode');
        if (ctxBar) {
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: laporanPeriode.labels,
                    datasets: [{
                            label: 'Divalidasi',
                            data: laporanPeriode.divalidasi,
                            backgroundColor: 'rgba(16,185,129,.85)',
                            borderRadius: 8,
                            maxBarThickness: 26,
                        },
                        {
                            label: 'Menunggu',
                            data: laporanPeriode.pending,
                            backgroundColor: 'rgba(99,102,241,.85)',
                            borderRadius: 8,
                            maxBarThickness: 26,
                        },
                        {
                            label: 'Ditolak',
                            data: laporanPeriode.ditolak,
                            backgroundColor: 'rgba(244,63,94,.85)',
                            borderRadius: 8,
                            maxBarThickness: 26,
                        },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 10.5
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f1f3f9'
                            },
                            ticks: {
                                precision: 0,
                                font: {
                                    size: 10.5
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                padding: 16,
                                font: {
                                    size: 11,
                                    weight: 600
                                },
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1e1b4b',
                            padding: 10,
                            cornerRadius: 10,
                            callbacks: {
                                title: (i) => i[0].label,
                                label: (c) => ` ${c.dataset.label}: ${c.parsed.y} laporan`,
                            }
                        }
                    }
                }
            });
        }
    </script>
@endpush
