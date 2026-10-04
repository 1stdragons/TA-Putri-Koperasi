@extends('layouts.app')

@section('title', 'Validasi Akad Syariah')
@section('page-title', 'Validasi Akad Syariah')
@section('page-subtitle', 'Periksa kesesuaian akad usaha anggota dengan prinsip syariah')

@section('sidebar-menu')
    @include('dps.partials._sidebar')
@endsection

@section('content')

    {{-- ── Header ──────────────────────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div class="section-title mb-0">
            <i class="bi bi-shield-check"></i> Daftar Usaha Perlu Validasi Syariah
        </div>
        @if ($totalMenunggu > 0)
            <span class="badge-soft tint-amber" style="font-size:.82rem; padding:.4rem 1rem;">
                <i class="bi bi-hourglass-split me-1"></i>
                {{ $totalMenunggu }} usaha menunggu validasi
            </span>
        @endif
    </div>

    {{-- ── Info Box ─────────────────────────────────────────────── --}}
    <div class="mb-3 p-3 d-flex align-items-start gap-2"
        style="background:linear-gradient(135deg,#fffbeb,#fef3c7); border-radius:var(--radius-md);
            border-left:4px solid var(--warning);">
        <i class="bi bi-info-circle-fill" style="color:#b45309; font-size:1rem; flex-shrink:0; margin-top:2px;"></i>
        <div style="font-size:.82rem; color:#92400e; line-height:1.6;">
            <strong>Catatan DPS:</strong> Halaman ini menampilkan usaha yang dokumen akadnya perlu diperiksa.
            Monitoring 14 hari <strong>belum dapat dimulai</strong> sebelum akad disetujui DPS.
            Ketika DPS menyetujui akad, sistem otomatis mengaktifkan usaha dan menetapkan tanggal aktivasi monitoring.
        </div>
    </div>

    {{-- ── Toolbar Filter ──────────────────────────────────────── --}}
    <div class="filter-toolbar mb-3">
        <form action="{{ route('dps.validasi-syariah.index') }}" method="GET" id="formFilter">
            <div class="row g-2">

                <div class="col-12 col-md-5">
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" value="{{ $search }}"
                            placeholder="Cari nama usaha, nama anggota, nomor anggota...">
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <select name="jenis_akad" class="form-select" onchange="document.getElementById('formFilter').submit()">
                        <option value="">Semua Jenis Akad</option>
                        @foreach (['Mudharabah', 'Musyarakah', 'Murabahah', 'Ijarah', 'Qardh', 'Lainnya'] as $akad)
                            <option value="{{ $akad }}" {{ $filterAkad === $akad ? 'selected' : '' }}>
                                {{ $akad }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <select name="status_syariah" class="form-select"
                        onchange="document.getElementById('formFilter').submit()">
                        <option value="">Semua Status</option>
                        <option value="menunggu" {{ $filterSyariah === 'menunggu' ? 'selected' : '' }}>Menunggu Validasi
                        </option>
                    </select>
                </div>

                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit"
                        class="btn-theme-primary flex-fill d-flex align-items-center justify-content-center">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    @if ($search || $filterAkad || $filterSyariah)
                        <a href="{{ route('dps.validasi-syariah.index') }}" class="btn-theme-light" title="Reset">
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
                        <th>#</th>
                        <th>Anggota</th>
                        <th>
                            <a
                                href="{{ route('dps.validasi-syariah.index', array_merge(request()->query(), ['sort' => 'nama_usaha', 'dir' => $sort === 'nama_usaha' && $dir === 'asc' ? 'desc' : 'asc'])) }}">
                                Nama Usaha
                                @if ($sort === 'nama_usaha')
                                    <i class="bi bi-arrow-{{ $dir === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>Jenis Akad</th>
                        <th>Dokumen Akad</th>
                        <th>Status Usaha</th>
                        <th>Aktivasi Monitoring</th>
                        <th>Iterasi Validasi</th>
                        <th>Status Syariah</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($usahaList as $i => $usaha)
                        <tr
                            style="{{ $usaha->status_validasi_syariah === 'menunggu' ? 'background:linear-gradient(90deg,rgba(99,102,241,.04),transparent);' : '' }}">

                            {{-- No --}}
                            <td style="color:var(--text-light); font-size:.78rem;">
                                {{ ($usahaList->currentPage() - 1) * $usahaList->perPage() + $i + 1 }}
                            </td>

                            {{-- Anggota --}}
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $usaha->anggota->akun->foto_url }}" alt="" class="avatar-sm">
                                    <div>
                                        <div style="font-size:.82rem; font-weight:700;">{{ $usaha->anggota->akun->nama }}
                                        </div>
                                        <div style="font-size:.72rem; color:var(--text-muted);">
                                            {{ $usaha->anggota->nomor_anggota }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Nama Usaha --}}
                            <td>
                                <div style="font-size:.82rem; font-weight:700;">{{ $usaha->nama_usaha }}</div>
                                <span class="badge-soft tint-{{ $usaha->jenis_usaha_tint }}" style="font-size:.68rem;">
                                    {{ $usaha->jenis_usaha_label }}
                                </span>
                            </td>

                            {{-- Jenis Akad --}}
                            <td>
                                @if ($usaha->jenis_akad)
                                    <span class="badge-soft tint-{{ $usaha->jenis_akad_tint }}">
                                        {{ $usaha->jenis_akad }}
                                    </span>
                                @else
                                    <span style="color:var(--text-light); font-size:.78rem;">Belum ditentukan</span>
                                @endif
                            </td>

                            {{-- Dokumen Akad --}}
                            <td>
                                @if ($usaha->has_file_akad)
                                    <a href="{{ $usaha->file_akad_url }}" target="_blank"
                                        style="font-size:.78rem; color:var(--primary); font-weight:600;
                                          text-decoration:none; display:inline-flex; align-items:center; gap:.3rem;">
                                        <i class="bi {{ $usaha->file_akad_icon }}"></i>
                                        Lihat Dokumen
                                    </a>
                                @else
                                    <span class="badge-soft tint-slate" style="font-size:.7rem;">
                                        <i class="bi bi-x-circle me-1"></i>Belum Ada
                                    </span>
                                @endif
                            </td>
                            {{-- Status Usaha --}}
                            <td>
                                <span class="badge-soft tint-{{ $usaha->status_usaha_tint }}">
                                    <i class="bi {{ $usaha->status_usaha_icon }} me-1"></i>
                                    {{ $usaha->status_usaha_label }}
                                </span>
                            </td>

                            {{-- Tanggal Aktivasi Monitoring --}}
                            <td>
                                @if ($usaha->tanggal_aktivasi_monitoring)
                                    <div style="font-size:.78rem; font-weight:700; color:#047857;">
                                        {{ $usaha->tanggal_aktivasi_monitoring->translatedFormat('d F Y') }}
                                    </div>
                                    <div style="font-size:.68rem; color:var(--text-muted); margin-top:2px;">
                                        Monitoring telah dimulai
                                    </div>
                                @else
                                    <span class="badge-soft tint-slate" style="font-size:.72rem;">
                                        <i class="bi bi-clock-history me-1"></i>Belum Dimulai
                                    </span>
                                @endif
                            </td>

                            {{-- Iterasi Validasi DPS --}}
                            <td class="text-center">
                                @if ($usaha->validasiAkadTerakhir)
                                    <span class="badge-soft tint-indigo" style="font-size:.78rem;">
                                        Iterasi ke-{{ $usaha->validasiAkadTerakhir->iterasi }}
                                    </span>
                                    <div style="font-size:.68rem; color:var(--text-muted); margin-top:3px;">
                                        {{ $usaha->validasiAkadTerakhir->created_at->format('d/m/Y') }}
                                    </div>
                                @else
                                    <span style="font-size:.76rem; color:var(--text-light);">Pertama kali</span>
                                @endif
                            </td>

                            {{-- Status Validasi Syariah --}}
                            <td>
                                <span class="badge-soft tint-{{ $usaha->status_validasi_syariah_tint }}">
                                    <i class="bi {{ $usaha->status_validasi_syariah_icon }} me-1"></i>
                                    {{ $usaha->status_validasi_syariah_label }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="text-center">
                                <a href="{{ route('dps.validasi-syariah.show', $usaha->id) }}"
                                    class="icon-action-btn btn-view" title="Periksa &amp; Validasi">
                                    <i class="bi bi-shield-check"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">
                                <div class="theme-empty-state">
                                    <div class="empty-icon"><i class="bi bi-shield-x"></i></div>
                                    <h6>Tidak ada usaha yang perlu divalidasi</h6>
                                    <p>
                                        @if ($search || $filterAkad || $filterSyariah)
                                            Tidak ditemukan usaha yang sesuai dengan filter Anda.
                                        @else
                                            Semua akad yang diajukan sudah diproses atau belum ada dokumen akad yang
                                            menunggu validasi.
                                        @endif
                                    </p>
                                    @if ($search || $filterAkad || $filterSyariah)
                                        <a href="{{ route('dps.validasi-syariah.index') }}" class="btn-theme-light">
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

        @if ($usahaList->hasPages())
            <div class="card-footer d-flex flex-wrap align-items-center justify-content-between gap-2 px-3 py-3">
                <span style="font-size:.78rem; color:var(--text-muted); font-weight:500;">
                    Menampilkan {{ $usahaList->firstItem() }}–{{ $usahaList->lastItem() }}
                    dari {{ $usahaList->total() }} usaha
                </span>
                {{ $usahaList->links() }}
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
        document.getElementById('formFilter').addEventListener('submit', () => {
            document.getElementById('loadingOverlay').classList.add('show');
        });

        @if (session('success'))
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
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Perhatian!',
                text: @json(session('error')),
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
