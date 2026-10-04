@extends('layouts.app')

@section('title', 'Data Anggota')
@section('page-title', 'Data Anggota')
@section('page-subtitle', 'Kelola seluruh data anggota koperasi')

@section('sidebar-menu')
    @include('admin.partials._sidebar')
@endsection

@section('content')

    {{-- ── Header ──────────────────────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div class="section-title mb-0">
            <i class="bi bi-people-fill"></i> Daftar Anggota Koperasi
        </div>
        <a href="{{ route('admin.anggota.create') }}" class="btn-theme-primary">
            <i class="bi bi-plus-lg"></i> Tambah Anggota
        </a>
    </div>

    {{-- ── Toolbar ─────────────────────────────────────────────── --}}
    <div class="filter-toolbar mb-3">
        <form action="{{ route('admin.anggota.index') }}" method="GET" id="formFilter">
            <div class="row g-2 align-items-center">

                {{-- Search --}}
                <div class="col-12 col-md-5">
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" value="{{ $search }}"
                            placeholder="Cari nama, nomor anggota, NIK, telepon...">
                    </div>
                </div>

                {{-- Filter Status --}}
                <div class="col-6 col-md-2">
                    <select name="status" class="form-select" onchange="document.getElementById('formFilter').submit()">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ $filterStatus === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="peringatan" {{ $filterStatus === 'peringatan' ? 'selected' : '' }}>Peringatan
                        </option>
                        <option value="tidak_aktif" {{ $filterStatus === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif
                        </option>
                    </select>
                </div>

                {{-- Filter Jenis Kelamin --}}
                <div class="col-6 col-md-2">
                    <select name="jk" class="form-select" onchange="document.getElementById('formFilter').submit()">
                        <option value="">Semua JK</option>
                        <option value="L" {{ $filterJk === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ $filterJk === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                {{-- Tombol --}}
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit"
                        class="btn-theme-primary flex-fill d-flex align-items-center justify-content-center">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    @if ($search || $filterStatus || $filterJk)
                        <a href="{{ route('admin.anggota.index') }}" class="btn-theme-light" title="Reset filter">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- ── Tabel ───────────────────────────────────────────────── --}}
    <div class="card">
        <div class="table-responsive" style="max-height:640px; overflow-y:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>
                            <a
                                href="{{ route('admin.anggota.index', array_merge(request()->query(), ['sort' => 'nomor_anggota', 'dir' => $sort === 'nomor_anggota' && $dir === 'asc' ? 'desc' : 'asc'])) }}">
                                No. Anggota
                                @if ($sort === 'nomor_anggota')
                                    <i class="bi bi-arrow-{{ $dir === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a
                                href="{{ route('admin.anggota.index', array_merge(request()->query(), ['sort' => 'nama', 'dir' => $sort === 'nama' && $dir === 'asc' ? 'desc' : 'asc'])) }}">
                                Nama
                                @if ($sort === 'nama')
                                    <i class="bi bi-arrow-{{ $dir === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>NIK</th>
                        <th>JK</th>
                        <th>Telepon</th>
                        <th>
                            <a
                                href="{{ route('admin.anggota.index', array_merge(request()->query(), ['sort' => 'tanggal_bergabung', 'dir' => $sort === 'tanggal_bergabung' && $dir === 'asc' ? 'desc' : 'asc'])) }}">
                                Bergabung
                                @if ($sort === 'tanggal_bergabung')
                                    <i class="bi bi-arrow-{{ $dir === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a
                                href="{{ route('admin.anggota.index', array_merge(request()->query(), ['sort' => 'status_anggota', 'dir' => $sort === 'status_anggota' && $dir === 'asc' ? 'desc' : 'asc'])) }}">
                                Status
                                @if ($sort === 'status_anggota')
                                    <i class="bi bi-arrow-{{ $dir === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($anggotaList as $anggota)
                        @php
                            $statusTint = match ($anggota->status_anggota) {
                                'aktif' => 'emerald',
                                'peringatan' => 'amber',
                                'tidak_aktif' => 'rose',
                                default => 'slate',
                            };
                            $statusIcon = match ($anggota->status_anggota) {
                                'aktif' => 'bi-check-circle-fill',
                                'peringatan' => 'bi-exclamation-circle-fill',
                                'tidak_aktif' => 'bi-x-circle-fill',
                                default => 'bi-circle',
                            };
                            $jkTint = $anggota->jenis_kelamin === 'L' ? 'blue' : 'pink';
                            $jkLabel = $anggota->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
                        @endphp
                        <tr>
                            <td>
                                <img src="{{ $anggota->akun->foto_url }}" alt="{{ $anggota->akun->nama }}"
                                    class="avatar-sm">
                            </td>
                            <td>
                                <span class="badge-soft tint-indigo">
                                    {{ $anggota->nomor_anggota }}
                                </span>
                            </td>
                            <td>
                                <div style="font-size:.83rem; font-weight:700;">
                                    {{ $anggota->akun->nama }}
                                </div>
                                <div style="font-size:.72rem; color:var(--text-muted);">
                                    {{ $anggota->akun->email }}
                                </div>
                            </td>
                            <td>
                                <span class="code-tag">{{ $anggota->nik }}</span>
                            </td>
                            <td>
                                <span class="badge-soft tint-{{ $jkTint }}">
                                    {{ $jkLabel }}
                                </span>
                            </td>
                            <td style="font-size:.82rem;">{{ $anggota->telepon }}</td>
                            <td>
                                <div style="font-size:.82rem; font-weight:500;">
                                    {{ $anggota->tanggal_bergabung->format('d/m/Y') }}
                                </div>
                                <div style="font-size:.7rem; color:var(--text-muted);">
                                    {{ $anggota->tanggal_bergabung->diffForHumans() }}
                                </div>
                            </td>
                            <td>
                                <span class="badge-soft tint-{{ $statusTint }}">
                                    <i class="bi {{ $statusIcon }}"></i>
                                    {{ $anggota->status_anggota_label }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.anggota.show', $anggota->id) }}" class="icon-action-btn btn-view"
                                    title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.anggota.edit', $anggota->id) }}" class="icon-action-btn btn-edit"
                                    title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="icon-action-btn btn-delete btn-hapus-anggota" title="Hapus"
                                    data-id="{{ $anggota->id }}" data-nama="{{ $anggota->akun->nama }}"
                                    data-nomor="{{ $anggota->nomor_anggota }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="theme-empty-state">
                                    <div class="empty-icon"><i class="bi bi-people"></i></div>
                                    <h6>Belum ada data anggota</h6>
                                    <p>
                                        @if ($search || $filterStatus || $filterJk)
                                            Tidak ditemukan anggota yang sesuai dengan pencarian atau filter Anda.
                                        @else
                                            Mulai tambahkan data anggota koperasi pertama.
                                        @endif
                                    </p>
                                    @if ($search || $filterStatus || $filterJk)
                                        <a href="{{ route('admin.anggota.index') }}" class="btn-theme-light">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                                        </a>
                                    @else
                                        <a href="{{ route('admin.anggota.create') }}" class="btn-theme-primary">
                                            <i class="bi bi-plus-lg me-1"></i> Tambah Anggota Pertama
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($anggotaList->hasPages())
            <div class="card-footer d-flex flex-wrap align-items-center justify-content-between gap-2 px-3 py-3">
                <span style="font-size:.78rem; color:var(--text-muted); font-weight:500;">
                    Menampilkan {{ $anggotaList->firstItem() }}–{{ $anggotaList->lastItem() }}
                    dari {{ $anggotaList->total() }} anggota
                </span>
                {{ $anggotaList->links() }}
            </div>
        @endif
    </div>

    {{-- Form hapus tersembunyi --}}
    <form id="formHapusAnggota" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    {{-- Loading overlay --}}
    <div class="theme-loading-overlay" id="loadingOverlay">
        <div class="theme-spinner"></div>
        <span>Memproses data...</span>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Konfirmasi hapus anggota
        document.querySelectorAll('.btn-hapus-anggota').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const nama = this.dataset.nama;
                const nomor = this.dataset.nomor;

                Swal.fire({
                    title: 'Hapus Data Anggota?',
                    html: `
                    <p style="color:var(--text-muted); font-size:.87rem;">
                        Anda akan menghapus data anggota:
                    </p>
                    <p style="font-weight:800; color:var(--text-dark); margin:.3rem 0;">
                        ${nama} <span style="color:var(--primary);">(${nomor})</span>
                    </p>
                    <div style="background:#fff8f0; border:1px solid #fde68a; border-radius:10px;
                                padding:.75rem 1rem; margin-top:.8rem; font-size:.81rem; color:#92400e; text-align:left;">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        <strong>Perhatian:</strong> Semua data usaha, laporan, dan evaluasi anggota ini
                        akan ikut terhapus secara permanen. Akun login tidak ikut terhapus.
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
                        const form = document.getElementById('formHapusAnggota');
                        form.action = `{{ url('admin/anggota') }}/${id}`;
                        form.submit();
                    }
                });
            });
        });

        // Loading saat filter
        document.getElementById('formFilter').addEventListener('submit', () => {
            document.getElementById('loadingOverlay').classList.add('show');
        });

        // Flash message SweetAlert
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
                    confirmButton: 'swal-theme-confirm',
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
                    confirmButton: 'swal-theme-deny',
                }
            });
        @endif
    </script>
@endpush
