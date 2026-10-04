<?php

namespace App\Services;

use App\Models\Akun;
use App\Models\Notifikasi;

class NotificationService
{
    /**
     * Kirim notifikasi ke satu akun.
     */
    public function kirim(int $akunId, string $judul, string $pesan, string $tipe = 'info', ?string $urlAksi = null): Notifikasi
    {
        return Notifikasi::kirim($akunId, $judul, $pesan, $tipe, $urlAksi);
    }

    /**
     * Kirim notifikasi ke seluruh akun dengan role tertentu yang aktif.
     */
    private function kirimKeRole(string $role, string $judul, string $pesan, string $tipe = 'info', ?string $urlAksi = null): int
    {
        $akunList = Akun::where('role', $role)->where('status', 'aktif')->get();

        foreach ($akunList as $akun) {
            Notifikasi::kirim($akun->id, $judul, $pesan, $tipe, $urlAksi);
        }

        return $akunList->count();
    }

    /**
     * Notifikasi ke seluruh Pengurus aktif.
     * Dipicu saat: anggota submit/revisi laporan.
     */
    public function notifyPengurus(string $judul, string $pesan, string $tipe = 'info', ?string $urlAksi = null): int
    {
        return $this->kirimKeRole('pengurus', $judul, $pesan, $tipe, $urlAksi);
    }

    /**
     * Notifikasi ke seluruh DPS aktif.
     * Dipicu saat: pengurus menyetujui laporan (laporan masuk ke antrean validasi syariah).
     */
    public function notifyDPS(string $judul, string $pesan, string $tipe = 'info', ?string $urlAksi = null): int
    {
        return $this->kirimKeRole('dps', $judul, $pesan, $tipe, $urlAksi);
    }

    /**
     * Notifikasi ke satu akun anggota tertentu.
     * Dipicu saat: laporan divalidasi/ditolak, akad disetujui/perlu revisi, evaluasi selesai, reminder.
     */
    public function notifyAnggota(int $akunAnggotaId, string $judul, string $pesan, string $tipe = 'info', ?string $urlAksi = null): Notifikasi
    {
        return $this->kirim($akunAnggotaId, $judul, $pesan, $tipe, $urlAksi);
    }

    /**
     * Notifikasi ke seluruh Ketua aktif.
     * Dipicu saat: evaluasi 14 hari selesai dijalankan.
     */
    public function notifyKetua(string $judul, string $pesan, string $tipe = 'info', ?string $urlAksi = null): int
    {
        return $this->kirimKeRole('ketua', $judul, $pesan, $tipe, $urlAksi);
    }

    /**
     * Notifikasi ke seluruh Admin aktif.
     * Dipicu untuk: informasi sistem/administratif tingkat tinggi.
     */
    public function notifyAdmin(string $judul, string $pesan, string $tipe = 'info', ?string $urlAksi = null): int
    {
        return $this->kirimKeRole('admin', $judul, $pesan, $tipe, $urlAksi);
    }

    /**
     * Kirim reminder monitoring ke satu akun anggota.
     * Dipicu oleh command monitoring:reminder.
     *
     * @param string $tahap  'h3' | 'h1' | 'hari_h' | 'lewat'
     */
    public function notifyReminder(int $akunAnggotaId, string $namaUsaha, string $tahap, ?string $tanggalDeadline = null): Notifikasi
    {
        [$judul, $pesan, $tipe] = match ($tahap) {
            'h3' => [
                'Pengingat: 3 Hari Lagi Batas Laporan',
                "Usaha \"{$namaUsaha}\" belum mengirim laporan monitoring 14 hari. Batas akhir pengiriman: {$tanggalDeadline}. Segera input laporan Anda.",
                'info',
            ],
            'h1' => [
                'Pengingat: 1 Hari Lagi Batas Laporan',
                "Usaha \"{$namaUsaha}\" belum mengirim laporan monitoring. Batas akhir pengiriman besok, {$tanggalDeadline}. Jangan sampai terlewat.",
                'peringatan',
            ],
            'hari_h' => [
                'Hari Ini Batas Akhir Laporan',
                "Hari ini adalah batas akhir pengiriman laporan monitoring untuk usaha \"{$namaUsaha}\". Segera input laporan Anda sebelum tengah malam.",
                'peringatan',
            ],
            'lewat' => [
                'Laporan Monitoring Terlambat',
                "Anda belum mengirim laporan monitoring untuk usaha \"{$namaUsaha}\". Batas waktu ({$tanggalDeadline}) telah terlewat. Segera input laporan agar status usaha Anda tidak terpengaruh.",
                'bahaya',
            ],
            default => [
                'Pengingat Laporan Monitoring',
                "Usaha \"{$namaUsaha}\" belum mengirim laporan monitoring 14 hari.",
                'info',
            ],
        };

        return $this->kirim($akunAnggotaId, $judul, $pesan, $tipe);
    }
}
