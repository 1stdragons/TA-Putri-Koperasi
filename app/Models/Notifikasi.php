<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasi';

    protected $fillable = [
        'akun_id',
        'judul',
        'pesan',
        'tipe',
        'url_aksi',
        'dibaca',
        'dibaca_pada',
    ];

    protected $casts = [
        'dibaca'      => 'boolean',
        'dibaca_pada' => 'datetime',
    ];

    // ── Relasi ──────────────────────────────────────────────────

    public function akun()
    {
        return $this->belongsTo(Akun::class, 'akun_id');
    }

    // ── Accessor ─────────────────────────────────────────────────

    public function getTipeIconAttribute(): string
    {
        return match ($this->tipe) {
            'sukses'     => 'bi-check-circle-fill',
            'peringatan' => 'bi-exclamation-triangle-fill',
            'bahaya'     => 'bi-x-circle-fill',
            default      => 'bi-info-circle-fill',
        };
    }

    public function getTipeTintAttribute(): string
    {
        return match ($this->tipe) {
            'sukses'     => 'emerald',
            'peringatan' => 'amber',
            'bahaya'     => 'rose',
            default      => 'indigo',
        };
    }

    public function getWaktuLabelAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    // ── Scope Filter ─────────────────────────────────────────────

    public function scopeBelumDibaca(Builder $query)
    {
        return $query->where('dibaca', false);
    }

    public function scopeSudahDibaca(Builder $query)
    {
        return $query->where('dibaca', true);
    }

    public function scopeHariIni(Builder $query)
    {
        return $query->whereDate('created_at', Carbon::today());
    }

    public function scopeMingguIni(Builder $query)
    {
        return $query->whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek(),
        ]);
    }

    // ── Helper Static (dipertahankan untuk kompatibilitas) ───────

    public static function kirim(int $akunId, string $judul, string $pesan, string $tipe = 'info', ?string $urlAksi = null): self
    {
        return self::create([
            'akun_id'  => $akunId,
            'judul'    => $judul,
            'pesan'    => $pesan,
            'tipe'     => $tipe,
            'url_aksi' => $urlAksi,
            'dibaca'   => false,
        ]);
    }
}
