<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Anggota extends Model
{
    use HasFactory;

    protected $table = 'anggota';

    protected $fillable = [
        'akun_id',
        'nomor_anggota',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'rt',
        'rw',
        'kelurahan',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'telepon',
        'pendidikan_terakhir',
        'pekerjaan_sampingan',
        'tanggal_bergabung',
        'status_anggota',
        'catatan',
    ];

    protected $casts = [
        'tanggal_lahir'    => 'date',
        'tanggal_bergabung' => 'date',
    ];

    // ── Relasi ──────────────────────────────────────────────────

    /**
     * Akun yang dimiliki anggota ini.
     */
    public function akun()
    {
        return $this->belongsTo(Akun::class, 'akun_id');
    }

    /**
     * Semua usaha milik anggota ini.
     */
    public function usaha()
    {
        return $this->hasMany(Usaha::class, 'anggota_id');
    }

    /**
     * Usaha yang masih aktif.
     */
    public function usahaAktif()
    {
        return $this->hasMany(Usaha::class, 'anggota_id')
                    ->where('status_usaha', 'aktif');
    }

    // ── Accessor ────────────────────────────────────────────────

    public function getNamaLengkapAttribute(): string
    {
        return $this->akun->nama ?? '-';
    }

    public function getJenisKelaminLabelAttribute(): string
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }

    public function getStatusAnggotaLabelAttribute(): string
    {
        return match ($this->status_anggota) {
            'aktif'       => 'Aktif',
            'peringatan'  => 'Peringatan',
            'tidak_aktif' => 'Tidak Aktif',
            default       => '-',
        };
    }

    public function getStatusAnggotaBadgeAttribute(): string
    {
        return match ($this->status_anggota) {
            'aktif'       => 'success',
            'peringatan'  => 'warning',
            'tidak_aktif' => 'danger',
            default       => 'secondary',
        };
    }

    public function getAlamatLengkapAttribute(): string
    {
        $parts = array_filter([
            $this->alamat,
            $this->rt   ? 'RT ' . $this->rt   : null,
            $this->rw   ? 'RW ' . $this->rw   : null,
            $this->kelurahan,
            $this->kecamatan,
            $this->kabupaten,
            $this->provinsi,
        ]);
        return implode(', ', $parts);
    }

    // ── Scope ───────────────────────────────────────────────────

    public function scopeAktif($query)
    {
        return $query->where('status_anggota', 'aktif');
    }

    public function scopePeringatan($query)
    {
        return $query->where('status_anggota', 'peringatan');
    }

    public function scopeTidakAktif($query)
    {
        return $query->where('status_anggota', 'tidak_aktif');
    }
}