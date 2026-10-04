<?php

namespace App\Services;

use App\Models\AktivitasLog;
use App\Models\Laporan;
use App\Models\Validasi;
use Illuminate\Support\Facades\Auth;

class EvaluasiService
{
    public function __construct(private NotificationService $notification = new NotificationService()) {}

    public function jalankan(Laporan $laporan, ?int $akunPemicuId = null): ?Validasi
    {
        $laporan->load(['usaha.anggota.akun', 'validasi']);

        if (! $this->syaratTerpenuhi($laporan)) {
            return null;
        }

        $validasi = $laporan->validasi;
        if (! $validasi) {
            $validasi = Validasi::create([
                'laporan_id'               => $laporan->id,
                'validator_id'             => null,
                'status_validasi_pengurus' => 'disetujui',
                'status_validasi_dps'      => 'disetujui',
            ]);
        }

        $laporanSebelumnya = Laporan::where('usaha_id', $laporan->usaha_id)
            ->where('periode_ke', $laporan->periode_ke - 1)
            ->where('status_laporan', 'divalidasi')
            ->first();

        $omzetSebelumnya = $laporanSebelumnya ? (float) $laporanSebelumnya->omzet : 0;
        $validasi->update(['omzet_sebelumnya' => $omzetSebelumnya]);

        $validasi->hitungEvaluasi($laporan);
        $validasi->refresh();

        $this->updateStatusAnggota($laporan, $validasi);
        $this->kirimNotifikasi($laporan, $validasi);
        $this->catatLog($laporan, $validasi, $akunPemicuId);

        return $validasi;
    }

    public function syaratTerpenuhi(Laporan $laporan): bool
    {
        if ($laporan->status_laporan !== 'divalidasi') {
            return false;
        }
        if (! $laporan->validasi || $laporan->validasi->status_validasi_pengurus !== 'disetujui') {
            return false;
        }
        $laporan->loadMissing('usaha');
        if ($laporan->usaha->status_validasi_syariah !== 'disetujui') {
            return false;
        }
        return true;
    }

    private function updateStatusAnggota(Laporan $laporan, Validasi $validasi): void
    {
        $laporan->loadMissing('usaha.anggota');
        $statusBaru = match ($validasi->hasil_evaluasi) {
            'aktif'       => 'aktif',
            'peringatan'  => 'peringatan',
            'tidak_aktif' => 'tidak_aktif',
            default       => 'peringatan',
        };
        $laporan->usaha->anggota->update(['status_anggota' => $statusBaru]);
    }

    /**
     * Kirim notifikasi ke Anggota DAN ke seluruh Ketua setelah evaluasi selesai.
     */
    private function kirimNotifikasi(Laporan $laporan, Validasi $validasi): void
    {
        $laporan->loadMissing('usaha.anggota.akun');

        $akunAnggotaId = $laporan->usaha->anggota->akun_id;
        $namaAnggota   = $laporan->usaha->anggota->akun->nama;
        $namaUsaha     = $laporan->usaha->nama_usaha;
        $periode       = $laporan->periode_ke;

        $labelHasil = match ($validasi->hasil_evaluasi) {
            'aktif'       => 'AKTIF ✓',
            'peringatan'  => 'PERINGATAN ⚠',
            'tidak_aktif' => 'TIDAK AKTIF ✗',
            default       => '-',
        };

        // ── Notifikasi ke Anggota ────────────────────────────────
        $pesanAnggota = "Evaluasi periode ke-{$periode} usaha \"{$namaUsaha}\" telah selesai. "
            . "Hasil: {$labelHasil}. "
            . "Kenaikan omzet: {$validasi->persentase_kenaikan_omzet}% "
            . "(indikator " . ($validasi->indikator_omzet ? "terpenuhi" : "tidak terpenuhi") . "). "
            . "Rasio biaya: {$validasi->rasio_biaya}% "
            . "(indikator " . ($validasi->indikator_biaya ? "terpenuhi" : "tidak terpenuhi") . ").";

        if ($validasi->hasil_evaluasi === 'peringatan') {
            $pesanAnggota .= ' Tingkatkan kinerja usaha Anda agar lebih baik pada periode berikutnya.';
        } elseif ($validasi->hasil_evaluasi === 'tidak_aktif') {
            $pesanAnggota .= ' Kinerja usaha Anda berada di bawah target. Segera evaluasi dan perbaiki usaha Anda, atau hubungi pengurus untuk mendapat bimbingan.';
        }

        $tipeAnggota = match ($validasi->hasil_evaluasi) {
            'aktif'       => 'sukses',
            'peringatan'  => 'peringatan',
            'tidak_aktif' => 'bahaya',
            default       => 'info',
        };

        $this->notification->notifyAnggota(
            $akunAnggotaId,
            'Hasil Evaluasi 14 Hari — ' . ucfirst(str_replace('_', ' ', $validasi->hasil_evaluasi)),
            $pesanAnggota,
            $tipeAnggota
        );

        // ── Notifikasi ke seluruh Ketua ──────────────────────────
        $pesanKetua = "Evaluasi 14 hari selesai untuk anggota {$namaAnggota} (usaha: {$namaUsaha}, periode ke-{$periode}). "
            . "Hasil evaluasi: {$labelHasil}.";

        $this->notification->notifyKetua(
            'Evaluasi Baru: ' . $namaAnggota,
            $pesanKetua,
            $tipeAnggota
        );
    }

    private function catatLog(Laporan $laporan, Validasi $validasi, ?int $akunPemicuId): void
    {
        $laporan->loadMissing('usaha.anggota.akun');

        AktivitasLog::create([
            'akun_id'    => $akunPemicuId ?? Auth::id(),
            'aktivitas'  => 'Evaluasi Otomatis 14 Hari',
            'keterangan' => 'Evaluasi periode ke-' . $laporan->periode_ke
                . ' usaha: ' . $laporan->usaha->nama_usaha
                . ' — anggota: ' . $laporan->usaha->anggota->akun->nama
                . ' — hasil: ' . $validasi->hasil_evaluasi
                . ' (omzet ' . ($validasi->indikator_omzet ? '✓' : '✗')
                . ', biaya ' . ($validasi->indikator_biaya ? '✓' : '✗') . ')',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function preview(Laporan $laporan): array
    {
        $laporan->loadMissing('usaha');

        $laporanSebelumnya = Laporan::where('usaha_id', $laporan->usaha_id)
            ->where('periode_ke', $laporan->periode_ke - 1)
            ->where('status_laporan', 'divalidasi')
            ->first();

        $omzetSebelumnya = $laporanSebelumnya ? (float) $laporanSebelumnya->omzet : 0;
        $omzetSekarang   = (float) $laporan->omzet;
        $biaya           = (float) $laporan->biaya_operasional;

        $pctKenaikan = $omzetSebelumnya > 0
            ? round((($omzetSekarang - $omzetSebelumnya) / $omzetSebelumnya) * 100, 2)
            : ($omzetSekarang > 0 ? 100.00 : 0.00);

        $rasioBiaya     = $omzetSekarang > 0 ? round(($biaya / $omzetSekarang) * 100, 2) : 100.00;
        $indikatorOmzet = $pctKenaikan >= 5;
        $indikatorBiaya = $rasioBiaya < 60;

        $hasil = $indikatorOmzet && $indikatorBiaya
            ? 'aktif'
            : (! $indikatorOmzet && ! $indikatorBiaya ? 'tidak_aktif' : 'peringatan');

        return compact('omzetSebelumnya', 'omzetSekarang', 'pctKenaikan', 'rasioBiaya', 'indikatorOmzet', 'indikatorBiaya', 'hasil');
    }
}
