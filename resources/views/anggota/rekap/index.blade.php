@extends('layouts.app')

@section('title', 'Rekap Laporan Saya')
@section('page-title', 'Rekap Laporan Saya')
@section('page-subtitle', 'Riwayat laporan monitoring dan hasil evaluasi usaha Anda')

@section('sidebar-menu')
    @include('anggota.partials._sidebar')
@endsection

@section('content')

    <div class="filter-toolbar mb-3">
        <form action="{{ route('anggota.rekap-laporan.index') }}" method="GET" id="formFilter">
            <div class="row g-2">
                <div class="col-12 col-md-5">
                    <div class="search-box"><i class="bi bi-search"></i>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama usaha...">
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ $filterStatus === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="dikirim" {{ $filterStatus === 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                        <option value="divalidasi" {{ $filterStatus === 'divalidasi' ? 'selected' : '' }}>Divalidasi</option>
                        <option value="ditolak" {{ $filterStatus === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
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
                <div class="col-6 col-md-2 d-flex gap-2">
                    <button type="submit"
                        class="btn-theme-primary flex-fill d-flex align-items-center justify-content-center"><i
                            class="bi bi-funnel"></i></button>
                    @if ($search || $filterStatus || $filterPeriode)
                        <a href="{{ route('anggota.rekap-laporan.index') }}" class="btn-theme-light"><i
                                class="bi bi-x-lg"></i></a>
                    @endif
                </div>
            </div>
            <div class="row g-2 mt-2">
                <div class="col-12 d-flex justify-content-end">
                    <a href="{{ route('anggota.rekap-laporan.pdf', request()->query()) }}" id="btnCetakPdf"
                        class="btn-theme-primary"
                        style="padding:.4rem 1rem; font-size:.8rem; background:linear-gradient(135deg,#dc2626,#ef4444);">
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i>Cetak PDF Rekap Saya
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
                        <th>Usaha</th>
                        <th>Periode</th>
                        <th>Omzet</th>
                        <th>Biaya</th>
                        <th>Laba</th>
                        <th>Status Laporan</th>
                        <th>Status Syariah</th>
                        <th>Hasil Evaluasi</th>
                        <th>Tgl Evaluasi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rekapList as $r)
                        <tr>
                            <td style="font-size:.82rem; font-weight:700;">{{ $r->nama_usaha }}</td>
                            <td><span class="badge-soft tint-indigo"
                                    style="font-size:.7rem;">Ke-{{ $r->periode_ke }}</span></td>
                            <td style="font-size:.8rem; font-weight:700; color:#047857;">Rp
                                {{ number_format($r->omzet_sekarang, 0, ',', '.') }}</td>
                            <td style="font-size:.8rem; color:var(--text-muted);">Rp
                                {{ number_format($r->biaya_operasional, 0, ',', '.') }}</td>
                            <td style="font-size:.8rem; font-weight:700;">Rp {{ number_format($r->laba_bersih, 0, ',', '.') }}
                            </td>
                            <td><span
                                    class="badge-soft tint-{{ $r->status_laporan_tint }}">{{ $r->status_laporan_label }}</span>
                            </td>
                            <td><span class="badge-soft tint-{{ $r->status_validasi_syariah_tint }}"
                                    style="font-size:.7rem;">{{ ucfirst(str_replace('_', ' ', $r->status_validasi_syariah)) }}</span>
                            </td>
                            <td>
                                @if ($r->hasil_evaluasi)
                                    <span
                                        class="badge-soft tint-{{ $r->hasil_evaluasi_tint }}">{{ $r->hasil_evaluasi_label }}</span>
                                @else
                                    <span style="font-size:.76rem; color:var(--text-light);">Belum ada</span>
                                @endif
                            </td>
                            <td style="font-size:.76rem; color:var(--text-muted);">
                                {{ $r->tanggal_evaluasi ? \Carbon\Carbon::parse($r->tanggal_evaluasi)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="text-center">
                                @if ($r->hasil_evaluasi)
                                    <a href="{{ route('anggota.rekap-laporan.detail-pdf', $r->id) }}"
                                        class="icon-action-btn btn-view btn-cetak-detail" title="Cetak PDF Detail"><i
                                            class="bi bi-file-earmark-pdf"></i></a>
                                @else
                                    <span style="color:var(--text-light); font-size:.78rem;">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">
                                <div class="theme-empty-state">
                                    <div class="empty-icon"><i class="bi bi-journal-x"></i></div>
                                    <h6>Belum ada laporan</h6>
                                    <p>Riwayat laporan Anda akan muncul di sini.</p>
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
