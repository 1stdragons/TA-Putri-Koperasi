@extends('layouts.app')

@section('title', 'Riwayat Evaluasi')
@section('page-title', 'Riwayat Evaluasi 14 Hari')
@section('page-subtitle', 'Pantau hasil evaluasi seluruh laporan yang sudah divalidasi')

@section('sidebar-menu')
    @include('pengurus.partials._sidebar')
@endsection

@section('content')

    @if ($stats && $stats->total > 0)
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="stat-label">Total Evaluasi</div>
                                <div class="stat-value text-indigo">{{ number_format($stats->total) }}</div>
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
                                <div class="stat-value text-emerald">{{ number_format($stats->aktif) }}</div>
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
                                <div class="stat-value text-amber">{{ number_format($stats->peringatan) }}</div>
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
                                <div class="stat-value text-rose">{{ number_format($stats->tidak_aktif) }}</div>
                            </div>
                            <div class="stat-icon tint-rose"><i class="bi bi-x-circle-fill"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="filter-toolbar mb-3">
        <form action="{{ route('pengurus.evaluasi.index') }}" method="GET" id="formFilter">
            <div class="row g-2">
                <div class="col-12 col-md-5">
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" value="{{ $search }}"
                            placeholder="Cari nama anggota, nomor, atau usaha...">
                    </div>
                </div>
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
                <div class="col-6 col-md-1">
                    <select name="periode" class="form-select" onchange="document.getElementById('formFilter').submit()">
                        <option value="">Periode</option>
                        @foreach ($periodeList as $val => $label)
                            <option value="{{ $val }}" {{ $filterPeriode === $val ? 'selected' : '' }}>
                                {{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2 d-flex gap-2">
                    <button type="submit"
                        class="btn-theme-primary flex-fill d-flex align-items-center justify-content-center">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    @if ($search || $filterHasil || $filterJenis || $filterPeriode)
                        <a href="{{ route('pengurus.evaluasi.index') }}" class="btn-theme-light" title="Reset">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="table-responsive" style="max-height:680px; overflow-y:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Anggota</th>
                        <th>Usaha</th>
                        <th>Periode</th>
                        <th>Omzet Sebelumnya</th>
                        <th>Omzet Sekarang</th>
                        <th>Kenaikan</th>
                        <th>Rasio Biaya</th>
                        <th>Indikator</th>
                        <th>Hasil</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($evaluasiList as $ev)
                        <tr>
                            <td>
                                <div style="font-size:.82rem; font-weight:700;">{{ $ev->nama_anggota }}</div>
                                <span class="code-tag" style="font-size:.68rem;">{{ $ev->nomor_anggota }}</span>
                            </td>
                            <td style="font-size:.82rem; font-weight:700;">{{ $ev->nama_usaha }}</td>
                            <td>
                                <span class="badge-soft tint-indigo"
                                    style="font-size:.72rem;">Ke-{{ $ev->periode_ke }}</span>
                            </td>
                            <td style="font-size:.8rem; color:var(--text-muted);">
                                {{ $ev->omzet_sebelumnya > 0 ? 'Rp ' . number_format($ev->omzet_sebelumnya, 0, ',', '.') : 'Pertama' }}
                            </td>
                            <td style="font-size:.82rem; font-weight:700; color:#047857;">
                                Rp {{ number_format($ev->omzet_sekarang, 0, ',', '.') }}
                            </td>
                            <td>
                                @php $kenaikanOk = (float) $ev->persentase_kenaikan_omzet >= 5; @endphp
                                <div
                                    style="font-size:.82rem; font-weight:700; color:{{ $kenaikanOk ? '#047857' : '#be123c' }};">
                                    {{ $ev->persentase_kenaikan_omzet }}%
                                </div>
                            </td>
                            <td>
                                @php $rasioOk = (float) $ev->rasio_biaya < 60; @endphp
                                <div
                                    style="font-size:.82rem; font-weight:700; color:{{ $rasioOk ? '#047857' : '#be123c' }};">
                                    {{ $ev->rasio_biaya }}%
                                </div>
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-column">
                                    <span class="badge-soft {{ $ev->indikator_omzet ? 'tint-emerald' : 'tint-rose' }}"
                                        style="font-size:.65rem;">
                                        <i
                                            class="bi bi-{{ $ev->indikator_omzet ? 'check' : 'x' }}-circle-fill me-1"></i>Omzet
                                    </span>
                                    <span class="badge-soft {{ $ev->indikator_biaya ? 'tint-emerald' : 'tint-rose' }}"
                                        style="font-size:.65rem;">
                                        <i
                                            class="bi bi-{{ $ev->indikator_biaya ? 'check' : 'x' }}-circle-fill me-1"></i>Biaya
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="badge-soft tint-{{ $ev->hasil_evaluasi_tint }}" style="font-size:.78rem;">
                                    {{ $ev->hasil_evaluasi_label }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('pengurus.evaluasi.show', $ev->id) }}" class="icon-action-btn btn-view"
                                    title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">
                                <div class="theme-empty-state">
                                    <div class="empty-icon"><i class="bi bi-clipboard2-x"></i></div>
                                    <h6>Belum ada data evaluasi</h6>
                                    <p>Evaluasi akan muncul setelah seluruh proses validasi selesai.</p>
                                    @if ($search || $filterHasil || $filterJenis || $filterPeriode)
                                        <a href="{{ route('pengurus.evaluasi.index') }}" class="btn-theme-light">
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

        @if ($evaluasiList->hasPages())
            <div class="card-footer d-flex flex-wrap align-items-center justify-content-between gap-2 px-3 py-3">
                <span style="font-size:.78rem; color:var(--text-muted); font-weight:500;">
                    Menampilkan {{ $evaluasiList->firstItem() }}–{{ $evaluasiList->lastItem() }}
                    dari {{ $evaluasiList->total() }} evaluasi
                </span>
                {{ $evaluasiList->links() }}
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
    </script>
@endpush
