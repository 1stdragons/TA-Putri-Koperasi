<?php

namespace App\Console\Commands;

use App\Models\Laporan;
use App\Models\Usaha;
use App\Models\Notifikasi;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MonitoringReminderCommand extends Command
{
    protected $signature = 'monitoring:reminder';

    protected $description = 'Kirim reminder H-3, H-1, Hari-H, dan Lewat Deadline untuk laporan monitoring 14 hari yang belum dikirim';

    public function handle(NotificationService $notif): int
    {
        $this->info('Menjalankan pengecekan reminder monitoring...');

        $hariIni   = Carbon::today();
        $terkirim  = 0;

        // Hanya usaha aktif yang perlu diingatkan
        $usahaAktif = Usaha::where('status_usaha', 'aktif')
            ->with(['anggota.akun', 'laporan' => fn($q) => $q->orderBy('periode_ke', 'desc')])
            ->get();

        foreach ($usahaAktif as $usaha) {
            $laporanTerakhir = $usaha->laporan->first();

            // ── Tentukan deadline periode berikutnya ─────────────
            // Dasar perhitungan: akhir periode laporan terakhir + 14 hari.
            // Jika belum pernah lapor sama sekali, dasar: tanggal_mulai usaha + 14 hari.
            if ($laporanTerakhir) {
                // Jika laporan terakhir untuk periode ini sudah ada & berstatus
                // apa pun selain draft, berarti periode berjalan sudah dilaporkan.
                // Reminder hanya berlaku untuk PERIODE BERIKUTNYA yang belum ada laporannya.
                $sudahLaporUntukPeriodeIni = in_array($laporanTerakhir->status_laporan, ['dikirim', 'divalidasi']);

                if ($sudahLaporUntukPeriodeIni) {
                    // Anggota sudah mengirim laporan periode ini, reminder untuk periode
                    // berikutnya dihitung dari akhir periode saat ini.
                    $deadline = Carbon::parse($laporanTerakhir->tanggal_selesai)->addDays(14);
                } else {
                    // Laporan terakhir berstatus draft/ditolak: deadline periode SAAT INI.
                    $deadline = Carbon::parse($laporanTerakhir->tanggal_selesai);
                }
            } else {
                // Belum pernah lapor sama sekali
                $mulai    = $usaha->tanggal_mulai ? Carbon::parse($usaha->tanggal_mulai) : Carbon::parse($usaha->created_at);
                $deadline = $mulai->copy()->addDays(14);
            }

            $selisihHari = $hariIni->diffInDays($deadline, false); // positif jika deadline > hari ini

            $tahap = match (true) {
                $selisihHari === 3   => 'h3',
                $selisihHari === 1   => 'h1',
                $selisihHari === 0   => 'hari_h',
                $selisihHari < 0     => 'lewat',
                default              => null,
            };

            if (! $tahap) {
                continue;
            }

            // ── Cegah duplikasi: cek apakah reminder tahap ini sudah dikirim hari ini ──
            $judulTahap = match ($tahap) {
                'h3'     => 'Pengingat: 3 Hari Lagi Batas Laporan',
                'h1'     => 'Pengingat: 1 Hari Lagi Batas Laporan',
                'hari_h' => 'Hari Ini Batas Akhir Laporan',
                'lewat'  => 'Laporan Monitoring Terlambat',
            };

            $sudahDikirimHariIni = Notifikasi::where('akun_id', $usaha->anggota->akun_id)
                ->where('judul', $judulTahap)
                ->whereDate('created_at', $hariIni)
                ->where('pesan', 'like', '%' . $usaha->nama_usaha . '%')
                ->exists();

            if ($sudahDikirimHariIni) {
                continue;
            }

            $notif->notifyReminder(
                akunAnggotaId: $usaha->anggota->akun_id,
                namaUsaha: $usaha->nama_usaha,
                tahap: $tahap,
                tanggalDeadline: $deadline->translatedFormat('d F Y')
            );

            $terkirim++;

            $this->line("→ [{$tahap}] {$usaha->nama_usaha} ({$usaha->anggota->akun->nama})");
        }

        $this->info("Selesai. Total reminder terkirim: {$terkirim}");

        return self::SUCCESS;
    }
}
