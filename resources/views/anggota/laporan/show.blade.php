@extends('layouts.app')

@section('title', 'Detail Laporan')
@section('page-title', 'Detail Laporan')
@section('page-subtitle', $laporan->periode_label)

@section('sidebar-menu')
    @include('anggota.partials._sidebar')
@endsection

@section('content')

    <div class="d-flex gap-2 mb-3 flex-wrap">
        <a href="{{ route('anggota.laporan.index') }}" class="btn-theme-light" style="padding:.4rem .9rem; font-size:.83rem;">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        @if (in_array($laporan->status_laporan, ['draft', 'dikirim', 'ditolak']))
            <a href="{{ route('anggota.laporan.edit', $laporan->id) }}" class="btn-theme-primary"
                style="padding:.4rem .9rem; font-size:.83rem;">
                <i class="bi bi-{{ $laporan->status_laporan === 'ditolak' ? 'arrow-repeat' : 'pencil-square' }} me-1"></i>
                {{ $laporan->status_laporan === 'ditolak' ? 'Revisi & Kirim Ulang' : 'Edit Laporan' }}
            </a>
        @endif
    </div>

    {{-- Banner ditolak --}}
    @if ($laporan->status_laporan === 'ditolak')
        <div class="mb-3 p-3 d-flex align-items-start gap-2"
            style="background:linear-gradient(135deg,#fff1f2,#ffe4e6);
                border-radius:var(--radius-md); border-left:4px solid #f43f5e;">
            <i class="bi bi-exclamation-circle-fill"
                style="color:#be123c; font-size:1rem; flex-shrink:0; margin-top:2px;"></i>
            <div>
                <div style="font-size:.84rem; font-weight:700; color:#be123c;">
                    Laporan Ini Ditolak oleh Pengurus
                </div>
                @if ($laporan->catatan_pengurus)
                    <div style="font-size:.8rem; color:#9f1239; margin-top:.25rem; line-height:1.5;">
                        <strong>Alasan:</strong> {{ $laporan->catatan_pengurus }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div class="row g-3">

        {{-- ── Kolom Kiri ──────────────────────────────────────────── --}}
        <div class="col-12 col-lg-4">

            {{-- Status & Periode --}}
            <div class="card mb-3" style="overflow:hidden;">
                <div
                    style="background:linear-gradient(120deg, var(--primary), #818cf8 60%, var(--secondary) 130%);
                        padding:1.5rem; text-align:center; position:relative; overflow:hidden;">
                    <div
                        style="position:absolute; width:100px; height:100px; background:rgba(255,255,255,.1);
                            border-radius:50%; top:-40px; right:-30px;">
                    </div>
                    <div style="font-size:2rem; font-weight:900; color:#fff; position:relative; z-index:1;">
                        {{ $laporan->periode_ke }}</div>
                    <div
                        style="font-size:.76rem; color:rgba(255,255,255,.85); margin:.1rem 0 .6rem; position:relative; z-index:1;">
                        Periode ke-{{ $laporan->periode_ke }}</div>
                    <div
                        style="font-size:.76rem; color:rgba(255,255,255,.85); margin-bottom:.7rem; position:relative; z-index:1;">
                        {{ $laporan->tanggal_mulai->format('d/m/Y') }} — {{ $laporan->tanggal_selesai->format('d/m/Y') }}
                    </div>
                    <span class="badge-soft tint-{{ $laporan->status_laporan_tint }}"
                        style="background:rgba(255,255,255,.2) !important; color:#fff !important;
                             border:1px solid rgba(255,255,255,.3); position:relative; z-index:1;">
                        <i class="bi {{ $laporan->status_laporan_icon }} me-1"></i>
                        {{ $laporan->status_laporan_label }}
                    </span>
                </div>
            </div>

            {{-- Ringkasan Keuangan --}}
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-cash-stack me-2" style="color:var(--warning);"></i>Ringkasan Keuangan
                </div>
                <div class="card-body d-flex flex-column gap-2">
                    <div
                        style="background:linear-gradient(135deg,#ecfdf5,#d1fae5); border-radius:var(--radius-sm); padding:.85rem;">
                        <div
                            style="font-size:.7rem; font-weight:700; color:#047857; text-transform:uppercase; margin-bottom:.25rem;">
                            Omzet</div>
                        <div style="font-size:1.05rem; font-weight:800; color:#047857;">Rp
                            {{ number_format($laporan->omzet, 0, ',', '.') }}</div>
                    </div>
                    <div
                        style="background:linear-gradient(135deg,#fff1f2,#ffe4e6); border-radius:var(--radius-sm); padding:.85rem;">
                        <div
                            style="font-size:.7rem; font-weight:700; color:#be123c; text-transform:uppercase; margin-bottom:.25rem;">
                            Biaya Operasional</div>
                        <div style="font-size:1.05rem; font-weight:800; color:#be123c;">Rp
                            {{ number_format($laporan->biaya_operasional, 0, ',', '.') }}</div>
                    </div>
                    @php $labaPos = (float) $laporan->laba_bersih >= 0; @endphp
                    <div
                        style="background:{{ $labaPos ? 'linear-gradient(135deg,#eef2ff,#e0e7ff)' : 'linear-gradient(135deg,#fff1f2,#ffe4e6)' }};
                            border-radius:var(--radius-sm); padding:.85rem;
                            border:2px solid {{ $labaPos ? '#c7d2fe' : '#fecaca' }};">
                        <div
                            style="font-size:.7rem; font-weight:700; color:{{ $labaPos ? '#4338ca' : '#be123c' }}; text-transform:uppercase; margin-bottom:.25rem;">
                            Laba Bersih</div>
                        <div style="font-size:1.05rem; font-weight:800; color:{{ $labaPos ? '#4338ca' : '#be123c' }};">
                            <i class="bi bi-arrow-{{ $labaPos ? 'up' : 'down' }}-circle-fill me-1"
                                style="font-size:.9rem;"></i>
                            Rp {{ number_format(abs($laporan->laba_bersih), 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Catatan Pengurus --}}
            @if ($laporan->catatan_pengurus)
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="bi bi-chat-left-text-fill me-2" style="color:var(--secondary);"></i>Catatan Pengurus
                    </div>
                    <div class="card-body">
                        <p style="font-size:.85rem; color:var(--text-dark); line-height:1.6; margin:0;">
                            {{ $laporan->catatan_pengurus }}
                        </p>
                    </div>
                </div>
            @endif

            {{-- Hasil Evaluasi --}}
            @if ($laporan->validasi)
                @php $v = $laporan->validasi; @endphp
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-clipboard2-check-fill me-2" style="color:var(--success);"></i>Hasil Evaluasi
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <span class="badge-soft tint-{{ $v->hasil_evaluasi_tint }}"
                                style="font-size:.88rem; padding:.45rem 1.2rem;">
                                {{ $v->hasil_evaluasi_label }}
                            </span>
                        </div>
                        <div class="row g-2 text-center">
                            <div class="col-6">
                                <div style="background:var(--bg-soft); border-radius:var(--radius-sm); padding:.65rem;">
                                    <div
                                        style="font-size:.68rem; color:var(--text-muted); font-weight:600; margin-bottom:.2rem;">
                                        Kenaikan Omzet</div>
                                    <div
                                        style="font-size:.88rem; font-weight:800; color:{{ $v->indikator_omzet ? '#047857' : '#be123c' }};">
                                        {{ $v->persentase_kenaikan_omzet }}%
                                        <i class="bi bi-{{ $v->indikator_omzet ? 'check' : 'x' }}-circle-fill ms-1"
                                            style="font-size:.8rem;"></i>
                                    </div>
                                    <div style="font-size:.68rem; color:var(--text-muted);">Target ≥ 5%</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div style="background:var(--bg-soft); border-radius:var(--radius-sm); padding:.65rem;">
                                    <div
                                        style="font-size:.68rem; color:var(--text-muted); font-weight:600; margin-bottom:.2rem;">
                                        Rasio Biaya</div>
                                    <div
                                        style="font-size:.88rem; font-weight:800; color:{{ $v->indikator_biaya ? '#047857' : '#be123c' }};">
                                        {{ $v->rasio_biaya }}%
                                        <i class="bi bi-{{ $v->indikator_biaya ? 'check' : 'x' }}-circle-fill ms-1"
                                            style="font-size:.8rem;"></i>
                                    </div>
                                    <div style="font-size:.68rem; color:var(--text-muted);">Target &lt; 60%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- ── Kolom Kanan ─────────────────────────────────────────── --}}
        <div class="col-12 col-lg-8">

            {{-- Informasi Usaha --}}
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-shop-window me-2" style="color:var(--primary);"></i>Informasi Usaha
                </div>
                <div class="card-body p-0">
                    @php $usaha = $laporan->usaha; @endphp
                    <div class="akun-info-row px-4">
                        <div class="akun-info-icon tint-indigo"><i class="bi bi-shop-window"></i></div>
                        <div>
                            <div class="akun-info-label">Nama Usaha</div>
                            <div class="akun-info-value">{{ $usaha->nama_usaha }}</div>
                        </div>
                    </div>
                    <div class="akun-info-row px-4">
                        <div class="akun-info-icon tint-cyan"><i class="bi bi-grid-3x3-gap-fill"></i></div>
                        <div>
                            <div class="akun-info-label">Jenis Usaha</div>
                            <div class="akun-info-value">
                                <span
                                    class="badge-soft tint-{{ $usaha->jenis_usaha_tint }}">{{ $usaha->jenis_usaha_label }}</span>
                            </div>
                        </div>
                    </div>
                    @if ($usaha->jenis_akad)
                        <div class="akun-info-row px-4">
                            <div class="akun-info-icon tint-violet"><i class="bi bi-patch-check-fill"></i></div>
                            <div>
                                <div class="akun-info-label">Jenis Akad</div>
                                <div class="akun-info-value">
                                    <span
                                        class="badge-soft tint-{{ $usaha->jenis_akad_tint }}">{{ $usaha->jenis_akad }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="akun-info-row px-4" style="border-bottom:none;">
                        <div class="akun-info-icon tint-{{ $usaha->status_validasi_syariah_tint }}">
                            <i class="bi {{ $usaha->status_validasi_syariah_icon }}"></i>
                        </div>
                        <div>
                            <div class="akun-info-label">Status Validasi Syariah</div>
                            <div class="akun-info-value">
                                <span class="badge-soft tint-{{ $usaha->status_validasi_syariah_tint }}">
                                    {{ $usaha->status_validasi_syariah_label }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Catatan Anggota --}}
            @if ($laporan->keterangan)
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="bi bi-chat-left-text-fill me-2" style="color:var(--secondary);"></i>Catatan Anda
                    </div>
                    <div class="card-body">
                        <p style="font-size:.87rem; color:var(--text-dark); line-height:1.7; margin:0;">
                            {{ $laporan->keterangan }}
                        </p>
                    </div>
                </div>
            @endif

            {{-- Bukti Transaksi --}}
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-paperclip me-2" style="color:#be185d;"></i>Bukti Transaksi
                </div>
                <div class="card-body">
                    @if ($laporan->has_file_bukti)
                        <div class="d-flex align-items-center gap-3"
                            style="background:var(--bg-soft); border-radius:var(--radius-md); padding:1rem 1.1rem;">
                            <i class="bi {{ $laporan->file_bukti_icon }}"
                                style="font-size:2rem; flex-shrink:0;
                                  color:{{ in_array($laporan->file_bukti_extension, ['jpg', 'jpeg', 'png']) ? '#7c3aed' : '#dc2626' }};"></i>
                            <div style="flex:1; min-width:0;">
                                <div
                                    style="font-size:.84rem; font-weight:700; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                    {{ $laporan->file_bukti }}
                                </div>
                                <span class="badge-soft tint-slate" style="font-size:.68rem;">
                                    {{ strtoupper($laporan->file_bukti_extension) }}
                                </span>
                            </div>
                            <a href="{{ $laporan->file_bukti_url }}" target="_blank" class="btn-theme-primary"
                                style="padding:.45rem 1rem; font-size:.8rem; flex-shrink:0;">
                                <i class="bi bi-eye me-1"></i> Lihat Bukti
                            </a>
                        </div>
                    @else
                        <div class="theme-empty-state" style="padding:1.8rem 1rem;">
                            <div class="empty-icon" style="width:56px; height:56px; font-size:1.4rem;">
                                <i class="bi bi-file-earmark-x"></i>
                            </div>
                            <h6 style="font-size:.86rem;">Belum ada bukti</h6>
                            <p style="font-size:.76rem;">Anda belum mengupload bukti transaksi.</p>
                            @if (in_array($laporan->status_laporan, ['draft', 'dikirim', 'ditolak']))
                                <a href="{{ route('anggota.laporan.edit', $laporan->id) }}" class="btn-theme-light"
                                    style="padding:.35rem .8rem; font-size:.78rem;">
                                    <i class="bi bi-cloud-upload me-1"></i> Upload Sekarang
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
