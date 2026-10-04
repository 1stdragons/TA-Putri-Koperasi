<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ValidasiAkad extends Model
{
    use HasFactory;

    protected $table = 'validasi_akad';

    protected $fillable = [
        'usaha_id',
        'dps_id',
        'file_akad_divalidasi',
        'jenis_akad_divalidasi',
        'status',
        'catatan_dps',
        'divalidasi_pada',
        'iterasi',
    ];

    protected $casts = [
        'divalidasi_pada' => 'datetime',
        'iterasi'         => 'integer',
    ];

    // ── Relasi ──────────────────────────────────────────────────

    /**
     * Usaha yang dokumen akadnya divalidasi.
     */
    public function usaha()
    {
        return $this->belongsTo(Usaha::class, 'usaha_id');
    }

    /**
     * Anggota DPS yang memvalidasi.
     */
    public function dps()
    {
        return $this->belongsTo(Akun::class, 'dps_id');
    }

    // ── Accessor ────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'menunggu'     => 'Menunggu Validasi',
            'disetujui'    => 'Disetujui',
            'perlu_revisi' => 'Perlu Revisi',
            default        => '-',
        };
    }

    public function getStatusTintAttribute(): string
    {
        return match ($this->status) {
            'menunggu'     => 'amber',
            'disetujui'    => 'emerald',
            'perlu_revisi' => 'rose',
            default        => 'slate',
        };
    }

    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            'menunggu'     => 'bi-hourglass-split',
            'disetujui'    => 'bi-patch-check-fill',
            'perlu_revisi' => 'bi-arrow-repeat',
            default        => 'bi-question-circle',
        };
    }

    public function getFileUrlAttribute(): ?string
    {
        if (! $this->file_akad_divalidasi) {
            return null;
        }
        return asset('uploads/akad/' . $this->file_akad_divalidasi);
    }

    // ── Helper ───────────────────────────────────────────────────

    /**
     * Buat entri validasi akad baru ketika anggota upload dokumen.
     * Dipanggil dari controller usaha nanti.
     *
     * @param int    $usahaId
     * @param string $fileAkad     Nama file yang sudah disimpan
     * @param string $jenisAkad    Jenis akad yang dipilih
     */
    public static function buatIterasiBaru(
        int    $usahaId,
        string $fileAkad,
        string $jenisAkad
    ): self {
        $iterasiTerakhir = self::where('usaha_id', $usahaId)->max('iterasi') ?? 0;

        return self::create([
            'usaha_id'              => $usahaId,
            'file_akad_divalidasi'  => $fileAkad,
            'jenis_akad_divalidasi' => $jenisAkad,
            'status'                => 'menunggu',
            'iterasi'               => $iterasiTerakhir + 1,
        ]);
    }

    /**
     * Scope: hanya yang masih menunggu validasi DPS.
     */
    public function scopeMenunggu($query)
    {
        return $query->where('status', 'menunggu');
    }
}
