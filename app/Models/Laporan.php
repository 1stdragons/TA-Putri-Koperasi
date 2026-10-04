<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Laporan extends Model
{
    use HasFactory;

    protected $table = 'laporan';

    protected $fillable = [
        'usaha_id',
        'periode_ke',
        'tanggal_mulai',
        'tanggal_selesai',
        'omzet',
        'biaya_operasional',
        'laba_bersih',
        'keterangan',
        'file_bukti',
        'status_laporan',
        'catatan_pengurus',
    ];

    protected $casts = [
        'tanggal_mulai'    => 'date',
        'tanggal_selesai'  => 'date',
        'omzet'            => 'decimal:2',
        'biaya_operasional' => 'decimal:2',
        'laba_bersih'      => 'decimal:2',
    ];

    // ── Relasi ──────────────────────────────────────────────────

    public function usaha()
    {
        return $this->belongsTo(Usaha::class, 'usaha_id');
    }

    public function validasi()
    {
        return $this->hasOne(Validasi::class, 'laporan_id');
    }

    // ── Accessor: Status ─────────────────────────────────────────

    public function getStatusLaporanLabelAttribute(): string
    {
        return match ($this->status_laporan) {
            'draft'      => 'Draft',
            'dikirim'    => 'Dikirim',
            'divalidasi' => 'Divalidasi',
            'ditolak'    => 'Ditolak',
            default      => '-',
        };
    }

    public function getStatusLaporanTintAttribute(): string
    {
        return match ($this->status_laporan) {
            'draft'      => 'slate',
            'dikirim'    => 'blue',
            'divalidasi' => 'emerald',
            'ditolak'    => 'rose',
            default      => 'slate',
        };
    }

    public function getStatusLaporanIconAttribute(): string
    {
        return match ($this->status_laporan) {
            'draft'      => 'bi-file-earmark',
            'dikirim'    => 'bi-send-fill',
            'divalidasi' => 'bi-check-circle-fill',
            'ditolak'    => 'bi-x-circle-fill',
            default      => 'bi-circle',
        };
    }

    // ── Accessor: File Bukti ─────────────────────────────────────

    public function getFileBuktiUrlAttribute(): ?string
    {
        if (! $this->file_bukti) return null;
        return asset('uploads/laporan/' . $this->file_bukti);
    }

    public function getHasFileBuktiAttribute(): bool
    {
        return ! empty($this->file_bukti);
    }

    public function getFileBuktiExtensionAttribute(): ?string
    {
        if (! $this->file_bukti) return null;
        return strtolower(pathinfo($this->file_bukti, PATHINFO_EXTENSION));
    }

    public function getFileBuktiIconAttribute(): string
    {
        return match ($this->file_bukti_extension) {
            'pdf'          => 'bi-file-earmark-pdf-fill',
            'jpg', 'jpeg', 'png' => 'bi-file-earmark-image-fill',
            default        => 'bi-file-earmark-fill',
        };
    }

    // ── Accessor: Periode ────────────────────────────────────────

    public function getPeriodeLabelAttribute(): string
    {
        return 'Periode ke-' . $this->periode_ke
            . ' (' . $this->tanggal_mulai->format('d/m/Y')
            . ' – ' . $this->tanggal_selesai->format('d/m/Y') . ')';
    }

    // ── Accessor: Laba ───────────────────────────────────────────

    public function getLabaBersihPositifAttribute(): bool
    {
        return (float) $this->laba_bersih >= 0;
    }

    // ── Scope ────────────────────────────────────────────────────

    public function scopeDikirim($query)
    {
        return $query->where('status_laporan', 'dikirim');
    }

    public function scopeDivalidasi($query)
    {
        return $query->where('status_laporan', 'divalidasi');
    }

    public function scopeDitolak($query)
    {
        return $query->where('status_laporan', 'ditolak');
    }
}
