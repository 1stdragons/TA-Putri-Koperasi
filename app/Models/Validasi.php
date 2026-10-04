<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Validasi extends Model
{
    use HasFactory;

    protected $table = 'validasi';

    protected $fillable = [
        'laporan_id',
        'validator_id',
        'dps_id',
        'omzet_sebelumnya',
        'persentase_kenaikan_omzet',
        'rasio_biaya',
        'indikator_omzet',
        'indikator_biaya',
        'hasil_evaluasi',
        'tanggal_validasi_pengurus',
        'status_validasi_pengurus',
        'catatan_pengurus',
        'tanggal_validasi_dps',
        'status_validasi_dps',
        'catatan_dps',
    ];

    protected $casts = [
        'omzet_sebelumnya'          => 'decimal:2',
        'persentase_kenaikan_omzet' => 'decimal:2',
        'rasio_biaya'               => 'decimal:2',
        'indikator_omzet'           => 'boolean',
        'indikator_biaya'           => 'boolean',
        'tanggal_validasi_pengurus' => 'datetime',
        'tanggal_validasi_dps'      => 'datetime',
    ];

    // ── Relasi ──────────────────────────────────────────────────

    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'laporan_id');
    }

    public function validator()
    {
        return $this->belongsTo(Akun::class, 'validator_id');
    }

    public function dps()
    {
        return $this->belongsTo(Akun::class, 'dps_id');
    }

    // ── Accessor: Hasil Evaluasi ─────────────────────────────────

    public function getHasilEvaluasiLabelAttribute(): string
    {
        return match ($this->hasil_evaluasi) {
            'aktif'       => 'Aktif',
            'peringatan'  => 'Peringatan',
            'tidak_aktif' => 'Tidak Aktif',
            default       => '-',
        };
    }

    public function getHasilEvaluasiBadgeAttribute(): string
    {
        return match ($this->hasil_evaluasi) {
            'aktif'       => 'success',
            'peringatan'  => 'warning',
            'tidak_aktif' => 'danger',
            default       => 'secondary',
        };
    }

    public function getHasilEvaluasiTintAttribute(): string
    {
        return match ($this->hasil_evaluasi) {
            'aktif'       => 'emerald',
            'peringatan'  => 'amber',
            'tidak_aktif' => 'rose',
            default       => 'slate',
        };
    }

    // ── Accessor: Status Pengurus ────────────────────────────────

    public function getStatusPengurusLabelAttribute(): string
    {
        return match ($this->status_validasi_pengurus) {
            'pending'   => 'Menunggu',
            'disetujui' => 'Disetujui',
            'ditolak'   => 'Ditolak',
            default     => '-',
        };
    }

    public function getStatusPengurusTintAttribute(): string
    {
        return match ($this->status_validasi_pengurus) {
            'pending'   => 'amber',
            'disetujui' => 'emerald',
            'ditolak'   => 'rose',
            default     => 'slate',
        };
    }

    // ── Accessor: Status DPS ─────────────────────────────────────

    public function getStatusDpsLabelAttribute(): string
    {
        return match ($this->status_validasi_dps) {
            'pending'   => 'Menunggu',
            'disetujui' => 'Disetujui',
            'ditolak'   => 'Ditolak',
            default     => '-',
        };
    }

    // ── Logic Evaluasi 14 Hari ───────────────────────────────────

    /**
     * Hitung semua indikator dan tentukan hasil evaluasi.
     * Pastikan $this->omzet_sebelumnya sudah di-set sebelum memanggil method ini.
     *
     * Aturan:
     * - indikator_omzet : kenaikan omzet >= 5% (atau periode pertama dengan omzet > 0)
     * - indikator_biaya : rasio biaya operasional < 60% omzet
     * - Keduanya terpenuhi  → aktif
     * - Salah satu gagal    → peringatan
     * - Keduanya gagal      → tidak_aktif
     */
    public function hitungEvaluasi(Laporan $laporan): void
    {
        $omzetSekarang    = (float) $laporan->omzet;
        $omzetSebelumnya  = (float) $this->omzet_sebelumnya;
        $biayaOperasional = (float) $laporan->biaya_operasional;

        // ── Persentase kenaikan omzet ────────────────────────────
        if ($omzetSebelumnya > 0) {
            $pctKenaikan = (($omzetSekarang - $omzetSebelumnya) / $omzetSebelumnya) * 100;
        } else {
            // Periode pertama: jika ada omzet, anggap terpenuhi (kenaikan 100%)
            $pctKenaikan = $omzetSekarang > 0 ? 100.0 : 0.0;
        }

        // ── Rasio biaya terhadap omzet ───────────────────────────
        $rasioBiaya = $omzetSekarang > 0
            ? ($biayaOperasional / $omzetSekarang) * 100
            : 100.0;

        // ── Indikator ────────────────────────────────────────────
        $indikatorOmzet = $pctKenaikan >= 5;
        $indikatorBiaya = $rasioBiaya < 60;

        // ── Hasil Evaluasi ───────────────────────────────────────
        if ($indikatorOmzet && $indikatorBiaya) {
            $hasilEvaluasi = 'aktif';
        } elseif (! $indikatorOmzet && ! $indikatorBiaya) {
            $hasilEvaluasi = 'tidak_aktif';
        } else {
            $hasilEvaluasi = 'peringatan';
        }

        $this->update([
            'persentase_kenaikan_omzet' => round($pctKenaikan, 2),
            'rasio_biaya'               => round($rasioBiaya, 2),
            'indikator_omzet'           => $indikatorOmzet,
            'indikator_biaya'           => $indikatorBiaya,
            'hasil_evaluasi'            => $hasilEvaluasi,
        ]);
    }
}
