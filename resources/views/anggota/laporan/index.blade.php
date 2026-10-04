@extends('layouts.app')

@section('title', 'Riwayat Laporan')
@section('page-title', 'Riwayat Laporan Monitoring')
@section('page-subtitle', 'Seluruh laporan usaha 14 hari yang pernah Anda kirim')

@section('sidebar-menu')
    @include('anggota.partials._sidebar')
@endsection

@section('content')

    @php
        $punyaUsahaAktif = $punyaUsahaAktif ?? false;
    @endphp


    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div class="section-title mb-0">
            <i class="bi bi-journal-richtext"></i> Laporan Saya
        </div>
        @if ($punyaUsahaAktif)
            <a href="{{ route('anggota.laporan.create') }}" class="btn-theme-primary">
                <i class="bi bi-plus-lg"></i> Input Laporan Baru
            </a>
        @endif
    </div>

    <div class="filter-toolbar mb-3">
        <form action="{{ route('anggota.laporan.index') }}" method="GET" id="formFilter">
            <div class="row g-2">
                <div class="col-12 col-md-5">
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" value="{{ $search }}"
                            placeholder="Cari nama usaha atau periode...">
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <select name="status" class="form-select" onchange="document.getElementById('formFilter').submit()">
                        <option value="">Semua Status</option>
                        <option value="dikirim" {{ $filterStatus === 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                        <option value="divalidasi" {{ $filterStatus === 'divalidasi' ? 'selected' : '' }}>Divalidasi
                        </option>
                        <option value="ditolak" {{ $filterStatus === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="jenis" class="form-select" onchange="document.getElementById('formFilter').submit()">
                        <option value="">Jenis Usaha</option>
                        @foreach (['perdagangan' => 'Perdagangan', 'jasa' => 'Jasa', 'produksi' => 'Produksi', 'pertanian' => 'Pertanian', 'lainnya' => 'Lainnya'] as $v => $l)
                            <option value="{{ $v }}" {{ $filterJenis === $v ? 'selected' : '' }}>
                                {{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit"
                        class="btn-theme-primary flex-fill d-flex align-items-center justify-content-center">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    @if ($search || $filterStatus || $filterJenis)
                        <a href="{{ route('anggota.laporan.index') }}" class="btn-theme-light" title="Reset">
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
                        <th>Usaha</th>
                        <th>Periode</th>
                        <th>Omzet</th>
                        <th>Biaya</th>
                        <th>Laba Bersih</th>
                        <th>Status</th>
                        <th>Catatan Pengurus</th>
                        <th>Tanggal Kirim</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($laporanList as $laporan)
                        @php $labaPos = (float) $laporan->laba_bersih >= 0; @endphp
                        <tr
                            style="{{ $laporan->status_laporan === 'ditolak' ? 'background:linear-gradient(90deg,rgba(244,63,94,.04),transparent);' : '' }}">
                            <td>
                                <div style="font-size:.82rem; font-weight:700;">{{ $laporan->nama_usaha }}</div>
                                @php
                                    $jTint = match ($laporan->jenis_usaha ?? '') {
                                        'perdagangan' => 'cyan',
                                        'jasa' => 'violet',
                                        'produksi' => 'indigo',
                                        'pertanian' => 'emerald',
                                        default => 'slate',
                                    };
                                @endphp
                                <span class="badge-soft tint-{{ $jTint }}" style="font-size:.65rem;">
                                    {{ match ($laporan->jenis_usaha ?? '') {'perdagangan' => 'Perdagangan','jasa' => 'Jasa','produksi' => 'Produksi','pertanian' => 'Pertanian',default => 'Lainnya'} }}
                                </span>
                            </td>
                            <td>
                                <span class="badge-soft tint-indigo"
                                    style="font-size:.72rem;">Ke-{{ $laporan->periode_ke }}</span>
                                <div style="font-size:.7rem; color:var(--text-muted); margin-top:2px;">
                                    {{ $laporan->tanggal_mulai->format('d/m/Y') }} –
                                    {{ $laporan->tanggal_selesai->format('d/m/Y') }}
                                </div>
                            </td>
                            <td style="font-size:.82rem; font-weight:700;">Rp
                                {{ number_format($laporan->omzet, 0, ',', '.') }}</td>
                            <td style="font-size:.82rem; color:var(--text-muted);">Rp
                                {{ number_format($laporan->biaya_operasional, 0, ',', '.') }}</td>
                            <td>
                                <div
                                    style="font-size:.82rem; font-weight:700; color:{{ $labaPos ? '#047857' : '#be123c' }};">
                                    <i class="bi bi-arrow-{{ $labaPos ? 'up' : 'down' }}-circle-fill me-1"
                                        style="font-size:.78rem;"></i>
                                    Rp {{ number_format(abs($laporan->laba_bersih), 0, ',', '.') }}
                                </div>
                            </td>
                            <td>
                                <span class="badge-soft tint-{{ $laporan->status_laporan_tint }}">
                                    <i class="bi {{ $laporan->status_laporan_icon }} me-1"></i>
                                    {{ $laporan->status_laporan_label }}
                                </span>
                            </td>
                            <td>
                                @if ($laporan->catatan_pengurus)
                                    <div style="font-size:.76rem; color:var(--text-muted); max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"
                                        title="{{ $laporan->catatan_pengurus }}">
                                        {{ $laporan->catatan_pengurus }}
                                    </div>
                                @else
                                    <span style="color:var(--text-light); font-size:.76rem;">—</span>
                                @endif
                            </td>
                            <td>
                                <div style="font-size:.8rem;">{{ $laporan->created_at->format('d/m/Y') }}</div>
                                <div style="font-size:.7rem; color:var(--text-muted);">
                                    {{ $laporan->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('anggota.laporan.show', $laporan->id) }}"
                                    class="icon-action-btn btn-view" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if ($laporan->status_laporan === 'ditolak')
                                    <a href="{{ route('anggota.laporan.edit', $laporan->id) }}"
                                        class="icon-action-btn btn-edit" title="Revisi & Kirim Ulang">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="theme-empty-state">
                                    <div class="empty-icon"><i class="bi bi-journal-x"></i></div>
                                    <h6>Belum ada laporan</h6>
                                    <p>
                                        @if ($search || $filterStatus || $filterJenis)
                                            Tidak ditemukan laporan yang sesuai filter Anda.
                                        @else
                                            Anda belum pernah mengirim laporan monitoring.
                                        @endif
                                    </p>
                                    @if ($search || $filterStatus || $filterJenis)
                                        <a href="{{ route('anggota.laporan.index') }}" class="btn-theme-light">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                                        </a>
                                    @elseif ($punyaUsahaAktif)
                                        <a href="{{ route('anggota.laporan.create') }}" class="btn-theme-primary">
                                            <i class="bi bi-plus-lg me-1"></i> Input Laporan Pertama
                                        </a>
                                    @else
                                        <a href="{{ route('anggota.usaha-saya.index') }}" class="btn-theme-light">
                                            <i class="bi bi-shop-window me-1"></i> Lihat Usaha Saya
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

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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
