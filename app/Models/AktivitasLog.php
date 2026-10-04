<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AktivitasLog extends Model
{
    use HasFactory;

    protected $table = 'aktivitas_log';

    protected $fillable = [
        'akun_id',
        'aktivitas',
        'keterangan',
        'ip_address',
        'user_agent',
    ];

    // ── Relasi ──────────────────────────────────────────────

    public function akun()
    {
        return $this->belongsTo(Akun::class, 'akun_id');
    }
}