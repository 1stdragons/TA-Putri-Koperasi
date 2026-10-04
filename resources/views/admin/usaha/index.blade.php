@extends('layouts.app')

@section('title', 'Data Usaha')
@section('page-title', 'Data Usaha')
@section('page-subtitle', 'Kelola seluruh data usaha anggota koperasi')

@section('sidebar-menu')
    @include('admin.partials._sidebar')
@endsection

@section('content')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div class="section-title mb-0">
            <i class="bi bi-shop-window"></i> Daftar Usaha Anggota
        </div>
        <a href="{{ route('admin.usaha.create') }}" class="btn-theme-primary">
            <i class="bi bi-plus-lg"></i> Tambah Usaha
        </a>
    </div>

    <div class="filter-toolbar mb-3">
        <form action="{{ route('admin.usaha.index') }}" method="GET" id="formFilter">
            <div class="row g-2">
                <div class="col-12 col-md-4">
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" value="{{ $search }}"
                            placeholder="Cari nama usaha, anggota, nomor anggota...">
                    </div>
                </div>

                <div class="col-6 col-md-2">
                    <select name="jenis_usaha" class="form-select"
                        onchange="document.getElementById('formFilter').submit()">
                        <option value="">Jenis Usaha</option>
                        @foreach (['perdagangan' => 'Perdagangan', 'jasa' => 'Jasa', 'produksi' => 'Produksi', 'pertanian' => 'Pertanian', 'lainnya' => 'Lainnya'] as $val => $label)
                            <option value="{{ $val }}" {{ $filterJenis === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <select name="jenis_akad" class="form-select" onchange="document.getElementById('formFilter').submit()">
                        <option value="">Jenis Akad</option>
                        @foreach (['Mudharabah', 'Musyarakah', 'Murabahah', 'Ijarah', 'Qardh', 'Lainnya'] as $akad)
                            <option value="{{ $akad }}" {{ $filterAkad === $akad ? 'selected' : '' }}>
                                {{ $akad }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <select name="status_usaha" class="form-select"
                        onchange="document.getElementById('formFilter').submit()">
                        <option value="">Status Usaha</option>
                        <option value="belum_aktif" {{ $filterStatus === 'belum_aktif' ? 'selected' : '' }}>
                            Belum Aktif
                        </option>
                        <option value="aktif" {{ $filterStatus === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="vakum" {{ $filterStatus === 'vakum' ? 'selected' : '' }}>Vakum</option>
                        <option value="tutup" {{ $filterStatus === 'tutup' ? 'selected' : '' }}>Tutup</option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <select name="status_syariah" class="form-select"
                        onchange="document.getElementById('formFilter').submit()">
                        <option value="">Status Syariah</option>
                        <option value="belum_diupload" {{ $filterSyariah === 'belum_diupload' ? 'selected' : '' }}>
                            Belum Upload
                        </option>
                        <option value="menunggu" {{ $filterSyariah === 'menunggu' ? 'selected' : '' }}>
                            Menunggu DPS
                        </option>
                        <option value="disetujui" {{ $filterSyariah === 'disetujui' ? 'selected' : '' }}>
                            Disetujui
                        </option>
                        <option value="perlu_revisi" {{ $filterSyariah === 'perlu_revisi' ? 'selected' : '' }}>
                            Perlu Revisi
                        </option>
                    </select>
                </div>

                <div class="col-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn-theme-primary d-flex align-items-center gap-1">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        @if ($search || $filterJenis || $filterAkad || $filterStatus || $filterSyariah)
                            <a href="{{ route('admin.usaha.index') }}" class="btn-theme-light">
                                <i class="bi bi-x-lg"></i> Reset
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="table-responsive" style="max-height:680px; overflow-y:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th style="min-width:180px;">Nama Usaha</th>
                        <th style="min-width:160px;">Anggota</th>
                        <th>Jenis Usaha</th>
                        <th>Jenis Akad</th>
                        <th>Status Usaha</th>
                        <th style="min-width:160px;">Status Syariah</th>
                        <th style="min-width:130px;">Modal Awal</th>
                        <th>Mulai</th>
                        <th class="text-center">Laporan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($usahaList as $usaha)
                        @php
                            $statusTidakKonsisten =
                                (in_array($usaha->status_usaha, ['aktif', 'vakum', 'tutup'], true) &&
                                    $usaha->status_validasi_syariah !== 'disetujui') ||
                                ($usaha->status_usaha === 'belum_aktif' &&
                                    $usaha->status_validasi_syariah === 'disetujui');
                        @endphp

                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div
                                        style="width:38px;height:38px;border-radius:10px;flex-shrink:0;
                                        background:linear-gradient(135deg,var(--primary-light),#ede9fe);
                                        display:flex;align-items:center;justify-content:center;
                                        font-size:1rem;color:var(--primary);">
                                        <i class="bi bi-shop-window"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-1">
                                            <div style="font-size:.83rem;font-weight:700;color:var(--text-dark);">
                                                {{ $usaha->nama_usaha }}
                                            </div>
                                            @if ($statusTidakKonsisten)
                                                <span title="Status usaha dan status syariah tidak konsisten"
                                                    style="color:#dc2626;font-size:.8rem;">
                                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                                </span>
                                            @endif
                                        </div>
                                        <div style="font-size:.72rem;color:var(--text-muted);">
                                            {{ $usaha->deskripsi ? \Illuminate\Support\Str::limit($usaha->deskripsi, 30) : '—' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $usaha->anggota->akun->foto_url }}" alt="" class="avatar-sm">
                                    <div>
                                        <div style="font-size:.82rem;font-weight:700;">
                                            {{ $usaha->anggota->akun->nama }}
                                        </div>
                                        <div style="font-size:.72rem;color:var(--text-muted);">
                                            {{ $usaha->anggota->nomor_anggota }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="badge-soft tint-{{ $usaha->jenis_usaha_tint }}">
                                    {{ $usaha->jenis_usaha_label }}
                                </span>
                            </td>

                            <td>
                                @if ($usaha->jenis_akad)
                                    <span class="badge-soft tint-{{ $usaha->jenis_akad_tint }}">
                                        {{ $usaha->jenis_akad_label }}
                                    </span>
                                @else
                                    <span style="color:var(--text-light);font-size:.78rem;">—</span>
                                @endif
                            </td>

                            <td>
                                <span class="badge-soft tint-{{ $usaha->status_usaha_tint }}">
                                    <i class="bi {{ $usaha->status_usaha_icon }} me-1"></i>
                                    {{ $usaha->status_usaha_label }}
                                </span>
                            </td>

                            <td>
                                <span class="badge-soft tint-{{ $usaha->status_validasi_syariah_tint }}">
                                    <i class="bi {{ $usaha->status_validasi_syariah_icon }} me-1"></i>
                                    {{ $usaha->status_validasi_syariah_label }}
                                </span>
                                @if ($statusTidakKonsisten)
                                    <div class="mt-1" style="font-size:.67rem;color:#b91c1c;line-height:1.35;">
                                        <i class="bi bi-exclamation-circle me-1"></i>
                                        Perlu pemeriksaan data
                                    </div>
                                @endif
                            </td>

                            <td>
                                <div style="font-size:.82rem;font-weight:700;color:var(--text-dark);">
                                    Rp {{ number_format($usaha->modal_awal, 0, ',', '.') }}
                                </div>
                            </td>

                            <td>
                                @if ($usaha->tanggal_mulai)
                                    <div style="font-size:.82rem;font-weight:500;">
                                        {{ $usaha->tanggal_mulai->format('d/m/Y') }}
                                    </div>
                                    <div style="font-size:.7rem;color:var(--text-muted);">
                                        {{ $usaha->tanggal_mulai->diffForHumans() }}
                                    </div>
                                @else
                                    <span style="color:var(--text-light);font-size:.78rem;">—</span>
                                @endif
                            </td>

                            <td class="text-center">
                                <span class="badge-soft tint-indigo"
                                    style="font-size:.8rem;min-width:36px;justify-content:center;">
                                    {{ $usaha->laporan_count ?? 0 }}
                                </span>
                            </td>

                            <td class="text-center">
                                <a href="{{ route('admin.usaha.show', $usaha->id) }}" class="icon-action-btn btn-view"
                                    title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.usaha.edit', $usaha->id) }}" class="icon-action-btn btn-edit"
                                    title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="icon-action-btn btn-delete btn-hapus-usaha" title="Hapus"
                                    data-id="{{ $usaha->id }}" data-nama="{{ $usaha->nama_usaha }}"
                                    data-anggota="{{ $usaha->anggota->akun->nama }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">
                                <div class="theme-empty-state">
                                    <div class="empty-icon"><i class="bi bi-shop"></i></div>
                                    <h6>Belum ada data usaha</h6>
                                    <p>
                                        @if ($search || $filterJenis || $filterAkad || $filterStatus || $filterSyariah)
                                            Tidak ditemukan usaha yang sesuai dengan pencarian atau filter Anda.
                                        @else
                                            Mulai tambahkan data usaha anggota koperasi.
                                        @endif
                                    </p>
                                    @if ($search || $filterJenis || $filterAkad || $filterStatus || $filterSyariah)
                                        <a href="{{ route('admin.usaha.index') }}" class="btn-theme-light">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                                        </a>
                                    @else
                                        <a href="{{ route('admin.usaha.create') }}" class="btn-theme-primary">
                                            <i class="bi bi-plus-lg me-1"></i> Tambah Usaha Pertama
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($usahaList->hasPages())
            <div class="card-footer d-flex flex-wrap align-items-center justify-content-between gap-2 px-3 py-3">
                <span style="font-size:.78rem;color:var(--text-muted);font-weight:500;">
                    Menampilkan {{ $usahaList->firstItem() }}–{{ $usahaList->lastItem() }}
                    dari {{ $usahaList->total() }} usaha
                </span>
                {{ $usahaList->links() }}
            </div>
        @endif
    </div>

    <form id="formHapusUsaha" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    <div class="theme-loading-overlay" id="loadingOverlay">
        <div class="theme-spinner"></div>
        <span>Memproses data...</span>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.btn-hapus-usaha').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const nama = this.dataset.nama;
                const anggota = this.dataset.anggota;

                Swal.fire({
                    title: 'Hapus Data Usaha?',
                    html: `
                        <p style="color:var(--text-muted);font-size:.87rem;margin-bottom:.5rem;">
                            Anda akan menghapus usaha:
                        </p>
                        <p style="font-weight:800;color:var(--text-dark);font-size:1rem;margin:.2rem 0;">
                            ${nama}
                        </p>
                        <p style="font-size:.82rem;color:var(--text-muted);margin-bottom:.8rem;">
                            milik anggota: <strong>${anggota}</strong>
                        </p>
                        <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;
                            padding:.75rem 1rem;font-size:.81rem;color:#92400e;text-align:left;">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            <strong>Perhatian:</strong> Seluruh laporan usaha, evaluasi, dan riwayat
                            validasi akad akan ikut terhapus secara permanen.
                            Data anggota dan akun login tidak ikut terhapus.
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
                        const form = document.getElementById('formHapusUsaha');
                        form.action = `{{ url('admin/usaha') }}/${id}`;
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
                text: @json(session('success')),
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
                text: @json(session('error')),
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
