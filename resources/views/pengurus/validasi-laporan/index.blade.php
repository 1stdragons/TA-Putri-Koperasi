@extends('layouts.app')

@section('title', 'Validasi Laporan')
@section('page-title', 'Validasi Laporan Usaha')
@section('page-subtitle', 'Periksa kebenaran laporan anggota; hasil evaluasi dihitung otomatis oleh sistem')

@section('sidebar-menu')
    @include('pengurus.partials._sidebar')
@endsection

@section('content')

    {{-- ── Header ──────────────────────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div class="section-title mb-0">
            <i class="bi bi-journal-check"></i> Daftar Laporan Masuk
        </div>
        @if ($jumlahMenunggu > 0)
            <span class="badge-soft tint-rose" style="font-size:.82rem; padding:.4rem 1rem;">
                <i class="bi bi-exclamation-circle-fill me-1"></i>
                {{ $jumlahMenunggu }} laporan menunggu validasi
            </span>
        @endif
    </div>

    <div class="mb-3 p-3 d-flex align-items-start gap-2"
        style="background:linear-gradient(135deg,#eef2ff,#f5f3ff);
        border:1px solid #c7d2fe; border-radius:var(--radius-md);">
        <i class="bi bi-info-circle-fill" style="color:#4f46e5; flex-shrink:0; margin-top:.1rem;"></i>
        <div>
            <div style="font-size:.83rem; font-weight:800; color:#3730a3;">
                Pembagian tugas validasi
            </div>
            <div style="font-size:.77rem; color:#4f46e5; margin-top:.2rem; line-height:1.5;">
                Pengurus hanya memeriksa kelengkapan dan kebenaran laporan.
                Setelah laporan disetujui, hasil evaluasi usaha dihitung otomatis oleh sistem.
            </div>
        </div>
    </div>

    {{-- ── Toolbar Filter ──────────────────────────────────────── --}}
    <div class="filter-toolbar mb-3">
        <form action="{{ route('pengurus.validasi-laporan.index') }}" method="GET" id="formFilter">
            <div class="row g-2">

                <div class="col-12 col-md-4">
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" value="{{ $search }}"
                            placeholder="Cari nama anggota, nomor anggota, nama usaha...">
                    </div>
                </div>

                <div class="col-6 col-md-2">
                    <select name="status" class="form-select" onchange="document.getElementById('formFilter').submit()">
                        <option value="">Semua Status</option>
                        <option value="dikirim" {{ $filterStatus === 'dikirim' ? 'selected' : '' }}>Menunggu Validasi
                        </option>
                        <option value="divalidasi" {{ $filterStatus === 'divalidasi' ? 'selected' : '' }}>Sudah Divalidasi
                        </option>
                        <option value="ditolak" {{ $filterStatus === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
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

                <div class="col-6 col-md-2">
                    <select name="periode" class="form-select" onchange="document.getElementById('formFilter').submit()">
                        <option value="">Semua Periode</option>
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
                    @if ($search || $filterStatus || $filterJenis || $filterPeriode)
                        <a href="{{ route('pengurus.validasi-laporan.index') }}" class="btn-theme-light" title="Reset">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
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
                        <th>Anggota</th>
                        <th>
                            <a
                                href="{{ route('pengurus.validasi-laporan.index', array_merge(request()->query(), ['sort' => 'nama_usaha', 'dir' => $sort === 'nama_usaha' && $dir === 'asc' ? 'desc' : 'asc'])) }}">
                                Usaha @if ($sort === 'nama_usaha')
                                    <i class="bi bi-arrow-{{ $dir === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>Periode</th>
                        <th>
                            <a
                                href="{{ route('pengurus.validasi-laporan.index', array_merge(request()->query(), ['sort' => 'omzet', 'dir' => $sort === 'omzet' && $dir === 'asc' ? 'desc' : 'asc'])) }}">
                                Omzet @if ($sort === 'omzet')
                                    <i class="bi bi-arrow-{{ $dir === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>Biaya Ops.</th>
                        <th>
                            <a
                                href="{{ route('pengurus.validasi-laporan.index', array_merge(request()->query(), ['sort' => 'laba_bersih', 'dir' => $sort === 'laba_bersih' && $dir === 'asc' ? 'desc' : 'asc'])) }}">
                                Laba Bersih @if ($sort === 'laba_bersih')
                                    <i class="bi bi-arrow-{{ $dir === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>Status Laporan</th>
                        <th>Validasi Pengurus</th>
                        <th>Evaluasi Sistem</th>
                        <th>
                            <a
                                href="{{ route('pengurus.validasi-laporan.index', array_merge(request()->query(), ['sort' => 'created_at', 'dir' => $sort === 'created_at' && $dir === 'asc' ? 'desc' : 'asc'])) }}">
                                Dikirim @if ($sort === 'created_at')
                                    <i class="bi bi-arrow-{{ $dir === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($laporanList as $laporan)
                        @php
                            $labaBersihPositif = (float) $laporan->laba_bersih >= 0;
                        @endphp
                        <tr
                            style="{{ $laporan->status_laporan === 'dikirim' ? 'background:linear-gradient(90deg,rgba(99,102,241,.04),transparent);' : '' }}">

                            {{-- Anggota --}}
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $laporan->usaha->anggota->akun->foto_url }}" alt=""
                                        class="avatar-sm">
                                    <div>
                                        <div style="font-size:.82rem; font-weight:700;">{{ $laporan->nama_anggota }}</div>
                                        <div style="font-size:.72rem; color:var(--text-muted);">
                                            {{ $laporan->nomor_anggota }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Usaha --}}
                            <td>
                                <div style="font-size:.82rem; font-weight:700;">{{ $laporan->nama_usaha }}</div>
                                <div style="font-size:.72rem; color:var(--text-muted);">
                                    @php
                                        $jTint = match ($laporan->jenis_usaha) {
                                            'perdagangan' => 'cyan',
                                            'jasa' => 'violet',
                                            'produksi' => 'indigo',
                                            'pertanian' => 'emerald',
                                            default => 'slate',
                                        };
                                        $jLabel = match ($laporan->jenis_usaha) {
                                            'perdagangan' => 'Perdagangan',
                                            'jasa' => 'Jasa',
                                            'produksi' => 'Produksi',
                                            'pertanian' => 'Pertanian',
                                            default => 'Lainnya',
                                        };
                                    @endphp
                                    <span class="badge-soft tint-{{ $jTint }}"
                                        style="font-size:.65rem;">{{ $jLabel }}</span>
                                </div>
                            </td>

                            {{-- Periode --}}
                            <td>
                                <span class="badge-soft tint-indigo"
                                    style="font-size:.72rem; display:inline-block; margin-bottom:3px;">
                                    Ke-{{ $laporan->periode_ke }}
                                </span>
                                <div style="font-size:.7rem; color:var(--text-muted);">
                                    {{ $laporan->tanggal_mulai->format('d/m/Y') }} –
                                    {{ $laporan->tanggal_selesai->format('d/m/Y') }}
                                </div>
                            </td>

                            {{-- Omzet --}}
                            <td>
                                <div style="font-size:.82rem; font-weight:700;">
                                    Rp {{ number_format($laporan->omzet, 0, ',', '.') }}
                                </div>
                            </td>

                            {{-- Biaya --}}
                            <td>
                                <div style="font-size:.82rem; color:var(--text-muted);">
                                    Rp {{ number_format($laporan->biaya_operasional, 0, ',', '.') }}
                                </div>
                            </td>

                            {{-- Laba Bersih --}}
                            <td>
                                <div
                                    style="font-size:.82rem; font-weight:700; color:{{ $labaBersihPositif ? '#047857' : '#be123c' }};">
                                    <i class="bi bi-arrow-{{ $labaBersihPositif ? 'up' : 'down' }}-circle-fill me-1"
                                        style="font-size:.8rem;"></i>
                                    Rp {{ number_format(abs($laporan->laba_bersih), 0, ',', '.') }}
                                </div>
                            </td>

                            {{-- Status Laporan --}}
                            <td>
                                <span class="badge-soft tint-{{ $laporan->status_laporan_tint }}">
                                    <i class="bi {{ $laporan->status_laporan_icon }} me-1"></i>
                                    {{ $laporan->status_laporan_label }}
                                </span>
                            </td>

                            {{-- Status Validasi Pengurus --}}
                            <td>
                                @if ($laporan->validasi)
                                    <span class="badge-soft tint-{{ $laporan->validasi->status_pengurus_tint }}">
                                        {{ $laporan->validasi->status_pengurus_label }}
                                    </span>
                                @else
                                    <span class="badge-soft tint-amber" style="font-size:.72rem;">
                                        <i class="bi bi-hourglass-split me-1"></i>Belum Divalidasi
                                    </span>
                                @endif
                            </td>

                            {{-- Hasil Evaluasi Otomatis Sistem --}}
                            <td>
                                @if (
                                    $laporan->validasi &&
                                        $laporan->validasi->status_validasi_pengurus === 'disetujui' &&
                                        $laporan->validasi->hasil_evaluasi)
                                    <span class="badge-soft tint-{{ $laporan->validasi->hasil_evaluasi_tint }}">
                                        <i class="bi bi-cpu-fill me-1"></i>
                                        {{ $laporan->validasi->hasil_evaluasi_label }}
                                    </span>
                                @elseif ($laporan->status_laporan === 'ditolak')
                                    <span class="badge-soft tint-slate" style="font-size:.72rem;">
                                        <i class="bi bi-dash-circle me-1"></i>Belum Dihitung
                                    </span>
                                @else
                                    <span class="badge-soft tint-slate" style="font-size:.72rem;">
                                        <i class="bi bi-hourglass me-1"></i>Menunggu Validasi
                                    </span>
                                @endif
                            </td>

                            {{-- Tanggal Kirim --}}
                            <td>
                                <div style="font-size:.8rem; font-weight:500;">{{ $laporan->created_at->format('d/m/Y') }}
                                </div>
                                <div style="font-size:.7rem; color:var(--text-muted);">
                                    {{ $laporan->created_at->diffForHumans() }}</div>
                            </td>

                            {{-- Aksi --}}
                            <td class="text-center">
                                <a href="{{ route('pengurus.validasi-laporan.show', $laporan->id) }}"
                                    class="icon-action-btn btn-view" title="Periksa Laporan">
                                    <i
                                        class="bi bi-{{ $laporan->status_laporan === 'dikirim' ? 'clipboard2-check' : 'eye' }}"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11">
                                <div class="theme-empty-state">
                                    <div class="empty-icon"><i class="bi bi-journal-x"></i></div>
                                    <h6>Belum ada laporan masuk</h6>
                                    <p>
                                        @if ($search || $filterStatus || $filterJenis || $filterPeriode)
                                            Tidak ditemukan laporan yang sesuai dengan filter Anda.
                                        @else
                                            Belum ada laporan usaha yang dikirim oleh anggota.
                                        @endif
                                    </p>
                                    @if ($search || $filterStatus || $filterJenis || $filterPeriode)
                                        <a href="{{ route('pengurus.validasi-laporan.index') }}" class="btn-theme-light">
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

        @if ($laporanList->hasPages())
            <div class="card-footer d-flex flex-wrap align-items-center justify-content-between gap-2 px-3 py-3">
                <span style="font-size:.78rem; color:var(--text-muted); font-weight:500;">
                    Menampilkan {{ $laporanList->firstItem() }}–{{ $laporanList->lastItem() }}
                    dari {{ $laporanList->total() }} laporan
                </span>
                {{ $laporanList->links() }}
            </div>
        @endif
    </div>

    <div class="theme-loading-overlay" id="loadingOverlay">
        <div class="theme-spinner"></div>
        <span>Memuat data...</span>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('formFilter')?.addEventListener('submit', () => {
            document.getElementById('loadingOverlay')?.classList.add('show');
        });

        @if (session('success'))
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: @json(session('success')),
                    timer: 4000,
                    timerProgressBar: true,
                    buttonsStyling: false,
                    customClass: {
                        popup: 'swal-theme-popup',
                        title: 'swal-theme-title',
                        htmlContainer: 'swal-theme-html',
                        confirmButton: 'swal-theme-confirm'
                    }
                });
            }
        @endif
    </script>
@endpush
