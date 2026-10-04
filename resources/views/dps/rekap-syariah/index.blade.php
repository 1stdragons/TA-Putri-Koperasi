@extends('layouts.app')

@section('title', 'Rekap Validasi Syariah')
@section('page-title', 'Rekap Validasi Syariah')
@section('page-subtitle', 'Ringkasan validasi akad seluruh usaha anggota')

@section('sidebar-menu')
    @include('dps.partials._sidebar')
@endsection

@section('content')

    <div class="filter-toolbar mb-3">
        <form action="{{ route('dps.rekap-syariah.index') }}" method="GET" id="formFilter">
            <div class="row g-2">
                <div class="col-12 col-md-4">
                    <div class="search-box"><i class="bi bi-search"></i>
                        <input type="text" name="search" value="{{ $search }}"
                            placeholder="Cari nama usaha atau anggota...">
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <select name="jenis_akad" class="form-select">
                        <option value="">Jenis Akad</option>
                        @foreach (['Mudharabah', 'Musyarakah', 'Murabahah', 'Ijarah', 'Qardh', 'Lainnya'] as $akad)
                            <option value="{{ $akad }}" {{ $filterAkad === $akad ? 'selected' : '' }}>{{ $akad }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="status_syariah" class="form-select">
                        <option value="">Status Syariah</option>
                        <option value="belum_diupload" {{ $filterStatus === 'belum_diupload' ? 'selected' : '' }}>Belum Diupload
                        </option>
                        <option value="menunggu" {{ $filterStatus === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="disetujui" {{ $filterStatus === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="perlu_revisi" {{ $filterStatus === 'perlu_revisi' ? 'selected' : '' }}>Perlu Revisi
                        </option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <input type="date" name="tgl_dari" class="form-control" value="{{ $filterTglDari }}"
                        title="Tanggal validasi dari">
                </div>
                <div class="col-6 col-md-2">
                    <input type="date" name="tgl_sampai" class="form-control" value="{{ $filterTglSampai }}"
                        title="Tanggal validasi sampai">
                </div>
            </div>
            <div class="row g-2 mt-2">
                <div class="col-12 d-flex gap-2 justify-content-end">
                    @if ($search || $filterAkad || $filterStatus || $filterTglDari || $filterTglSampai)
                        <a href="{{ route('dps.rekap-syariah.index') }}" class="btn-theme-light"
                            style="padding:.4rem .9rem; font-size:.8rem;"><i class="bi bi-x-lg me-1"></i>Reset</a>
                    @endif
                    <button type="submit" class="btn-theme-primary" style="padding:.4rem 1rem; font-size:.8rem;"><i
                            class="bi bi-funnel me-1"></i>Terapkan Filter</button>
                    <a href="{{ route('dps.rekap-syariah.pdf', request()->query()) }}" id="btnCetakPdf"
                        class="btn-theme-primary"
                        style="padding:.4rem 1rem; font-size:.8rem; background:linear-gradient(135deg,#dc2626,#ef4444);">
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i>Cetak PDF Rekap
                    </a>
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
                        <th>Jenis Akad</th>
                        <th>File Akad</th>
                        <th>Status Syariah</th>
                        <th>DPS Terakhir</th>
                        <th>Tanggal Validasi</th>
                        <th>Iterasi</th>
                        <th>Catatan Terakhir</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rekapList as $r)
                        <tr>
                            <td>
                                <div style="font-size:.82rem; font-weight:700;">{{ $r->nama_anggota }}</div><span
                                    class="code-tag" style="font-size:.66rem;">{{ $r->nomor_anggota }}</span>
                            </td>
                            <td style="font-size:.82rem; font-weight:700;">{{ $r->nama_usaha }}</td>
                            <td>
                                @if ($r->jenis_akad)
                                    <span class="badge-soft tint-violet"
                                    style="font-size:.7rem;">{{ $r->jenis_akad }}</span>@else—
                                @endif
                            </td>
                            <td>
                                @if ($r->has_file_akad)
                                    <span style="font-size:.76rem; color:var(--primary);"><i
                                        class="bi {{ $r->file_akad_icon }} me-1"></i>Ada</span>@else<span
                                        style="font-size:.76rem; color:var(--text-light);">Belum ada</span>
                                @endif
                            </td>
                            <td><span
                                    class="badge-soft tint-{{ $r->status_validasi_syariah_tint }}">{{ $r->status_validasi_syariah_label }}</span>
                            </td>
                            <td style="font-size:.8rem;">{{ $r->validasiAkadTerakhir?->dps?->nama ?? '-' }}</td>
                            <td style="font-size:.78rem; color:var(--text-muted);">
                                {{ $r->validasiAkadTerakhir?->divalidasi_pada?->format('d/m/Y') ?? '-' }}</td>
                            <td class="text-center"><span class="badge-soft tint-indigo"
                                    style="font-size:.72rem;">{{ $r->validasi_akad_count }}x</span></td>
                            <td style="font-size:.76rem; color:var(--text-muted); max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"
                                title="{{ $r->validasiAkadTerakhir?->catatan_dps }}">
                                {{ $r->validasiAkadTerakhir?->catatan_dps ?? '-' }}
                            </td>
                            <td class="text-center">
                                <a href="{{ route('dps.rekap-syariah.detail-pdf', $r->id) }}"
                                    class="icon-action-btn btn-view btn-cetak-detail" title="Cetak PDF Detail"><i
                                        class="bi bi-file-earmark-pdf"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">
                                <div class="theme-empty-state">
                                    <div class="empty-icon"><i class="bi bi-shield-x"></i></div>
                                    <h6>Belum ada data</h6>
                                    <p>Belum ada usaha dengan jenis akad terdaftar.</p>
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
        <div class="theme-spinner"></div><span>Memproses...</span>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('formFilter').addEventListener('submit', () => document.getElementById('loadingOverlay')
            .classList.add('show'));
        document.getElementById('btnCetakPdf').addEventListener('click', () => {
            document.getElementById('loadingOverlay').classList.add('show');
            setTimeout(() => document.getElementById('loadingOverlay').classList.remove('show'), 4000);
        });
        document.querySelectorAll('.btn-cetak-detail').forEach(btn => btn.addEventListener('click', () => {
            document.getElementById('loadingOverlay').classList.add('show');
            setTimeout(() => document.getElementById('loadingOverlay').classList.remove('show'), 3000);
        }));
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
