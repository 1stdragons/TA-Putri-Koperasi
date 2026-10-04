@extends('layouts.app')

@section('title', 'Rekap Monitoring')
@section('page-title', 'Rekap Monitoring Usaha Anggota')
@section('page-subtitle', 'Data evaluasi yang sudah melewati seluruh proses validasi')

@section('sidebar-menu')
    @include('admin.partials._sidebar')
@endsection

@section('content')

    {{-- ── Statistik Ringkas ────────────────────────────────────── --}}
    @if ($stats->total_laporan > 0)
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="stat-label">Total Laporan</div>
                                <div class="stat-value text-indigo">{{ $stats->total_laporan }}</div>
                            </div>
                            <div class="stat-icon tint-indigo"><i class="bi bi-journal-richtext"></i></div>
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
                                <div class="stat-value text-emerald">{{ $stats->jumlah_aktif }}</div>
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
                                <div class="stat-value text-amber">{{ $stats->jumlah_peringatan }}</div>
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
                                <div class="stat-value text-rose">{{ $stats->jumlah_tidak_aktif }}</div>
                            </div>
                            <div class="stat-icon tint-rose"><i class="bi bi-x-circle-fill"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Filter ───────────────────────────────────────────────── --}}
    <div class="filter-toolbar mb-3">
        <form action="{{ route('admin.rekap-monitoring.index') }}" method="GET" id="formFilter">
            <div class="row g-2">
                <div class="col-12 col-md-3">
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" value="{{ $search }}"
                            placeholder="Cari anggota/usaha...">
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <select name="hasil" class="form-select">
                        <option value="">Semua Hasil</option>
                        <option value="aktif" {{ $filterHasil === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="peringatan" {{ $filterHasil === 'peringatan' ? 'selected' : '' }}>Peringatan</option>
                        <option value="tidak_aktif" {{ $filterHasil === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif
                        </option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="jenis_usaha" class="form-select">
                        <option value="">Jenis Usaha</option>
                        @foreach (['perdagangan' => 'Perdagangan', 'jasa' => 'Jasa', 'produksi' => 'Produksi', 'pertanian' => 'Pertanian', 'lainnya' => 'Lainnya'] as $v => $l)
                            <option value="{{ $v }}" {{ $filterJenis === $v ? 'selected' : '' }}>
                                {{ $l }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="jenis_akad" class="form-select">
                        <option value="">Jenis Akad</option>
                        @foreach (['Mudharabah', 'Musyarakah', 'Murabahah', 'Ijarah', 'Qardh', 'Lainnya'] as $akad)
                            <option value="{{ $akad }}" {{ $filterAkad === $akad ? 'selected' : '' }}>
                                {{ $akad }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="periode" class="form-select">
                        <option value="">Semua Periode</option>
                        @foreach ($periodeList as $val => $label)
                            <option value="{{ $val }}" {{ $filterPeriode === $val ? 'selected' : '' }}>
                                {{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-1 d-flex gap-2">
                    <button type="submit"
                        class="btn-theme-primary flex-fill d-flex align-items-center justify-content-center">
                        <i class="bi bi-funnel"></i>
                    </button>
                </div>
            </div>
            <div class="row g-2 mt-2">
                <div class="col-6 col-md-3">
                    <label class="form-label" style="font-size:.72rem;">Tgl Mulai Dari</label>
                    <input type="date" name="tgl_mulai_dari" class="form-control" value="{{ $filterTglMulaiDari }}">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label" style="font-size:.72rem;">Tgl Mulai Sampai</label>
                    <input type="date" name="tgl_mulai_sampai" class="form-control" value="{{ $filterTglMulaiSampai }}">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label" style="font-size:.72rem;">Tgl Selesai Dari</label>
                    <input type="date" name="tgl_selesai_dari" class="form-control" value="{{ $filterTglSelesaiDari }}">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label" style="font-size:.72rem;">Tgl Selesai Sampai</label>
                    <input type="date" name="tgl_selesai_sampai" class="form-control"
                        value="{{ $filterTglSelesaiSampai }}">
                </div>
            </div>
            <div class="row g-2 mt-2">
                <div class="col-12 d-flex gap-2 justify-content-end">
                    @if (
                        $search ||
                            $filterHasil ||
                            $filterStatus ||
                            $filterJenis ||
                            $filterAkad ||
                            $filterPeriode ||
                            $filterTglMulaiDari ||
                            $filterTglMulaiSampai)
                        <a href="{{ route('admin.rekap-monitoring.index') }}" class="btn-theme-light"
                            style="padding:.4rem .9rem; font-size:.8rem;">
                            <i class="bi bi-x-lg me-1"></i>Reset
                        </a>
                    @endif
                    <button type="submit" class="btn-theme-primary" style="padding:.4rem 1rem; font-size:.8rem;">
                        <i class="bi bi-funnel me-1"></i>Terapkan Filter
                    </button>
                    <a href="{{ route('admin.rekap-monitoring.pdf', request()->query()) }}" class="btn-theme-primary"
                        id="btnCetakPdf"
                        style="padding:.4rem 1rem; font-size:.8rem; background:linear-gradient(135deg,#dc2626,#ef4444);">
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i>Cetak PDF Rekap
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- ── Tabel ───────────────────────────────────────────────── --}}
    <div class="card">
        <div class="table-responsive" style="max-height:680px; overflow-y:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th><a
                                href="{{ request()->fullUrlWithQuery(['sort' => 'nama_anggota', 'dir' => $sort === 'nama_anggota' && $dir === 'asc' ? 'desc' : 'asc']) }}">Anggota</a>
                        </th>
                        <th><a
                                href="{{ request()->fullUrlWithQuery(['sort' => 'nama_usaha', 'dir' => $sort === 'nama_usaha' && $dir === 'asc' ? 'desc' : 'asc']) }}">Usaha</a>
                        </th>
                        <th>Akad</th>
                        <th><a
                                href="{{ request()->fullUrlWithQuery(['sort' => 'periode_ke', 'dir' => $sort === 'periode_ke' && $dir === 'asc' ? 'desc' : 'asc']) }}">Periode</a>
                        </th>
                        <th><a
                                href="{{ request()->fullUrlWithQuery(['sort' => 'omzet_sekarang', 'dir' => $sort === 'omzet_sekarang' && $dir === 'asc' ? 'desc' : 'asc']) }}">Omzet</a>
                        </th>
                        <th><a
                                href="{{ request()->fullUrlWithQuery(['sort' => 'biaya_operasional', 'dir' => $sort === 'biaya_operasional' && $dir === 'asc' ? 'desc' : 'asc']) }}">Biaya</a>
                        </th>
                        <th><a
                                href="{{ request()->fullUrlWithQuery(['sort' => 'laba_bersih', 'dir' => $sort === 'laba_bersih' && $dir === 'asc' ? 'desc' : 'asc']) }}">Laba</a>
                        </th>
                        <th><a
                                href="{{ request()->fullUrlWithQuery(['sort' => 'persentase_kenaikan_omzet', 'dir' => $sort === 'persentase_kenaikan_omzet' && $dir === 'asc' ? 'desc' : 'asc']) }}">Kenaikan</a>
                        </th>
                        <th><a
                                href="{{ request()->fullUrlWithQuery(['sort' => 'rasio_biaya', 'dir' => $sort === 'rasio_biaya' && $dir === 'asc' ? 'desc' : 'asc']) }}">Rasio
                                Biaya</a></th>
                        <th><a
                                href="{{ request()->fullUrlWithQuery(['sort' => 'hasil_evaluasi', 'dir' => $sort === 'hasil_evaluasi' && $dir === 'asc' ? 'desc' : 'asc']) }}">Hasil</a>
                        </th>
                        <th><a
                                href="{{ request()->fullUrlWithQuery(['sort' => 'tanggal_evaluasi', 'dir' => $sort === 'tanggal_evaluasi' && $dir === 'asc' ? 'desc' : 'asc']) }}">Tgl
                                Evaluasi</a></th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rekapList as $i => $r)
                        <tr>
                            <td>{{ ($rekapList->currentPage() - 1) * $rekapList->perPage() + $i + 1 }}</td>
                            <td>
                                <div style="font-size:.82rem; font-weight:700;">{{ $r->nama_anggota }}</div>
                                <span class="code-tag" style="font-size:.66rem;">{{ $r->nomor_anggota }}</span>
                            </td>
                            <td style="font-size:.82rem; font-weight:700;">{{ $r->nama_usaha }}</td>
                            <td>
                                @if ($r->jenis_akad)
                                    @php
                                        $akadTint = match ($r->jenis_akad) {
                                            'Mudharabah' => 'violet',
                                            'Musyarakah' => 'indigo',
                                            'Murabahah' => 'cyan',
                                            'Ijarah' => 'amber',
                                            'Qardh' => 'pink',
                                            default => 'slate',
                                        };
                                    @endphp

                                    <span class="badge-soft tint-{{ $akadTint }}" style="font-size:.68rem;">
                                        {{ $r->jenis_akad }}
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                            <td><span class="badge-soft tint-indigo"
                                    style="font-size:.7rem;">Ke-{{ $r->periode_ke }}</span></td>
                            <td style="font-size:.8rem; font-weight:700; color:#047857;">Rp
                                {{ number_format($r->omzet_sekarang, 0, ',', '.') }}</td>
                            <td style="font-size:.8rem; color:var(--text-muted);">Rp
                                {{ number_format($r->biaya_operasional, 0, ',', '.') }}</td>
                            <td style="font-size:.8rem; font-weight:700;">Rp
                                {{ number_format($r->laba_bersih, 0, ',', '.') }}</td>
                            <td
                                style="font-size:.8rem; font-weight:700; color:{{ $r->persentase_kenaikan_omzet >= 5 ? '#047857' : '#be123c' }};">
                                {{ $r->persentase_kenaikan_omzet }}%</td>
                            <td
                                style="font-size:.8rem; font-weight:700; color:{{ $r->rasio_biaya < 60 ? '#047857' : '#be123c' }};">
                                {{ $r->rasio_biaya }}%</td>
                            <td><span
                                    class="badge-soft tint-{{ $r->hasil_evaluasi_tint }}">{{ $r->hasil_evaluasi_label }}</span>
                            </td>
                            <td style="font-size:.76rem; color:var(--text-muted);">
                                {{ \Carbon\Carbon::parse($r->tanggal_evaluasi)->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.rekap-monitoring.detail-pdf', $r->laporan_id) }}"
                                    class="icon-action-btn btn-view btn-cetak-detail" title="Cetak PDF Detail">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13">
                                <div class="theme-empty-state">
                                    <div class="empty-icon"><i class="bi bi-clipboard2-x"></i></div>
                                    <h6>Belum ada data rekap</h6>
                                    <p>Data akan muncul setelah evaluasi 14 hari selesai untuk seluruh proses validasi.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($rekapList->hasPages())
            <div class="card-footer d-flex flex-wrap align-items-center justify-content-between gap-2 px-3 py-3">
                <span style="font-size:.78rem; color:var(--text-muted);">Menampilkan
                    {{ $rekapList->firstItem() }}–{{ $rekapList->lastItem() }} dari {{ $rekapList->total() }} data</span>
                {{ $rekapList->links() }}
            </div>
        @endif
    </div>

    <div class="theme-loading-overlay" id="loadingOverlay">
        <div class="theme-spinner"></div>
        <span>Memproses...</span>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('formFilter').addEventListener('submit', () => {
            document.getElementById('loadingOverlay').classList.add('show');
        });
        document.getElementById('btnCetakPdf').addEventListener('click', () => {
            document.getElementById('loadingOverlay').classList.add('show');
            setTimeout(() => document.getElementById('loadingOverlay').classList.remove('show'), 4000);
        });
        document.querySelectorAll('.btn-cetak-detail').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('loadingOverlay').classList.add('show');
                setTimeout(() => document.getElementById('loadingOverlay').classList.remove('show'), 3000);
            });
        });
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Tidak Dapat Mencetak',
                text: '{{ session('error') }}',
                buttonsStyling: false,
                customClass: {
                    popup: 'swal-theme-popup',
                    title: 'swal-theme-title',
                    htmlContainer: 'swal-theme-html',
                    confirmButton: 'swal-theme-deny'
                }
            });
        @endif
    </script>
@endpush
