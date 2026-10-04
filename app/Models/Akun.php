<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Akun extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'akun';

    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
        'foto',
        'status',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'last_login'        => 'datetime',
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    // ── Relasi ──────────────────────────────────────────────────

    public function aktivitasLog()
    {
        return $this->hasMany(AktivitasLog::class, 'akun_id');
    }

    public function anggota()
    {
        return $this->hasOne(Anggota::class, 'akun_id');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'akun_id')
            ->orderBy('created_at', 'desc');
    }

    public function notifikasiBelumDibaca()
    {
        return $this->hasMany(Notifikasi::class, 'akun_id')
            ->where('dibaca', false);
    }

    /**
     * Laporan yang pernah divalidasi oleh akun ini (role: pengurus).
     */
    public function validasiSebagaiPengurus()
    {
        return $this->hasMany(Validasi::class, 'validator_id');
    }

    /**
     * Laporan yang pernah divalidasi syariah oleh akun ini (role: dps).
     */
    public function validasiSebagaiDps()
    {
        return $this->hasMany(Validasi::class, 'dps_id');
    }

    /**
     * Riwayat validasi akad yang pernah diproses oleh akun ini (role: dps).
     */
    public function validasiAkadSebagaiDps()
    {
        return $this->hasMany(ValidasiAkad::class, 'dps_id');
    }
    // ── Helper ──────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    public function isAnggota(): bool
    {
        return $this->role === 'anggota';
    }
    public function isPengurus(): bool
    {
        return $this->role === 'pengurus';
    }
    public function isDps(): bool
    {
        return $this->role === 'dps';
    }
    public function isKetua(): bool
    {
        return $this->role === 'ketua';
    }
    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin'    => 'Administrator',
            'anggota'  => 'Anggota',
            'pengurus' => 'Pengurus',
            'dps'      => 'DPS',
            'ketua'    => 'Ketua',
            default    => '-',
        };
    }

    public function getFotoUrlAttribute(): string
    {
        if ($this->foto && file_exists(public_path('uploads/foto/' . $this->foto))) {
            return asset('uploads/foto/' . $this->foto);
        }
        return asset('images/default-avatar.png');
    }

    public function getJumlahNotifikasiAttribute(): int
    {
        return $this->notifikasiBelumDibaca()->count();
    }
}
