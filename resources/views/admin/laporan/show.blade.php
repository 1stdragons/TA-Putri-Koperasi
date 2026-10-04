@extends('layouts.app')

@section('title', 'Detail Laporan')
@section('page-title', 'Detail Laporan Usaha')
@section('page-subtitle', 'Laporan ' . $laporan->periode_label)

@section('sidebar-menu')
    @include('admin.partials._sidebar')
@endsection

@section('content')

    {{-- Tombol Aksi --}}
    <div class="d-flex align-items-center gap-2 flex-wrap mb-3">
        <a href="{{ route('admin.laporan.index') }}" class="btn-theme-light" style="padding:.4rem .9rem; font-size:.83rem;">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <a href="{{ route('admin.laporan.edit', $laporan->id) }}" class="btn-theme-primary"
            style="padding:.4rem .9rem; font-size:.83rem;">
            <i class="bi bi-pencil-square me-1"></i> Edit Laporan
        </a>
        <button type="button" class="btn-theme-danger" style="padding:.4rem .9rem; font-size:.83rem;" id="btnHapus">
            <i class="bi bi-trash me-1"></i> Hapus Laporan
        </button>
    </div>

    <div class="row g-3">

        {{-- ═══ KOLOM KIRI ══════════════════════════════════════════ --}}
        <div class="col-12 col-lg-4">

            {{-- Header Status --}}
            <div class="card mb-3" style="overflow:hidden;">
                <div
                    style="background:linear-gradient(120deg, var(--primary), #818cf8 60%, var(--secondary) 130%);
                    padding:1.6rem; text-align:center; position:relative; overflow:hidden;">
                    <div
                        style="position:absolute; width:120px; height:120px; background:rgba(255,255,255,.1);
                        border-radius:50%; top:-50px; right:-30px;">
                    </div>

                    <div
                        style="width:64px; height:64px; border-radius:16px; background:rgba(255,255,255,.18);
                        border:2px solid rgba(255,255,255,.4);
                        display:flex; align-items:center; justify-content:center;
                        font-size:1.75rem; color:#fff; margin:0 auto .75rem; position:relative; z-index:1;">
                        <i class="bi bi-journal-richtext"></i>
                    </div>

                    <div style="font-weight:800; color:#fff; font-size:1rem; position:relative; z-index:1;">
                        Periode ke-{{ $laporan->periode_ke }}
                    </div>
                    <div
                        style="font-size:.8rem; color:rgba(255,255,255,.85); margin:.25rem 0 .75rem; position:relative; z-index:1;">
                        {{ $laporan->tanggal_mulai->format('d/m/Y') }}
                        &mdash;
                        {{ $laporan->tanggal_selesai->format('d/m/Y') }}
                    </div>
                    <span class="badge-soft tint-{{ $laporan->status_laporan_tint }}"
                        style="background:rgba(255,255,255,.2) !important; color:#fff !important;
                         border:1px solid rgba(255,255,255,.3); font-size:.78rem;
                         position:relative; z-index:1;">
                        <i class="bi {{ $laporan->status_laporan_icon }} me-1"></i>
                        {{ $laporan->status_laporan_label }}
                    </span>
                </div>

                {{-- Catatan Pengurus jika ada --}}
                @if ($laporan->catatan_pengurus)
                    <div class="card-body py-3">
                        <div
                            style="font-size:.7rem; font-weight:700; text-transform:uppercase;
                            letter-spacing:.4px; color:var(--text-muted); margin-bottom:.4rem;">
                            Catatan Pengurus
                        </div>
                        <p style="font-size:.82rem; color:var(--text-dark); line-height:1.55; margin:0;">
                            {{ $laporan->catatan_pengurus }}
                        </p>
                    </div>
                @endif
            </div>

            {{-- Statistik Keuangan --}}
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-cash-stack me-2" style="color:var(--warning);"></i>Ringkasan Keuangan
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-3">

                        <div
                            style="background:linear-gradient(135deg,#ecfdf5,#d1fae5);
                            border-radius:var(--radius-sm); padding:.85rem 1rem;">
                            <div
                                style="font-size:.7rem; font-weight:700; text-transform:uppercase;
                                letter-spacing:.3px; color:#047857; margin-bottom:.3rem;">
                                Omzet</div>
                            <div style="font-size:1.15rem; font-weight:800; color:#047857;">
                                Rp {{ number_format($laporan->omzet, 0, ',', '.') }}
                            </div>
                        </div>

                        <div
                            style="background:linear-gradient(135deg,#fff1f2,#ffe4e6);
                            border-radius:var(--radius-sm); padding:.85rem 1rem;">
                            <div
                                style="font-size:.7rem; font-weight:700; text-transform:uppercase;
                                letter-spacing:.3px; color:#be123c; margin-bottom:.3rem;">
                                Biaya Operasional
                            </div>
                            <div style="font-size:1.15rem; font-weight:800; color:#be123c;">
                                Rp {{ number_format($laporan->biaya_operasional, 0, ',', '.') }}
                            </div>
                        </div>

                        @php $labaPositif = (float) $laporan->laba_bersih >= 0; @endphp
                        <div
                            style="background:{{ $labaPositif ? 'linear-gradient(135deg,#eef2ff,#e0e7ff)' : 'linear-gradient(135deg,#fff1f2,#ffe4e6)' }};
                            border-radius:var(--radius-sm); padding:.85rem 1rem;
                            border:2px solid {{ $labaPositif ? '#c7d2fe' : '#fecaca' }};">
                            <div
                                style="font-size:.7rem; font-weight:700; text-transform:uppercase;
                                letter-spacing:.3px; color:{{ $labaPositif ? '#4338ca' : '#be123c' }}; margin-bottom:.3rem;">
                                Laba Bersih
                            </div>
                            <div
                                style="font-size:1.25rem; font-weight:800; color:{{ $labaPositif ? '#4338ca' : '#be123c' }};">
                                <i class="bi bi-{{ $labaPositif ? 'arrow-up' : 'arrow-down' }}-circle-fill me-1"></i>
                                Rp {{ number_format(abs($laporan->laba_bersih), 0, ',', '.') }}
                            </div>
                            @php
                                $rasio =
                                    (float) $laporan->omzet > 0
                                        ? round(
                                            ((float) $laporan->biaya_operasional / (float) $laporan->omzet) * 100,
                                            1,
                                        )
                                        : 0;
                            @endphp
                            <div
                                style="font-size:.73rem; color:{{ $rasio < 60 ? '#047857' : '#b45309' }}; margin-top:.35rem; font-weight:600;">
                                <i class="bi bi-pie-chart me-1"></i>
                                Rasio Biaya: {{ $rasio }}%
                                (Target &lt; 60%)
                                @if ($rasio < 60)
                                    <i class="bi bi-check-circle-fill ms-1 text-success"></i>
                                @else
                                    <i class="bi bi-x-circle-fill ms-1" style="color:#b45309;"></i>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Informasi Anggota --}}
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-person-badge-fill me-2" style="color:var(--accent);"></i>Anggota
                </div>
                <div class="card-body p-0">
                    @php $anggota = $laporan->usaha->anggota; @endphp

                    <div class="akun-info-row px-3">
                        <img src="{{ $anggota->akun->foto_url }}" class="avatar-sm" alt="">
                        <div>
                            <div class="akun-info-label">Nama Anggota</div>
                            <div class="akun-info-value">{{ $anggota->akun->nama }}</div>
                        </div>
                    </div>
                    <div class="akun-info-row px-3">
                        <div class="akun-info-icon tint-indigo"><i class="bi bi-hash"></i></div>
                        <div>
                            <div class="akun-info-label">Nomor Anggota</div>
                            <div class="akun-info-value">
                                <span class="code-tag">{{ $anggota->nomor_anggota }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="akun-info-row px-3" style="border-bottom:none;">
                        <div class="akun-info-icon tint-emerald"><i class="bi bi-telephone-fill"></i></div>
                        <div>
                            <div class="akun-info-label">Telepon</div>
                            <div class="akun-info-value">{{ $anggota->telepon }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ KOLOM KANAN ════════════════════════════════════════ --}}
        <div class="col-12 col-lg-8">

            {{-- Informasi Usaha --}}
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-shop-window me-2" style="color:var(--primary);"></i>Informasi Usaha
                </div>
                <div class="card-body p-0">
                    @php
                        $usaha = $laporan->usaha;
                        $jTint = match ($usaha->jenis_usaha) {
                            'perdagangan' => 'cyan',
                            'jasa' => 'violet',
                            'produksi' => 'indigo',
                            'pertanian' => 'emerald',
                            default => 'slate',
                        };
                    @endphp

                    <div class="akun-info-row px-4">
                        <div class="akun-info-icon tint-indigo"><i class="bi bi-shop-window"></i></div>
                        <div>
                            <div class="akun-info-label">Nama Usaha</div>
                            <div class="akun-info-value">{{ $usaha->nama_usaha }}</div>
                        </div>
                    </div>

                    <div class="akun-info-row px-4">
                        <div class="akun-info-icon tint-{{ $jTint }}"><i class="bi bi-grid-3x3-gap-fill"></i></div>
                        <div>
                            <div class="akun-info-label">Jenis Usaha</div>
                            <div class="akun-info-value">
                                <span class="badge-soft tint-{{ $jTint }}">{{ $usaha->jenis_usaha_label }}</span>
                            </div>
                        </div>
                    </div>

                    @if ($usaha->jenis_akad)
                        <div class="akun-info-row px-4">
                            <div class="akun-info-icon tint-violet"><i class="bi bi-patch-check-fill"></i></div>
                            <div>
                                <div class="akun-info-label">Jenis Akad</div>
                                <div class="akun-info-value">
                                    <span class="badge-soft tint-{{ $usaha->jenis_akad_tint }}">
                                        {{ $usaha->jenis_akad_label }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="akun-info-row px-4" style="border-bottom:none;">
                        <div class="akun-info-icon tint-{{ $usaha->status_usaha_tint }}">
                            <i class="bi {{ $usaha->status_usaha_icon }}"></i>
                        </div>
                        <div>
                            <div class="akun-info-label">Status Usaha</div>
                            <div class="akun-info-value">
                                <span class="badge-soft tint-{{ $usaha->status_usaha_tint }}">
                                    {{ $usaha->status_usaha_label }}
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
                        <i class="bi bi-chat-left-text-fill me-2" style="color:var(--secondary);"></i>Catatan Anggota
                    </div>
                    <div class="card-body">
                        <p style="font-size:.87rem; color:var(--text-dark); line-height:1.7; margin:0;">
                            {{ $laporan->keterangan }}
                        </p>
                    </div>
                </div>
            @endif

            {{-- Bukti Laporan --}}
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span>
                        <i class="bi bi-paperclip me-2" style="color:#be185d;"></i>Bukti Laporan
                    </span>
                </div>
                <div class="card-body">
                    @if ($laporan->has_file_bukti)
                        <div class="d-flex align-items-center gap-3"
                            style="background:var(--bg-soft); border-radius:var(--radius-md); padding:1rem 1.1rem;">
                            <i class="bi {{ $laporan->file_bukti_icon }}"
                                style="font-size:2.2rem; flex-shrink:0;
                              color:{{ in_array($laporan->file_bukti_extension, ['jpg', 'jpeg', 'png']) ? '#7c3aed' : '#dc2626' }};"></i>
                            <div style="flex:1; min-width:0;">
                                <div
                                    style="font-size:.85rem; font-weight:700; overflow:hidden;
                                    text-overflow:ellipsis; white-space:nowrap;">
                                    {{ $laporan->file_bukti }}
                                </div>
                                <div style="font-size:.74rem; color:var(--text-muted); margin-top:.2rem;">
                                    <span class="badge-soft tint-slate" style="font-size:.68rem;">
                                        {{ strtoupper($laporan->file_bukti_extension) }}
                                    </span>
                                </div>
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
                            <h6 style="font-size:.87rem;">Belum ada bukti laporan</h6>
                            <p style="font-size:.77rem;">Bukti laporan belum diunggah untuk periode ini.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Informasi Evaluasi (jika sudah ada) --}}
            @if ($laporan->validasi)
                @php $v = $laporan->validasi; @endphp
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-clipboard2-check-fill me-2" style="color:var(--success);"></i>Hasil Evaluasi 14
                        Hari
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6">
                                <div
                                    style="background:var(--bg-soft); border-radius:var(--radius-sm); padding:.85rem 1rem;">
                                    <div
                                        style="font-size:.7rem; font-weight:700; text-transform:uppercase;
                                        color:var(--text-muted); margin-bottom:.4rem;">
                                        Hasil Evaluasi</div>
                                    <span class="badge-soft tint-{{ $v->hasil_evaluasi_badge ?? 'slate' }}"
                                        style="font-size:.85rem;">
                                        {{ $v->hasil_evaluasi_label ?? '-' }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div
                                    style="background:var(--bg-soft); border-radius:var(--radius-sm); padding:.85rem 1rem;">
                                    <div
                                        style="font-size:.7rem; font-weight:700; text-transform:uppercase;
                                        color:var(--text-muted); margin-bottom:.35rem;">
                                        Kenaikan Omzet</div>
                                    <div style="font-size:.92rem; font-weight:800; color:var(--text-dark);">
                                        {{ $v->persentase_kenaikan_omzet }}%
                                        @if ($v->indikator_omzet)
                                            <i class="bi bi-check-circle-fill" style="color:var(--success);"></i>
                                        @else
                                            <i class="bi bi-x-circle-fill" style="color:var(--danger);"></i>
                                        @endif
                                    </div>
                                    <div style="font-size:.7rem; color:var(--text-muted);">Target ≥ 5%</div>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div
                                    style="background:var(--bg-soft); border-radius:var(--radius-sm); padding:.85rem 1rem;">
                                    <div
                                        style="font-size:.7rem; font-weight:700; text-transform:uppercase;
                                        color:var(--text-muted); margin-bottom:.35rem;">
                                        Rasio Biaya</div>
                                    <div style="font-size:.92rem; font-weight:800; color:var(--text-dark);">
                                        {{ $v->rasio_biaya }}%
                                        @if ($v->indikator_biaya)
                                            <i class="bi bi-check-circle-fill" style="color:var(--success);"></i>
                                        @else
                                            <i class="bi bi-x-circle-fill" style="color:var(--danger);"></i>
                                        @endif
                                    </div>
                                    <div style="font-size:.7rem; color:var(--text-muted);">Target &lt; 60%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

    <form id="formHapus" method="POST" style="display:none;">
        @csrf @method('DELETE')
    </form>

    <div class="theme-loading-overlay" id="loadingOverlay">
        <div class="theme-spinner"></div>
        <span>Menghapus laporan...</span>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('btnHapus').addEventListener('click', function() {
            Swal.fire({
                title: 'Hapus Laporan?',
                html: `
                <p style="color:var(--text-muted); font-size:.87rem;">Anda akan menghapus:</p>
                <p style="font-weight:800; color:var(--text-dark); margin:.2rem 0;">
                    {{ $laporan->periode_label }}
                </p>
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
                    const form = document.getElementById('formHapus');
                    form.action = '{{ route('admin.laporan.destroy', $laporan->id) }}';
                    form.submit();
                }
            });
        });
    </script>
@endpush
