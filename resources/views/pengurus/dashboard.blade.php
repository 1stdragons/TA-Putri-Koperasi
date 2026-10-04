@extends('layouts.app')

@section('title', 'Dashboard Pengurus')
@section('page-title', 'Dashboard Pengurus')
@section('page-subtitle', 'Kelola dan validasi laporan perkembangan usaha anggota koperasi')

@section('sidebar-menu')
    @include('pengurus.partials._sidebar')
@endsection

@push('styles')
    <style>
        .pengurus-dashboard-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.55fr) minmax(320px, .95fr);
            gap: 1rem;
        }

        .pengurus-donut-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.4rem;
            min-height: 220px;
        }

        .pengurus-donut {
            position: relative;
            width: 150px;
            height: 150px;
            flex-shrink: 0;
            border-radius: 50%;
        }

        .pengurus-donut::after {
            content: '';
            position: absolute;
            inset: 28px;
            border-radius: 50%;
            background: #fff;
            box-shadow: inset 0 0 0 1px #edf0f6;
        }

        .pengurus-donut-content {
            position: absolute;
            inset: 0;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .pengurus-donut-content small {
            color: var(--text-muted);
            font-size: .68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .35px;
        }

        .pengurus-donut-content strong {
            color: var(--text-dark);
            font-size: 1.55rem;
            line-height: 1.1;
        }

        .pengurus-legend {
            display: flex;
            flex: 1;
            flex-direction: column;
            gap: .85rem;
        }

        .pengurus-legend-row {
            display: grid;
            grid-template-columns: 12px 1fr auto;
            align-items: center;
            gap: .55rem;
            font-size: .76rem;
        }

        .pengurus-legend-dot {
            width: 11px;
            height: 11px;
            border-radius: 3px;
        }

        .pengurus-legend-name {
            color: var(--text-dark);
            font-weight: 700;
        }

        .pengurus-legend-value {
            color: var(--text-muted);
            white-space: nowrap;
        }

        .pengurus-chart-box {
            position: relative;
            min-height: 260px;
        }

        .pengurus-notif-item {
            display: flex;
            align-items: flex-start;
            gap: .8rem;
            padding: .95rem 1rem;
            border-bottom: 1px solid #f1f3f8;
        }

        .pengurus-notif-item:last-child {
            border-bottom: none;
        }

        .pengurus-notif-body {
            flex: 1;
            min-width: 0;
        }

        .pengurus-notif-title {
            color: var(--text-dark);
            font-size: .8rem;
            font-weight: 750;
        }

        .pengurus-notif-message {
            margin-top: .18rem;
            color: var(--text-muted);
            font-size: .71rem;
            line-height: 1.45;
        }

        .pengurus-notif-time {
            color: var(--text-muted);
            font-size: .67rem;
            white-space: nowrap;
        }

        @media (max-width: 1199.98px) {
            .pengurus-dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767.98px) {
            .pengurus-donut-wrap {
                flex-direction: column;
            }

            .pengurus-legend {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="welcome-banner d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h5 class="mb-1">
                <i class="bi bi-briefcase-fill me-1"></i>
                Selamat Datang, {{ Auth::user()->nama }} 👋
            </h5>
            <p class="mb-0">Kelola dan validasi laporan perkembangan usaha anggota koperasi.</p>
        </div>

        <div class="text-end">
            <span class="role-pill">
                <i class="bi bi-person-check-fill me-1"></i>
                Pengurus Koperasi
            </span>
            <div style="color:rgba(255,255,255,.8); font-size:.72rem; margin-top:.45rem;">
                {{ now('Asia/Jakarta')->translatedFormat('l, d F Y — H:i') }} WIB
            </div>
        </div>
    </div>

    <div class="section-title">
        <i class="bi bi-bar-chart-line-fill"></i>
        Statistik Laporan
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div>
                            <div class="stat-label">Anggota Aktif</div>
                            <div class="stat-value text-indigo">{{ number_format($totalAnggotaAktif) }}</div>
                            <div class="stat-sub">Terdaftar aktif</div>
                        </div>
                        <div class="stat-icon tint-indigo"><i class="bi bi-people-fill"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div>
                            <div class="stat-label">Menunggu Validasi</div>
                            <div class="stat-value text-amber">{{ number_format($totalLaporanMenunggu) }}</div>
                            <div class="stat-sub">Perlu segera diperiksa</div>
                        </div>
                        <div class="stat-icon tint-amber"><i class="bi bi-hourglass-split"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div>
                            <div class="stat-label">Laporan Disetujui</div>
                            <div class="stat-value text-emerald">{{ number_format($totalLaporanDisetujui) }}</div>
                            <div class="stat-sub">Sudah divalidasi</div>
                        </div>
                        <div class="stat-icon tint-emerald"><i class="bi bi-check-circle-fill"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div>
                            <div class="stat-label">Ditolak / Revisi</div>
                            <div class="stat-value text-rose">{{ number_format($totalLaporanDitolak) }}</div>
                            <div class="stat-sub">Perlu diperbaiki anggota</div>
                        </div>
                        <div class="stat-icon tint-rose"><i class="bi bi-x-circle-fill"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="pengurus-dashboard-grid mb-4">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between gap-2">
                <span>
                    <i class="bi bi-journal-check me-2" style="color:#d97706;"></i>
                    Laporan Terbaru Menunggu Validasi
                </span>
                <a href="{{ route('pengurus.validasi-laporan.index') }}" class="btn-theme-light"
                    style="padding:.35rem .75rem; font-size:.73rem;">
                    Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Anggota</th>
                            <th>Usaha</th>
                            <th>Periode</th>
                            <th>Dikirim</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($laporanMenunggu ?? [] as $laporan)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $laporan->usaha->anggota->akun->foto_url }}"
                                            alt="{{ $laporan->usaha->anggota->akun->nama }}" class="avatar-sm">
                                        <div>
                                            <div style="font-size:.79rem; font-weight:750;">
                                                {{ $laporan->usaha->anggota->akun->nama }}
                                            </div>
                                            <div style="font-size:.68rem; color:var(--text-muted);">
                                                {{ $laporan->usaha->anggota->nomor_anggota }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size:.79rem; font-weight:750;">{{ $laporan->usaha->nama_usaha }}</div>
                                    <span class="badge-soft tint-{{ $laporan->usaha->jenis_usaha_tint }}"
                                        style="font-size:.65rem;">{{ $laporan->usaha->jenis_usaha_label }}</span>
                                </td>
                                <td>
                                    <span class="badge-soft tint-indigo"
                                        style="font-size:.68rem;">Ke-{{ $laporan->periode_ke }}</span>
                                    <div style="font-size:.67rem; color:var(--text-muted); margin-top:.2rem;">
                                        {{ $laporan->tanggal_mulai->format('d/m/Y') }} –
                                        {{ $laporan->tanggal_selesai->format('d/m/Y') }}
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size:.72rem; font-weight:650;">
                                        {{ $laporan->created_at->format('d/m/Y') }}</div>
                                    <div style="font-size:.67rem; color:var(--text-muted);">
                                        {{ $laporan->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('pengurus.validasi-laporan.show', $laporan->id) }}"
                                        class="icon-action-btn btn-view" title="Periksa laporan">
                                        <i class="bi bi-journal-check"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="theme-empty-state" style="padding:2.5rem 1rem;">
                                        <div class="empty-icon" style="width:58px; height:58px; font-size:1.4rem;">
                                            <i class="bi bi-check2-all"></i>
                                        </div>
                                        <h6 style="font-size:.87rem;">Tidak ada laporan tertunda</h6>
                                        <p style="font-size:.76rem;">Seluruh laporan yang masuk telah diproses.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="bi bi-pie-chart-fill me-2" style="color:var(--primary);"></i>
                Ringkasan Evaluasi
            </div>

            <div class="card-body">
                @php
                    $batasAktif = $persentaseEvaluasiAktif;
                    $batasPeringatan = $persentaseEvaluasiAktif + $persentaseEvaluasiPeringatan;
                @endphp

                <div class="pengurus-donut-wrap">
                    <div class="pengurus-donut"
                        style="background:conic-gradient(#10b981 0% {{ $batasAktif }}%, #f59e0b {{ $batasAktif }}% {{ $batasPeringatan }}%, #f43f5e {{ $batasPeringatan }}% 100%);">
                        <div class="pengurus-donut-content">
                            <small>Total</small>
                            <strong>{{ number_format($totalEvaluasi) }}</strong>
                            <small>Evaluasi</small>
                        </div>
                    </div>

                    <div class="pengurus-legend">
                        <div class="pengurus-legend-row">
                            <span class="pengurus-legend-dot" style="background:#10b981;"></span>
                            <span class="pengurus-legend-name">Aktif</span>
                            <span class="pengurus-legend-value">{{ $totalEvaluasiAktif }}
                                ({{ number_format($persentaseEvaluasiAktif, 1, ',', '.') }}%)</span>
                        </div>
                        <div class="pengurus-legend-row">
                            <span class="pengurus-legend-dot" style="background:#f59e0b;"></span>
                            <span class="pengurus-legend-name">Peringatan</span>
                            <span class="pengurus-legend-value">{{ $totalEvaluasiPeringatan }}
                                ({{ number_format($persentaseEvaluasiPeringatan, 1, ',', '.') }}%)</span>
                        </div>
                        <div class="pengurus-legend-row">
                            <span class="pengurus-legend-dot" style="background:#f43f5e;"></span>
                            <span class="pengurus-legend-name">Tidak Aktif</span>
                            <span class="pengurus-legend-value">{{ $totalEvaluasiTidakAktif }}
                                ({{ number_format($persentaseEvaluasiTidakAktif, 1, ',', '.') }}%)</span>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-2">
                    <a href="{{ route('pengurus.evaluasi.index') }}" class="btn-theme-light"
                        style="padding:.35rem .8rem; font-size:.72rem;">Lihat Riwayat Evaluasi</a>
                </div>
            </div>
        </div>
    </div>

    <div class="pengurus-dashboard-grid">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between gap-2">
                <span>
                    <i class="bi bi-graph-up-arrow me-2" style="color:var(--primary);"></i>
                    Perkembangan Rata-rata Omzet
                </span>
                <span class="badge-soft tint-indigo" style="font-size:.68rem;">6 periode terakhir</span>
            </div>
            <div class="card-body">
                <div class="pengurus-chart-box"><canvas id="grafikOmzetPengurus"></canvas></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between gap-2">
                <span><i class="bi bi-bell-fill me-2" style="color:var(--primary);"></i>Notifikasi Terbaru</span>
                <a href="{{ route('notifikasi.index') }}" class="btn-theme-light"
                    style="padding:.3rem .7rem; font-size:.7rem;">Lihat Semua</a>
            </div>

            <div class="card-body p-0">
                @forelse ($notifikasiTerbaru as $notifikasi)
                    @php
                        $notifTint = match ($notifikasi->tipe) {
                            'sukses' => 'emerald',
                            'peringatan' => 'amber',
                            'bahaya' => 'rose',
                            default => 'indigo',
                        };

                        $notifIcon = match ($notifikasi->tipe) {
                            'sukses' => 'bi-check-circle-fill',
                            'peringatan' => 'bi-exclamation-triangle-fill',
                            'bahaya' => 'bi-x-circle-fill',
                            default => 'bi-info-circle-fill',
                        };
                    @endphp

                    <a href="{{ route('notifikasi.show', $notifikasi->id) }}" class="pengurus-notif-item"
                        style="text-decoration:none;">
                        <div class="notif-icon tint-{{ $notifTint }}"><i class="bi {{ $notifIcon }}"></i></div>
                        <div class="pengurus-notif-body">
                            <div class="pengurus-notif-title">{{ $notifikasi->judul }}</div>
                            <div class="pengurus-notif-message">
                                {{ \Illuminate\Support\Str::limit($notifikasi->pesan, 90) }}</div>
                        </div>
                        <div class="pengurus-notif-time">{{ $notifikasi->created_at->diffForHumans() }}</div>
                    </a>
                @empty
                    <div class="theme-empty-state" style="padding:2.5rem 1rem;">
                        <div class="empty-icon" style="width:58px; height:58px; font-size:1.4rem;">
                            <i class="bi bi-bell-slash"></i>
                        </div>
                        <h6 style="font-size:.87rem;">Belum ada notifikasi</h6>
                        <p style="font-size:.76rem;">Pemberitahuan terbaru akan muncul di sini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        const grafikOmzetCanvas = document.getElementById('grafikOmzetPengurus');

        if (grafikOmzetCanvas) {
            new Chart(grafikOmzetCanvas, {
                type: 'line',
                data: {
                    labels: @json($grafikOmzetLabels),
                    datasets: [{
                        label: 'Rata-rata Omzet',
                        data: @json($grafikOmzetData),
                        borderWidth: 3,
                        tension: 0.35,
                        fill: true,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        backgroundColor: 'rgba(99, 102, 241, 0.10)',
                        borderColor: '#6366f1',
                        pointBackgroundColor: '#6366f1',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label(context) {
                                    const value = Number(context.raw || 0);
                                    return 'Rata-rata: Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback(value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID', {
                                        notation: 'compact',
                                        maximumFractionDigits: 1,
                                    }).format(value);
                                }
                            },
                            grid: {
                                color: 'rgba(148, 163, 184, 0.15)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
    </script>
@endpush
