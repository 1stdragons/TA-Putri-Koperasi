@extends('layouts.app')

@section('title', 'Detail Anggota')
@section('page-title', 'Detail Anggota')
@section('page-subtitle', 'Profil lengkap ' . $anggota->akun->nama)

@section('sidebar-menu')
    @include('admin.partials._sidebar')
@endsection

@section('content')

    <div class="mb-3 d-flex align-items-center gap-2">
        <a href="{{ route('admin.anggota.index') }}" class="btn-theme-light" style="padding:.4rem .9rem; font-size:.83rem;">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <a href="{{ route('admin.anggota.edit', $anggota->id) }}" class="btn-theme-primary"
            style="padding:.4rem .9rem; font-size:.83rem;">
            <i class="bi bi-pencil-square me-1"></i> Edit Data
        </a>
    </div>

    <div class="row g-3">

        {{-- ── Kolom Kiri: Profil Header & Stats ──────────────── --}}
        <div class="col-12 col-lg-4">

            {{-- Profile Banner --}}
            <div class="akun-detail-header mb-3">
                <img src="{{ $anggota->akun->foto_url }}" alt="{{ $anggota->akun->nama }}">
                <h5>{{ $anggota->akun->nama }}</h5>
                <span class="role-pill">
                    <i class="bi bi-hash me-1"></i>{{ $anggota->nomor_anggota }}
                </span>
            </div>

            {{-- Status Badge --}}
            <div class="card mb-3">
                <div class="card-body py-3">
                    @php
                        $sTint = match ($anggota->status_anggota) {
                            'aktif' => 'emerald',
                            'peringatan' => 'amber',
                            'tidak_aktif' => 'rose',
                            default => 'slate',
                        };
                        $sIcon = match ($anggota->status_anggota) {
                            'aktif' => 'bi-check-circle-fill',
                            'peringatan' => 'bi-exclamation-circle-fill',
                            'tidak_aktif' => 'bi-x-circle-fill',
                            default => 'bi-circle',
                        };
                    @endphp
                    <div class="text-center">
                        <div
                            style="font-size:.72rem; color:var(--text-muted); font-weight:700; text-transform:uppercase; letter-spacing:.4px; margin-bottom:.5rem;">
                            Status Keanggotaan
                        </div>
                        <span class="badge-soft tint-{{ $sTint }}" style="font-size:.88rem; padding:.5rem 1.2rem;">
                            <i class="bi {{ $sIcon }} me-1"></i>
                            {{ $anggota->status_anggota_label }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Statistik Mini --}}
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-bar-chart-fill me-2" style="color:var(--primary);"></i>Ringkasan Data
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="akun-stat-mini">
                                <div class="num">{{ $anggota->usaha->count() }}</div>
                                <div class="label">Unit Usaha</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="akun-stat-mini">
                                <div class="num">{{ $jumlahLaporan }}</div>
                                <div class="label">Laporan</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="akun-stat-mini">
                                <div style="font-size:1rem; font-weight:800; color:var(--primary-dark);">
                                    {{ $anggota->tanggal_bergabung->format('d M Y') }}
                                </div>
                                <div class="label">Bergabung</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Evaluasi Terakhir --}}
            @if ($evaluasiTerakhir)
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="bi bi-clipboard2-check-fill me-2" style="color:var(--success);"></i>Evaluasi Terakhir
                    </div>
                    <div class="card-body">
                        @php
                            $eTint = match ($evaluasiTerakhir->hasil_evaluasi) {
                                'aktif' => 'emerald',
                                'peringatan' => 'amber',
                                'tidak_aktif' => 'rose',
                                default => 'slate',
                            };
                        @endphp
                        <div class="d-flex flex-column gap-2">
                            <div>
                                <div
                                    style="font-size:.72rem; color:var(--text-muted); font-weight:700; text-transform:uppercase;">
                                    Hasil Evaluasi</div>
                                <span class="badge-soft tint-{{ $eTint }} mt-1">
                                    {{ $evaluasiTerakhir->hasil_evaluasi_label }}
                                </span>
                            </div>
                            <div>
                                <div
                                    style="font-size:.72rem; color:var(--text-muted); font-weight:700; text-transform:uppercase;">
                                    Kenaikan Omzet</div>
                                <div style="font-size:.88rem; font-weight:700; color:var(--text-dark);">
                                    {{ $evaluasiTerakhir->persentase_kenaikan_omzet }}%
                                    @if ($evaluasiTerakhir->indikator_omzet)
                                        <i class="bi bi-check-circle-fill text-success ms-1"></i>
                                    @else
                                        <i class="bi bi-x-circle-fill text-danger ms-1"></i>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <div
                                    style="font-size:.72rem; color:var(--text-muted); font-weight:700; text-transform:uppercase;">
                                    Rasio Biaya</div>
                                <div style="font-size:.88rem; font-weight:700; color:var(--text-dark);">
                                    {{ $evaluasiTerakhir->rasio_biaya }}%
                                    @if ($evaluasiTerakhir->indikator_biaya)
                                        <i class="bi bi-check-circle-fill text-success ms-1"></i>
                                    @else
                                        <i class="bi bi-x-circle-fill text-danger ms-1"></i>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Akun Terkait --}}
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-person-circle me-2" style="color:var(--accent);"></i>Akun Login
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ $anggota->akun->foto_url }}" class="avatar-sm" alt="">
                        <div>
                            <div style="font-size:.85rem; font-weight:700; color:var(--text-dark);">
                                {{ $anggota->akun->nama }}
                            </div>
                            <div style="font-size:.76rem; color:var(--text-muted);">
                                {{ $anggota->akun->email }}
                            </div>
                            <span class="badge-soft tint-indigo mt-1">
                                {{ $anggota->akun->role_label }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Kolom Kanan: Detail Biodata ────────────────────── --}}
        <div class="col-12 col-lg-8">

            {{-- Data Diri --}}
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-person-lines-fill me-2" style="color:var(--success);"></i>Data Diri
                </div>
                <div class="card-body p-0">
                    @php
                        $rows = [
                            [
                                'icon' => 'bi-credit-card-fill',
                                'tint' => 'indigo',
                                'label' => 'NIK',
                                'value' => '<span class="code-tag">' . $anggota->nik . '</span>',
                            ],
                            [
                                'icon' => 'bi-geo-fill',
                                'tint' => 'amber',
                                'label' => 'Tempat, Tgl Lahir',
                                'value' =>
                                    $anggota->tempat_lahir .
                                    ', ' .
                                    $anggota->tanggal_lahir->translatedFormat('d F Y') .
                                    ' (' .
                                    $anggota->tanggal_lahir->age .
                                    ' tahun)',
                            ],
                            [
                                'icon' => 'bi-gender-ambiguous',
                                'tint' => $anggota->jenis_kelamin === 'L' ? 'blue' : 'pink',
                                'label' => 'Jenis Kelamin',
                                'value' => $anggota->jenis_kelamin_label,
                            ],
                            [
                                'icon' => 'bi-telephone-fill',
                                'tint' => 'emerald',
                                'label' => 'Telepon',
                                'value' => $anggota->telepon,
                            ],
                            [
                                'icon' => 'bi-mortarboard-fill',
                                'tint' => 'violet',
                                'label' => 'Pendidikan Terakhir',
                                'value' => $anggota->pendidikan_terakhir ?: '-',
                            ],
                            [
                                'icon' => 'bi-briefcase-fill',
                                'tint' => 'cyan',
                                'label' => 'Pekerjaan Sampingan',
                                'value' => $anggota->pekerjaan_sampingan ?: '-',
                            ],
                        ];
                    @endphp

                    @foreach ($rows as $row)
                        <div class="akun-info-row px-4">
                            <div class="akun-info-icon tint-{{ $row['tint'] }}">
                                <i class="bi {{ $row['icon'] }}"></i>
                            </div>
                            <div>
                                <div class="akun-info-label">{{ $row['label'] }}</div>
                                <div class="akun-info-value">{!! $row['value'] !!}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Alamat --}}
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-geo-alt-fill me-2" style="color:var(--warning);"></i>Alamat Domisili
                </div>
                <div class="card-body">
                    <div class="akun-info-row" style="border:none; padding-top:0;">
                        <div class="akun-info-icon tint-amber">
                            <i class="bi bi-house-fill"></i>
                        </div>
                        <div>
                            <div class="akun-info-label">Alamat Lengkap</div>
                            <div class="akun-info-value" style="line-height:1.6;">
                                {{ $anggota->alamat }}
                                @if ($anggota->rt || $anggota->rw)
                                    , RT {{ $anggota->rt ?? '-' }} / RW {{ $anggota->rw ?? '-' }}
                                @endif
                                @if ($anggota->kelurahan)
                                    <br>Kel.
                                    {{ $anggota->kelurahan }}{{ $anggota->kecamatan ? ', Kec. ' . $anggota->kecamatan : '' }}
                                @endif
                                @if ($anggota->kabupaten)
                                    <br>{{ $anggota->kabupaten }}{{ $anggota->provinsi ? ', ' . $anggota->provinsi : '' }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Catatan --}}
            @if ($anggota->catatan)
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="bi bi-sticky-fill me-2" style="color:var(--secondary);"></i>Catatan
                    </div>
                    <div class="card-body">
                        <p style="font-size:.87rem; color:var(--text-muted); line-height:1.7; margin:0;">
                            {{ $anggota->catatan }}
                        </p>
                    </div>
                </div>
            @endif

            {{-- Daftar Usaha --}}
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span>
                        <i class="bi bi-shop me-2" style="color:var(--primary);"></i>Daftar Usaha
                    </span>
                    <span class="badge-soft tint-indigo">{{ $anggota->usaha->count() }} usaha</span>
                </div>
                <div class="card-body p-0">
                    @forelse ($anggota->usaha as $usaha)
                        @php
                            $uTint = match ($usaha->status_usaha) {
                                'aktif' => 'emerald',
                                'tutup' => 'rose',
                                'vakum' => 'amber',
                                default => 'slate',
                            };
                        @endphp
                        <div class="d-flex align-items-center gap-3 px-4 py-3" style="border-bottom:1px solid #f6f7fb;">
                            <div
                                style="width:40px;height:40px;border-radius:11px;flex-shrink:0;
                                    background:linear-gradient(135deg,#eef2ff,#e0e7ff);
                                    display:flex;align-items:center;justify-content:center;
                                    font-size:1.1rem;color:#4338ca;">
                                <i class="bi bi-shop-window"></i>
                            </div>
                            <div style="flex:1; min-width:0;">
                                <div style="font-size:.85rem; font-weight:700; color:var(--text-dark);">
                                    {{ $usaha->nama_usaha }}
                                </div>
                                <div style="font-size:.76rem; color:var(--text-muted);">
                                    {{ $usaha->jenis_usaha_label }} &mdash; mulai
                                    {{ $usaha->tanggal_mulai->format('d/m/Y') }}
                                </div>
                            </div>
                            <span class="badge-soft tint-{{ $uTint }}">{{ $usaha->status_usaha_label }}</span>
                        </div>
                    @empty
                        <div class="theme-empty-state" style="padding:2rem 1rem;">
                            <div class="empty-icon" style="width:60px;height:60px;font-size:1.6rem;">
                                <i class="bi bi-shop"></i>
                            </div>
                            <h6 style="font-size:.88rem;">Belum ada data usaha</h6>
                            <p style="font-size:.78rem;">Data usaha anggota akan tampil di sini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Form hapus --}}
    <form id="formHapusAnggota" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
        <input type="hidden" name="_method" value="DELETE">
    </form>

    <div class="theme-loading-overlay" id="loadingOverlay">
        <div class="theme-spinner"></div>
        <span>Menghapus data...</span>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function hapusAnggota() {
            Swal.fire({
                title: 'Hapus Data Anggota?',
                html: `
                <p style="color:var(--text-muted); font-size:.87rem;">Anda akan menghapus:</p>
                <p style="font-weight:800; color:var(--text-dark);">
                    {{ $anggota->akun->nama }}
                    <span style="color:var(--primary);">({{ $anggota->nomor_anggota }})</span>
                </p>
                <div style="background:#fff8f0; border:1px solid #fde68a; border-radius:10px;
                            padding:.75rem 1rem; margin-top:.8rem; font-size:.81rem; color:#92400e; text-align:left;">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    <strong>Perhatian:</strong> Seluruh data usaha, laporan, dan evaluasi akan ikut terhapus.
                    Akun login <strong>tidak</strong> ikut terhapus.
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
                    form.action = '{{ route('admin.anggota.destroy', $anggota->id) }}';
                    form.submit();
                }
            });
        }
    </script>
@endpush
