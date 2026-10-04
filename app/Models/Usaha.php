<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usaha extends Model
{
    use HasFactory;

    protected $table = 'usaha';

    protected $fillable = [
        'anggota_id',
        'nama_usaha',
        'jenis_usaha',
        'jenis_akad',
        'file_akad',
        'status_validasi_syariah',
        'deskripsi',
        'alamat_usaha',
        'modal_awal',
        'tanggal_mulai',
        'tanggal_aktivasi_monitoring',
        'status_usaha',
        'catatan',
    ];

    protected $casts = [
        'tanggal_mulai'                => 'date',
        'tanggal_aktivasi_monitoring' => 'date',
        'modal_awal'                  => 'decimal:2',
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'anggota_id');
    }

    public function laporan()
    {
        return $this->hasMany(Laporan::class, 'usaha_id')
            ->orderBy('periode_ke', 'asc');
    }

    public function laporanTerakhir()
    {
        return $this->hasOne(Laporan::class, 'usaha_id')
            ->latestOfMany('periode_ke');
    }

    public function validasiAkad()
    {
        return $this->hasMany(ValidasiAkad::class, 'usaha_id')
            ->orderBy('iterasi', 'asc');
    }

    public function validasiAkadTerakhir()
    {
        return $this->hasOne(ValidasiAkad::class, 'usaha_id')
            ->latestOfMany('iterasi');
    }

    public function getJenisUsahaLabelAttribute(): string
    {
        return match ($this->jenis_usaha) {
            'perdagangan' => 'Perdagangan',
            'jasa'        => 'Jasa',
            'produksi'    => 'Produksi',
            'pertanian'   => 'Pertanian',
            'lainnya'     => 'Lainnya',
            default       => '-',
        };
    }

    public function getJenisUsahaTintAttribute(): string
    {
        return match ($this->jenis_usaha) {
            'perdagangan' => 'cyan',
            'jasa'        => 'violet',
            'produksi'    => 'indigo',
            'pertanian'   => 'emerald',
            'lainnya'     => 'slate',
            default       => 'slate',
        };
    }

    public function getStatusUsahaLabelAttribute(): string
    {
        return match ($this->status_usaha) {
            'belum_aktif' => 'Belum Aktif',
            'aktif'       => 'Aktif',
            'vakum'       => 'Vakum',
            'tutup'       => 'Tutup',
            default       => '-',
        };
    }

    public function getStatusUsahaTintAttribute(): string
    {
        return match ($this->status_usaha) {
            'belum_aktif' => 'slate',
            'aktif'       => 'emerald',
            'vakum'       => 'amber',
            'tutup'       => 'rose',
            default       => 'slate',
        };
    }

    public function getStatusUsahaIconAttribute(): string
    {
        return match ($this->status_usaha) {
            'belum_aktif' => 'bi-clock-history',
            'aktif'       => 'bi-check-circle-fill',
            'vakum'       => 'bi-pause-circle-fill',
            'tutup'       => 'bi-x-circle-fill',
            default       => 'bi-circle',
        };
    }

    public function getStatusValidasiSyariahLabelAttribute(): string
    {
        return match ($this->status_validasi_syariah) {
            'belum_diupload' => 'Belum Upload Akad',
            'menunggu'       => 'Menunggu Validasi DPS',
            'disetujui'      => 'Disetujui DPS',
            'perlu_revisi'   => 'Perlu Revisi',
            default          => '-',
        };
    }

    public function getStatusValidasiSyariahTintAttribute(): string
    {
        return match ($this->status_validasi_syariah) {
            'belum_diupload' => 'slate',
            'menunggu'       => 'amber',
            'disetujui'      => 'emerald',
            'perlu_revisi'   => 'rose',
            default          => 'slate',
        };
    }

    public function getStatusValidasiSyariahIconAttribute(): string
    {
        return match ($this->status_validasi_syariah) {
            'belum_diupload' => 'bi-cloud-upload',
            'menunggu'       => 'bi-hourglass-split',
            'disetujui'      => 'bi-patch-check-fill',
            'perlu_revisi'   => 'bi-arrow-repeat',
            default          => 'bi-question-circle',
        };
    }

    public function getJenisAkadLabelAttribute(): string
    {
        if (! $this->jenis_akad) {
            return '-';
        }

        return match ($this->jenis_akad) {
            'Mudharabah' => 'Mudharabah',
            'Musyarakah' => 'Musyarakah',
            'Murabahah'  => 'Murabahah',
            'Ijarah'     => 'Ijarah',
            'Qardh'      => 'Qardh',
            'Lainnya'    => 'Lainnya',
            default      => $this->jenis_akad,
        };
    }

    public function getJenisAkadTintAttribute(): string
    {
        return match ($this->jenis_akad) {
            'Mudharabah' => 'indigo',
            'Musyarakah' => 'violet',
            'Murabahah'  => 'cyan',
            'Ijarah'     => 'amber',
            'Qardh'      => 'pink',
            'Lainnya'    => 'slate',
            default      => 'slate',
        };
    }

    public function getFileAkadUrlAttribute(): ?string
    {
        return $this->file_akad
            ? asset('uploads/akad/' . $this->file_akad)
            : null;
    }

    public function getHasFileAkadAttribute(): bool
    {
        return ! empty($this->file_akad);
    }

    public function getFileAkadExtensionAttribute(): ?string
    {
        return $this->file_akad
            ? strtolower(pathinfo($this->file_akad, PATHINFO_EXTENSION))
            : null;
    }

    public function getFileAkadIconAttribute(): string
    {
        return match ($this->file_akad_extension) {
            'pdf'         => 'bi-file-earmark-pdf-fill',
            'doc', 'docx' => 'bi-file-earmark-word-fill',
            default       => 'bi-file-earmark-fill',
        };
    }

    public function getPeriodeBerikutnyaAttribute(): int
    {
        $periodeTerakhir = $this->laporan()->max('periode_ke');

        return ((int) ($periodeTerakhir ?? 0)) + 1;
    }

    public function getPeriodeMonitoringBerikutnyaAttribute(): array
    {
        $laporanTerakhir = $this->laporan()
            ->orderByDesc('periode_ke')
            ->first();

        if ($laporanTerakhir) {
            $tanggalMulai = Carbon::parse($laporanTerakhir->tanggal_selesai)
                ->addDay()
                ->startOfDay();
        } else {
            if (! $this->tanggal_aktivasi_monitoring) {
                return [
                    'periode_ke'      => 1,
                    'tanggal_mulai'   => null,
                    'tanggal_selesai' => null,
                    'label'           => 'Monitoring belum dimulai',
                ];
            }

            $tanggalMulai = Carbon::parse(
                $this->tanggal_aktivasi_monitoring
            )->startOfDay();
        }

        $tanggalSelesai = $tanggalMulai
            ->copy()
            ->addDays(13);

        return [
            'periode_ke'      => $this->periode_berikutnya,
            'tanggal_mulai'   => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'label'           => $tanggalMulai->translatedFormat('d F Y')
                . ' – '
                . $tanggalSelesai->translatedFormat('d F Y'),
        ];
    }

    public function getBolehMembuatLaporanBaruAttribute(): bool
    {
        if (
            $this->status_usaha !== 'aktif'
            || $this->status_validasi_syariah !== 'disetujui'
            || ! $this->tanggal_aktivasi_monitoring
        ) {
            return false;
        }

        $laporanTerakhir = $this->laporan()
            ->orderByDesc('periode_ke')
            ->first();

        if (
            $laporanTerakhir
            && $laporanTerakhir->status_laporan !== 'divalidasi'
        ) {
            return false;
        }

        $periode = $this->periode_monitoring_berikutnya;

        if (! $periode['tanggal_selesai']) {
            return false;
        }

        return Carbon::today()->greaterThanOrEqualTo(
            $periode['tanggal_selesai']->copy()->startOfDay()
        );
    }

    public function getPesanPeriodeMonitoringAttribute(): string
    {
        if ($this->status_validasi_syariah === 'belum_diupload') {
            return 'Dokumen akad belum diunggah.';
        }

        if ($this->status_validasi_syariah === 'menunggu') {
            return 'Dokumen akad sedang menunggu validasi DPS.';
        }

        if ($this->status_validasi_syariah === 'perlu_revisi') {
            return 'Dokumen akad perlu direvisi sebelum monitoring dapat dimulai.';
        }

        if ($this->status_validasi_syariah !== 'disetujui') {
            return 'Akad usaha belum disetujui DPS.';
        }

        if ($this->status_usaha === 'belum_aktif') {
            return 'Usaha belum diaktifkan setelah validasi DPS.';
        }

        if ($this->status_usaha === 'vakum') {
            return 'Usaha sedang berstatus vakum dan tidak dapat membuat laporan monitoring.';
        }

        if ($this->status_usaha === 'tutup') {
            return 'Usaha telah ditutup dan tidak dapat membuat laporan monitoring.';
        }

        if ($this->status_usaha !== 'aktif') {
            return 'Usaha belum dapat melakukan monitoring.';
        }

        if (! $this->tanggal_aktivasi_monitoring) {
            return 'Tanggal aktivasi monitoring belum tersedia.';
        }

        $laporanTerakhir = $this->laporan()
            ->orderByDesc('periode_ke')
            ->first();

        if ($laporanTerakhir) {
            if ($laporanTerakhir->status_laporan === 'dikirim') {
                return 'Laporan periode sebelumnya masih menunggu validasi Pengurus.';
            }

            if ($laporanTerakhir->status_laporan === 'ditolak') {
                return 'Laporan periode sebelumnya harus direvisi terlebih dahulu.';
            }

            if ($laporanTerakhir->status_laporan !== 'divalidasi') {
                return 'Periode monitoring berikutnya belum tersedia.';
            }
        }

        $periode = $this->periode_monitoring_berikutnya;
        $tanggalSelesai = $periode['tanggal_selesai'];

        if (! $tanggalSelesai) {
            return 'Periode monitoring belum tersedia.';
        }

        if (Carbon::today()->lt($tanggalSelesai->copy()->startOfDay())) {
            return 'Periode ke-'
                . $periode['periode_ke']
                . ' sedang berjalan. Laporan dapat dikirim mulai '
                . $tanggalSelesai->translatedFormat('d F Y')
                . '.';
        }

        return 'Periode ke-'
            . $periode['periode_ke']
            . ' telah selesai dan siap dilaporkan.';
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status_usaha', 'aktif');
    }

    public function scopeBelumAktif(Builder $query): Builder
    {
        return $query->where('status_usaha', 'belum_aktif');
    }

    public function scopeMenungguValidasiDps(Builder $query): Builder
    {
        return $query->where(
            'status_validasi_syariah',
            'menunggu'
        );
    }

    public function scopeDisetujuiDps(Builder $query): Builder
    {
        return $query->where(
            'status_validasi_syariah',
            'disetujui'
        );
    }

    public function scopeJenisAkad(
        Builder $query,
        string $jenisAkad
    ): Builder {
        return $query->where('jenis_akad', $jenisAkad);
    }
}
