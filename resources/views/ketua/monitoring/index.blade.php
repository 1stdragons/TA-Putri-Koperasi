@extends('layouts.app')

@section('title', 'Monitoring Anggota')
@section('page-title', 'Monitoring Hasil Evaluasi')
@section('page-subtitle', 'Data evaluasi anggota yang sudah melewati seluruh proses validasi')

@section('sidebar-menu')
    @include('ketua.partials._sidebar')
@endsection

@section('content')

    {{-- ── Statistik Mini ──────────────────────────────────────── --}}
    @if ($statsMonitoring && $statsMonitoring->total > 0)
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="stat-label">Total Evaluasi</div>
                                <div class="stat-value text-indigo">{{ $statsMonitoring->total }}</div>
                            </div>
                            <div class="stat-icon tint-indigo"><i class="bi bi-clipboard2-data-fill"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="stat-label">Aktif</div>
                                <div class="stat-value text-emerald">{{ $statsMonitoring->aktif }}</div>
                            </div>
                            <div class="stat-icon tint-emerald"><i class="bi bi-check-circle-fill"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="stat-label">Peringatan</div>
                                <div class="stat-value text-amber">{{ $statsMonitoring->peringatan }}</div>
                            </div>
                            <div class="stat-icon tint-amber"><i class="bi bi-exclamation-circle-fill"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="stat-label">Tidak Aktif</div>
                                <div class="stat-value text-rose">{{ $statsMonitoring->tidak_aktif }}</div>
                            </div>
                            <div class="stat-icon tint-rose"><i class="bi bi-x-circle-fill"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Filter Toolbar ──────────────────────────────────────── --}}
    <div class="filter-toolbar mb-3">
        <form action="{{ route('ketua.monitoring.index') }}" method="GET" id="formFilter">
            <div class="row g-2">

                {{-- Search --}}
                <div class="col-12 col-md-4">
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" value="{{ $search }}"
                            placeholder="Cari nama anggota, nomor anggota, nama usaha...">
                    </div>
                </div>

                {{-- Filter Hasil Evaluasi --}}
                <div class="col-6 col-md-2">
                    <select name="hasil" class="form-select" onchange="document.getElementById('formFilter').submit()">
                        <option value="">Semua Hasil</option>
                        <option value="aktif" {{ $filterHasil === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="peringatan" {{ $filterHasil === 'peringatan' ? 'selected' : '' }}>Peringatan
                        </option>
                        <option value="tidak_aktif" {{ $filterHasil === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif
                        </option>
                    </select>
                </div>

                {{-- Filter Status Anggota --}}
                <div class="col-6 col-md-2">
                    <select name="status_anggota" class="form-select"
                        onchange="document.getElementById('formFilter').submit()">
                        <option value="">Status Anggota</option>
                        <option value="aktif" {{ $filterStatus === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="peringatan" {{ $filterStatus === 'peringatan' ? 'selected' : '' }}>Peringatan
                        </option>
                        <option value="tidak_aktif" {{ $filterStatus === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif
                        </option>
                    </select>
                </div>

                {{-- Filter Jenis Usaha --}}
                <div class="col-6 col-md-2">
                    <select name="jenis_usaha" class="form-select"
                        onchange="document.getElementById('formFilter').submit()">
                        <option value="">Jenis Usaha</option>
                        @foreach (['perdagangan' => 'Perdagangan', 'jasa' => 'Jasa', 'produksi' => 'Produksi', 'pertanian' => 'Pertanian', 'lainnya' => 'Lainnya'] as $v => $l)
                            <option value="{{ $v }}" {{ $filterJenis === $v ? 'selected' : '' }}>
                                {{ $l }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Jenis Akad --}}
                <div class="col-6 col-md-2">
                    <select name="jenis_akad" class="form-select" onchange="document.getElementById('formFilter').submit()">
                        <option value="">Jenis Akad</option>
                        @foreach (['Mudharabah', 'Musyarakah', 'Murabahah', 'Ijarah', 'Qardh', 'Lainnya'] as $akad)
                            <option value="{{ $akad }}" {{ $filterAkad === $akad ? 'selected' : '' }}>
                                {{ $akad }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Periode --}}
                <div class="col-6 col-md-2">
                    <select name="periode" class="form-select" onchange="document.getElementById('formFilter').submit()">
                        <option value="">Semua Periode</option>
                        @foreach ($periodeList as $val => $label)
                            <option value="{{ $val }}" {{ $filterPeriode === $val ? 'selected' : '' }}>
                                {{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tombol --}}
                <div class="col-6 col-md-2 d-flex gap-2">
                    <button type="submit"
                        class="btn-theme-primary flex-fill d-flex align-items-center justify-content-center">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    @if ($search || $filterHasil || $filterStatus || $filterJenis || $filterAkad || $filterPeriode)
                        <a href="{{ route('ketua.monitoring.index') }}" class="btn-theme-light" title="Reset">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- ── Sorting Header ──────────────────────────────────────── --}}
    @php
        function sortUrl(string $col, string $current, string $dir): string
        {
            return request()->fullUrlWithQuery([
                'sort' => $col,
                'dir' => $current === $col && $dir === 'asc' ? 'desc' : 'asc',
            ]);
        }
        function sortIcon(string $col, string $current, string $dir): string
        {
            if ($current !== $col) {
                return '';
            }
            return '<i class="bi bi-arrow-' . ($dir === 'asc' ? 'up' : 'down') . '"></i>';
        }
    @endphp

    {{-- ── Tabel ───────────────────────────────────────────────── --}}
    <div class="card">
        <div class="table-responsive" style="max-height:720px; overflow-y:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>
                            <a href="{{ sortUrl('nama_anggota', $sort, $dir) }}">
                                Anggota {!! sortIcon('nama_anggota', $sort, $dir) !!}
                            </a>
                        </th>
                        <th>
                            <a href="{{ sortUrl('nama_usaha', $sort, $dir) }}">
                                Usaha {!! sortIcon('nama_usaha', $sort, $dir) !!}
                            </a>
                        </th>
                        <th>Periode</th>
                        <th>Omzet Sebelumnya</th>
                        <th>
                            <a href="{{ sortUrl('omzet_sekarang', $sort, $dir) }}">
                                Omzet Sekarang {!! sortIcon('omzet_sekarang', $sort, $dir) !!}
                            </a>
                        </th>
                        <th>
                            <a href="{{ sortUrl('persentase_kenaikan_omzet', $sort, $dir) }}">
                                Kenaikan {!! sortIcon('persentase_kenaikan_omzet', $sort, $dir) !!}
                            </a>
                        </th>
                        <th>
                            <a href="{{ sortUrl('rasio_biaya', $sort, $dir) }}">
                                Rasio Biaya {!! sortIcon('rasio_biaya', $sort, $dir) !!}
                            </a>
                        </th>
                        <th>Indikator</th>
                        <th>Hasil</th>
                        <th>Validasi</th>
                        <th class="text-center">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($monitoringList as $ev)
                        @php
                            $kOk = (float) $ev->persentase_kenaikan_omzet >= 5;
                            $rOk = (float) $ev->rasio_biaya < 60;
                            $aTint = match ($ev->status_anggota) {
                                'aktif' => 'emerald',
                                'peringatan' => 'amber',
                                default => 'rose',
                            };
                        @endphp
                        <tr>
                            {{-- Anggota --}}
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $ev->laporan->usaha->anggota->akun->foto_url }}" alt=""
                                        class="avatar-sm">
                                    <div>
                                        <div style="font-size:.82rem; font-weight:700;">{{ $ev->nama_anggota }}</div>
                                        <div class="d-flex gap-1 mt-1 flex-wrap">
                                            <span class="code-tag"
                                                style="font-size:.65rem;">{{ $ev->nomor_anggota }}</span>
                                            <span class="badge-soft tint-{{ $aTint }}" style="font-size:.63rem;">
                                                {{ match ($ev->status_anggota) {'aktif' => 'Aktif','peringatan' => 'Peringatan',default => 'Tdk Aktif'} }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Usaha --}}
                            <td>
                                <div style="font-size:.82rem; font-weight:700;">{{ $ev->nama_usaha }}</div>
                                @php
                                    $jTint = match ($ev->jenis_usaha ?? '') {
                                        'perdagangan' => 'cyan',
                                        'jasa' => 'violet',
                                        'produksi' => 'indigo',
                                        'pertanian' => 'emerald',
                                        default => 'slate',
                                    };
                                    $jLabel = match ($ev->jenis_usaha ?? '') {
                                        'perdagangan' => 'Perdagangan',
                                        'jasa' => 'Jasa',
                                        'produksi' => 'Produksi',
                                        'pertanian' => 'Pertanian',
                                        default => 'Lainnya',
                                    };
                                @endphp
                                <div class="d-flex gap-1 mt-1 flex-wrap">
                                    <span class="badge-soft tint-{{ $jTint }}"
                                        style="font-size:.65rem;">{{ $jLabel }}</span>
                                    @if ($ev->jenis_akad)
                                        <span class="badge-soft tint-violet"
                                            style="font-size:.65rem;">{{ $ev->jenis_akad }}</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Periode --}}
                            <td>
                                <span class="badge-soft tint-indigo"
                                    style="font-size:.72rem; display:block; margin-bottom:3px;">
                                    Ke-{{ $ev->periode_ke }}
                                </span>
                                <div style="font-size:.7rem; color:var(--text-muted);">
                                    {{ \Carbon\Carbon::parse($ev->tanggal_mulai)->format('d/m/Y') }}
                                    – {{ \Carbon\Carbon::parse($ev->tanggal_selesai)->format('d/m/Y') }}
                                </div>
                            </td>

                            {{-- Omzet Sebelumnya --}}
                            <td style="font-size:.8rem; color:var(--text-muted);">
                                @if ((float) $ev->omzet_sebelumnya > 0)
                                    Rp {{ number_format($ev->omzet_sebelumnya, 0, ',', '.') }}
                                @else
                                    <span style="color:var(--text-light);">Pertama</span>
                                @endif
                            </td>

                            {{-- Omzet Sekarang --}}
                            <td>
                                <div style="font-size:.82rem; font-weight:700; color:#047857;">
                                    Rp {{ number_format($ev->omzet_sekarang, 0, ',', '.') }}
                                </div>
                            </td>

                            {{-- Kenaikan Omzet --}}
                            <td>
                                <div style="font-size:.82rem; font-weight:700; color:{{ $kOk ? '#047857' : '#be123c' }};">
                                    <i class="bi bi-arrow-{{ (float) $ev->persentase_kenaikan_omzet >= 0 ? 'up' : 'down' }}-circle-fill me-1"
                                        style="font-size:.78rem;"></i>
                                    {{ $ev->persentase_kenaikan_omzet }}%
                                </div>
                                <div style="font-size:.68rem; color:var(--text-muted);">Target ≥ 5%</div>
                            </td>

                            {{-- Rasio Biaya --}}
                            <td>
                                <div style="font-size:.82rem; font-weight:700; color:{{ $rOk ? '#047857' : '#be123c' }};">
                                    {{ $ev->rasio_biaya }}%
                                </div>
                                <div style="font-size:.68rem; color:var(--text-muted);">Target &lt; 60%</div>
                            </td>

                            {{-- Indikator --}}
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <span class="badge-soft {{ $kOk ? 'tint-emerald' : 'tint-rose' }}"
                                        style="font-size:.65rem;">
                                        <i class="bi bi-{{ $kOk ? 'check' : 'x' }}-circle-fill me-1"></i>Omzet
                                    </span>
                                    <span class="badge-soft {{ $rOk ? 'tint-emerald' : 'tint-rose' }}"
                                        style="font-size:.65rem;">
                                        <i class="bi bi-{{ $rOk ? 'check' : 'x' }}-circle-fill me-1"></i>Biaya
                                    </span>
                                </div>
                            </td>

                            {{-- Hasil Evaluasi --}}
                            <td>
                                <span class="badge-soft tint-{{ $ev->hasil_evaluasi_tint }}">
                                    {{ $ev->hasil_evaluasi_label }}
                                </span>
                            </td>

                            {{-- Status Validasi --}}
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <span class="badge-soft tint-emerald" style="font-size:.65rem;">
                                        <i class="bi bi-check-circle-fill me-1"></i>Pengurus
                                    </span>
                                    <span class="badge-soft tint-emerald" style="font-size:.65rem;">
                                        <i class="bi bi-patch-check-fill me-1"></i>DPS
                                    </span>
                                </div>
                            </td>

                            {{-- Aksi Detail --}}
                            <td class="text-center">
                                <a href="{{ route('ketua.monitoring.show', $ev->id) }}" class="icon-action-btn btn-view"
                                    title="Lihat Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11">
                                <div class="theme-empty-state">
                                    <div class="empty-icon"><i class="bi bi-clipboard2-x"></i></div>
                                    <h6>Belum ada data monitoring</h6>
                                    <p>
                                        @if ($search || $filterHasil || $filterStatus || $filterJenis || $filterAkad || $filterPeriode)
                                            Tidak ditemukan data yang sesuai dengan filter Anda.
                                        @else
                                            Data monitoring akan muncul setelah evaluasi 14 hari selesai dijalankan.
                                        @endif
                                    </p>
                                    @if ($search || $filterHasil || $filterStatus || $filterJenis || $filterAkad || $filterPeriode)
                                        <a href="{{ route('ketua.monitoring.index') }}" class="btn-theme-light">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($monitoringList->hasPages())
            <div class="card-footer d-flex flex-wrap align-items-center justify-content-between gap-2 px-3 py-3">
                <span style="font-size:.78rem; color:var(--text-muted); font-weight:500;">
                    Menampilkan {{ $monitoringList->firstItem() }}–{{ $monitoringList->lastItem() }}
                    dari {{ $monitoringList->total() }} data monitoring
                </span>
                {{ $monitoringList->links() }}
            </div>
        @endif
    </div>

    <div class="theme-loading-overlay" id="loadingOverlay">
        <div class="theme-spinner"></div>
        <span>Memuat data monitoring...</span>
    </div>

@endsection

@push('scripts')
    <script>
        document.getElementById('formFilter').addEventListener('submit', () => {
            document.getElementById('loadingOverlay').classList.add('show');
        });
    </script>
@endpush
