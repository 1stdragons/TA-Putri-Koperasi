@extends('layouts.app')

@section('title', 'Laporan Usaha')
@section('page-title', 'Laporan Usaha 14 Hari')
@section('page-subtitle', 'Monitoring laporan perkembangan usaha seluruh anggota')

@section('sidebar-menu')
    @include('admin.partials._sidebar')
@endsection

@section('content')

    {{-- ── Header ──────────────────────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div class="section-title mb-0">
            <i class="bi bi-journal-richtext"></i> Daftar Laporan Usaha
        </div>
        <a href="{{ route('admin.laporan.create') }}" class="btn-theme-primary">
            <i class="bi bi-plus-lg"></i> Tambah Laporan
        </a>
    </div>

    {{-- ── Toolbar Filter ──────────────────────────────────────── --}}
    <div class="filter-toolbar mb-3">
        <form action="{{ route('admin.laporan.index') }}" method="GET" id="formFilter">
            <div class="row g-2">

                {{-- Search --}}
                <div class="col-12 col-md-4">
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" value="{{ $search }}"
                            placeholder="Cari nama anggota, nomor anggota, nama usaha...">
                    </div>
                </div>

                {{-- Filter Status --}}
                <div class="col-6 col-md-2">
                    <select name="status" class="form-select" onchange="document.getElementById('formFilter').submit()">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ $filterStatus === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="dikirim" {{ $filterStatus === 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                        <option value="divalidasi" {{ $filterStatus === 'divalidasi' ? 'selected' : '' }}>Divalidasi
                        </option>
                        <option value="ditolak" {{ $filterStatus === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
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

                {{-- Filter Periode --}}
                <div class="col-6 col-md-2">
                    <select name="periode" class="form-select" onchange="document.getElementById('formFilter').submit()">
                        <option value="">Semua Periode</option>
                        @foreach ($periodeList as $val => $label)
                            <option value="{{ $val }}" {{ $filterPeriode === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tombol --}}
                <div class="col-6 col-md-2 d-flex gap-2">
                    <button type="submit"
                        class="btn-theme-primary flex-fill d-flex align-items-center justify-content-center">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    @if ($search || $filterStatus || $filterJenis || $filterPeriode)
                        <a href="{{ route('admin.laporan.index') }}" class="btn-theme-light" title="Reset">
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
                        <th>Usaha</th>
                        <th>Periode</th>
                        <th>
                            <a
                                href="{{ route('admin.laporan.index', array_merge(request()->query(), ['sort' => 'omzet', 'dir' => $sort === 'omzet' && $dir === 'asc' ? 'desc' : 'asc'])) }}">
                                Omzet @if ($sort === 'omzet')
                                    <i class="bi bi-arrow-{{ $dir === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>Biaya Ops.</th>
                        <th>
                            <a
                                href="{{ route('admin.laporan.index', array_merge(request()->query(), ['sort' => 'laba_bersih', 'dir' => $sort === 'laba_bersih' && $dir === 'asc' ? 'desc' : 'asc'])) }}">
                                Laba Bersih @if ($sort === 'laba_bersih')
                                    <i class="bi bi-arrow-{{ $dir === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>Status</th>
                        <th>
                            <a
                                href="{{ route('admin.laporan.index', array_merge(request()->query(), ['sort' => 'created_at', 'dir' => $sort === 'created_at' && $dir === 'asc' ? 'desc' : 'asc'])) }}">
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
                        <tr>
                            {{-- Anggota --}}
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $laporan->usaha->anggota->akun->foto_url }}" alt=""
                                        class="avatar-sm">
                                    <div>
                                        <div style="font-size:.82rem; font-weight:700;">
                                            {{ $laporan->nama_anggota }}
                                        </div>
                                        <div style="font-size:.72rem; color:var(--text-muted);">
                                            {{ $laporan->nomor_anggota }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Usaha --}}
                            <td>
                                <div style="font-size:.82rem; font-weight:700;">
                                    {{ $laporan->nama_usaha }}
                                </div>
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
                                    <span class="badge-soft tint-{{ $jTint }}" style="font-size:.65rem;">
                                        {{ $jLabel }}
                                    </span>
                                </div>
                            </td>

                            {{-- Periode --}}
                            <td>
                                <span class="badge-soft tint-indigo"
                                    style="font-size:.72rem; margin-bottom:3px; display:inline-block;">
                                    Ke-{{ $laporan->periode_ke }}
                                </span>
                                <div style="font-size:.7rem; color:var(--text-muted);">
                                    {{ $laporan->tanggal_mulai->format('d/m/Y') }}
                                    – {{ $laporan->tanggal_selesai->format('d/m/Y') }}
                                </div>
                            </td>

                            {{-- Omzet --}}
                            <td>
                                <div style="font-size:.82rem; font-weight:700; color:var(--text-dark);">
                                    Rp {{ number_format($laporan->omzet, 0, ',', '.') }}
                                </div>
                            </td>

                            {{-- Biaya Operasional --}}
                            <td>
                                <div style="font-size:.82rem; font-weight:600; color:var(--text-muted);">
                                    Rp {{ number_format($laporan->biaya_operasional, 0, ',', '.') }}
                                </div>
                            </td>

                            {{-- Laba Bersih --}}
                            <td>
                                <div
                                    style="font-size:.82rem; font-weight:700;
                                        color:{{ $labaBersihPositif ? '#047857' : '#be123c' }};">
                                    <i class="bi bi-{{ $labaBersihPositif ? 'arrow-up' : 'arrow-down' }}-circle-fill me-1"
                                        style="font-size:.8rem;"></i>
                                    Rp {{ number_format(abs($laporan->laba_bersih), 0, ',', '.') }}
                                </div>
                            </td>

                            {{-- Status --}}
                            <td>
                                <span class="badge-soft tint-{{ $laporan->status_laporan_tint }}">
                                    <i class="bi {{ $laporan->status_laporan_icon }} me-1"></i>
                                    {{ $laporan->status_laporan_label }}
                                </span>
                            </td>

                            {{-- Tanggal Upload --}}
                            <td>
                                <div style="font-size:.8rem; font-weight:500;">
                                    {{ $laporan->created_at->format('d/m/Y') }}
                                </div>
                                <div style="font-size:.7rem; color:var(--text-muted);">
                                    {{ $laporan->created_at->diffForHumans() }}
                                </div>
                            </td>

                            {{-- Aksi --}}
                            <td class="text-center">
                                <a href="{{ route('admin.laporan.show', $laporan->id) }}" class="icon-action-btn btn-view"
                                    title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.laporan.edit', $laporan->id) }}" class="icon-action-btn btn-edit"
                                    title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="icon-action-btn btn-delete btn-hapus-laporan" title="Hapus"
                                    data-id="{{ $laporan->id }}"
                                    data-info="Periode ke-{{ $laporan->periode_ke }} — {{ $laporan->nama_usaha }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="theme-empty-state">
                                    <div class="empty-icon"><i class="bi bi-journal-x"></i></div>
                                    <h6>Belum ada laporan usaha</h6>
                                    <p>
                                        @if ($search || $filterStatus || $filterJenis || $filterPeriode)
                                            Tidak ditemukan laporan yang sesuai dengan filter Anda.
                                        @else
                                            Laporan usaha 14 hari dari anggota akan muncul di sini.
                                        @endif
                                    </p>
                                    @if ($search || $filterStatus || $filterJenis || $filterPeriode)
                                        <a href="{{ route('admin.laporan.index') }}" class="btn-theme-light">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                                        </a>
                                    @else
                                        <a href="{{ route('admin.laporan.create') }}" class="btn-theme-primary">
                                            <i class="bi bi-plus-lg me-1"></i> Tambah Laporan Pertama
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

    <form id="formHapusLaporan" method="POST" style="display:none;">
        @csrf @method('DELETE')
    </form>

    <div class="theme-loading-overlay" id="loadingOverlay">
        <div class="theme-spinner"></div>
        <span>Memproses data...</span>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.btn-hapus-laporan').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const info = this.dataset.info;

                Swal.fire({
                    title: 'Hapus Laporan?',
                    html: `
                    <p style="color:var(--text-muted); font-size:.87rem;">Anda akan menghapus laporan:</p>
                    <p style="font-weight:800; color:var(--text-dark); margin:.3rem 0;">${info}</p>
                    <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:10px;
                                padding:.7rem .9rem; font-size:.81rem; color:#92400e; text-align:left; margin-top:.6rem;">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        <strong>Perhatian:</strong> Data evaluasi terkait laporan ini juga akan ikut terhapus.
                    </div>
                `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    buttonsStyling: false,
                    customClass: {
                        popup: 'swal-theme-popup',
                        title: 'swal-theme-title',
                        htmlContainer: 'swal-theme-html',
                        confirmButton: 'swal-theme-deny',
                        cancelButton: 'swal-theme-cancel',
                    }
                }).then(result => {
                    if (result.isConfirmed) {
                        document.getElementById('loadingOverlay').classList.add('show');
                        const form = document.getElementById('formHapusLaporan');
                        form.action = `{{ url('admin/laporan') }}/${id}`;
                        form.submit();
                    }
                });
            });
        });

        document.getElementById('formFilter').addEventListener('submit', () => {
            document.getElementById('loadingOverlay').classList.add('show');
        });

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 3500,
                timerProgressBar: true,
                buttonsStyling: false,
                customClass: {
                    popup: 'swal-theme-popup',
                    title: 'swal-theme-title',
                    htmlContainer: 'swal-theme-html',
                    confirmButton: 'swal-theme-confirm'
                }
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
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
