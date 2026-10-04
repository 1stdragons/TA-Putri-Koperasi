@extends('layouts.app')

@section('title', 'Manajemen Akun')
@section('page-title', 'Manajemen Akun')
@section('page-subtitle', 'Kelola seluruh akun pengguna sistem')

@section('sidebar-menu')
    @include('admin.partials._sidebar')
@endsection

@section('content')

    {{-- ── Header Aksi ─────────────────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div class="section-title mb-0"><i class="bi bi-people-fill"></i> Daftar Akun Pengguna</div>
        <a href="{{ route('admin.akun.create') }}" class="btn-theme-primary">
            <i class="bi bi-plus-lg me-1"></i> Tambah Akun
        </a>
    </div>

    {{-- ── Toolbar: Search & Filter ────────────────────────────── --}}
    <div class="akun-toolbar">
        <form action="{{ route('admin.akun.index') }}" method="GET" id="formFilter">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-5">
                    <div class="akun-search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" value="{{ $search }}"
                            placeholder="Cari nama atau email akun...">
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <select name="role" class="form-select akun-filter-select"
                        onchange="document.getElementById('formFilter').submit()">
                        <option value="">Semua Role</option>
                        <option value="admin" {{ $filterRole === 'admin' ? 'selected' : '' }}>Administrator</option>
                        <option value="anggota" {{ $filterRole === 'anggota' ? 'selected' : '' }}>Anggota</option>
                        <option value="pengurus" {{ $filterRole === 'pengurus' ? 'selected' : '' }}>Pengurus</option>
                        <option value="dps" {{ $filterRole === 'dps' ? 'selected' : '' }}>DPS</option>
                        <option value="ketua" {{ $filterRole === 'ketua' ? 'selected' : '' }}>Ketua</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="status" class="form-select akun-filter-select"
                        onchange="document.getElementById('formFilter').submit()">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ $filterStatus === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ $filterStatus === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit"
                        class="btn-theme-primary flex-fill text-center justify-content-center d-flex align-items-center">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    @if ($search || $filterRole || $filterStatus)
                        <a href="{{ route('admin.akun.index') }}" class="btn-theme-light" title="Reset filter">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- ── Tabel Akun ───────────────────────────────────────────── --}}
    <div class="card akun-table-card">
        <div class="akun-table-wrapper">
            <table class="table akun-table">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>
                            <a
                                href="{{ route('admin.akun.index', array_merge(request()->query(), ['sort' => 'nama', 'dir' => $sort === 'nama' && $dir === 'asc' ? 'desc' : 'asc'])) }}">
                                Nama
                                @if ($sort === 'nama')
                                    <i class="bi bi-arrow-{{ $dir === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a
                                href="{{ route('admin.akun.index', array_merge(request()->query(), ['sort' => 'email', 'dir' => $sort === 'email' && $dir === 'asc' ? 'desc' : 'asc'])) }}">
                                Email
                                @if ($sort === 'email')
                                    <i class="bi bi-arrow-{{ $dir === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a
                                href="{{ route('admin.akun.index', array_merge(request()->query(), ['sort' => 'role', 'dir' => $sort === 'role' && $dir === 'asc' ? 'desc' : 'asc'])) }}">
                                Role
                                @if ($sort === 'role')
                                    <i class="bi bi-arrow-{{ $dir === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a
                                href="{{ route('admin.akun.index', array_merge(request()->query(), ['sort' => 'status', 'dir' => $sort === 'status' && $dir === 'asc' ? 'desc' : 'asc'])) }}">
                                Status
                                @if ($sort === 'status')
                                    <i class="bi bi-arrow-{{ $dir === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a
                                href="{{ route('admin.akun.index', array_merge(request()->query(), ['sort' => 'last_login', 'dir' => $sort === 'last_login' && $dir === 'asc' ? 'desc' : 'asc'])) }}">
                                Last Login
                                @if ($sort === 'last_login')
                                    <i class="bi bi-arrow-{{ $dir === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($akunList as $akun)
                        @php
                            $roleTint = match ($akun->role) {
                                'admin' => 'indigo',
                                'anggota' => 'emerald',
                                'pengurus' => 'amber',
                                'dps' => 'cyan',
                                'ketua' => 'pink',
                                default => 'slate',
                            };
                        @endphp
                        <tr>
                            <td>
                                <img src="{{ $akun->foto_url }}" alt="{{ $akun->nama }}" class="akun-avatar">
                            </td>
                            <td>
                                <div class="fw-bold" style="font-size:.84rem;">{{ $akun->nama }}</div>
                            </td>
                            <td>
                                <div class="text-muted" style="font-size:.82rem;">{{ $akun->email }}</div>
                            </td>
                            <td>
                                <span class="badge-soft tint-{{ $roleTint }}">{{ $akun->role_label }}</span>
                            </td>
                            <td>
                                @if ($akun->status === 'aktif')
                                    <span class="badge-soft tint-emerald">
                                        <i class="bi bi-check-circle-fill me-1"></i> Aktif
                                    </span>
                                @else
                                    <span class="badge-soft tint-rose">
                                        <i class="bi bi-x-circle-fill me-1"></i> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if ($akun->last_login)
                                    <div style="font-size:.8rem; font-weight:500;">
                                        {{ $akun->last_login->format('d/m/Y H:i') }}</div>
                                    <div class="text-muted" style="font-size:.7rem;">
                                        {{ $akun->last_login->diffForHumans() }}</div>
                                @else
                                    <span class="text-muted" style="font-size:.78rem;">Belum pernah login</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.akun.show', $akun->id) }}" class="icon-action-btn btn-view"
                                    title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.akun.edit', $akun->id) }}" class="icon-action-btn btn-edit"
                                    title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if ($akun->id !== Auth::id())
                                    <button type="button" class="icon-action-btn btn-delete btn-hapus-akun" title="Hapus"
                                        data-id="{{ $akun->id }}" data-nama="{{ $akun->nama }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="theme-empty-state">
                                    <div class="empty-icon"><i class="bi bi-inbox"></i></div>
                                    <h6>Belum ada data akun</h6>
                                    <p>
                                        @if ($search || $filterRole || $filterStatus)
                                            Tidak ditemukan akun yang sesuai dengan pencarian/filter Anda.
                                        @else
                                            Mulai tambahkan akun pengguna pertama untuk sistem.
                                        @endif
                                    </p>
                                    @if ($search || $filterRole || $filterStatus)
                                        <a href="{{ route('admin.akun.index') }}" class="btn-theme-light">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                                        </a>
                                    @else
                                        <a href="{{ route('admin.akun.create') }}" class="btn-theme-primary">
                                            <i class="bi bi-plus-lg me-1"></i> Tambah Akun Pertama
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ── Pagination ───────────────────────────────────────── --}}
        @if ($akunList->hasPages())
            <div
                class="card-footer bg-transparent d-flex flex-wrap align-items-center justify-content-between gap-2 px-3 py-3">
                <span class="text-muted" style="font-size:.78rem;">
                    Menampilkan {{ $akunList->firstItem() }}–{{ $akunList->lastItem() }} dari {{ $akunList->total() }}
                    akun
                </span>
                {{ $akunList->links() }}
            </div>
        @endif
    </div>

    {{-- ── Form Hapus Tersembunyi ──────────────────────────────── --}}
    <form id="formHapusAkun" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    {{-- ── Loading Overlay ──────────────────────────────────────── --}}
    <div class="theme-loading-overlay" id="loadingOverlay">
        <div class="theme-spinner"></div>
        <span>Memproses data...</span>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.btn-hapus-akun').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const nama = this.dataset.nama;

                Swal.fire({
                    title: 'Hapus Akun?',
                    html: `Anda yakin ingin menghapus akun <strong>${nama}</strong>?<br><span style="font-size:.8rem; color:#94a3b8;">Tindakan ini tidak dapat dibatalkan.</span>`,
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
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('loadingOverlay').classList.add('show');
                        const form = document.getElementById('formHapusAkun');
                        form.action = `{{ url('admin/akun') }}/${id}`;
                        form.submit();
                    }
                });
            });
        });

        document.getElementById('formFilter').addEventListener('submit', function() {
            document.getElementById('loadingOverlay').classList.add('show');
        });

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 3000,
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
