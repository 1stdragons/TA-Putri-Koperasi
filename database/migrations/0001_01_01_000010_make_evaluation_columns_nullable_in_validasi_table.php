<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Membuat kolom hasil evaluasi sementara boleh bernilai NULL.
     *
     * Saat laporan baru disetujui Pengurus, record validasi dibuat lebih dulu.
     * EvaluasiService kemudian menghitung dan mengisi hasil evaluasi dalam
     * transaksi yang sama.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE `validasi`
            MODIFY `persentase_kenaikan_omzet` DECIMAL(8,2) NULL DEFAULT NULL
            COMMENT '((omzet_sekarang - omzet_sebelumnya) / omzet_sebelumnya) * 100'
        ");

        DB::statement("
            ALTER TABLE `validasi`
            MODIFY `rasio_biaya` DECIMAL(8,2) NULL DEFAULT NULL
            COMMENT '(biaya_operasional / omzet) * 100'
        ");

        DB::statement("
            ALTER TABLE `validasi`
            MODIFY `indikator_omzet` TINYINT(1) NULL DEFAULT NULL
            COMMENT 'true jika kenaikan omzet >= 5%'
        ");

        DB::statement("
            ALTER TABLE `validasi`
            MODIFY `indikator_biaya` TINYINT(1) NULL DEFAULT NULL
            COMMENT 'true jika rasio biaya < 60%'
        ");

        DB::statement("
            ALTER TABLE `validasi`
            MODIFY `hasil_evaluasi`
            ENUM('aktif', 'peringatan', 'tidak_aktif')
            NULL DEFAULT NULL
            COMMENT 'aktif = kedua indikator terpenuhi'
        ");
    }

    /**
     * Mengembalikan struktur awal jika migration di-rollback.
     *
     * Nilai NULL dinormalisasi lebih dulu agar perubahan menjadi NOT NULL
     * tidak gagal.
     */
    public function down(): void
    {
        DB::table('validasi')
            ->whereNull('persentase_kenaikan_omzet')
            ->update(['persentase_kenaikan_omzet' => 0]);

        DB::table('validasi')
            ->whereNull('rasio_biaya')
            ->update(['rasio_biaya' => 0]);

        DB::table('validasi')
            ->whereNull('indikator_omzet')
            ->update(['indikator_omzet' => false]);

        DB::table('validasi')
            ->whereNull('indikator_biaya')
            ->update(['indikator_biaya' => false]);

        DB::table('validasi')
            ->whereNull('hasil_evaluasi')
            ->update(['hasil_evaluasi' => 'peringatan']);

        DB::statement("
            ALTER TABLE `validasi`
            MODIFY `persentase_kenaikan_omzet` DECIMAL(8,2) NOT NULL DEFAULT 0
            COMMENT '((omzet_sekarang - omzet_sebelumnya) / omzet_sebelumnya) * 100'
        ");

        DB::statement("
            ALTER TABLE `validasi`
            MODIFY `rasio_biaya` DECIMAL(8,2) NOT NULL DEFAULT 0
            COMMENT '(biaya_operasional / omzet) * 100'
        ");

        DB::statement("
            ALTER TABLE `validasi`
            MODIFY `indikator_omzet` TINYINT(1) NOT NULL DEFAULT 0
            COMMENT 'true jika kenaikan omzet >= 5%'
        ");

        DB::statement("
            ALTER TABLE `validasi`
            MODIFY `indikator_biaya` TINYINT(1) NOT NULL DEFAULT 0
            COMMENT 'true jika rasio biaya < 60%'
        ");

        DB::statement("
            ALTER TABLE `validasi`
            MODIFY `hasil_evaluasi`
            ENUM('aktif', 'peringatan', 'tidak_aktif')
            NOT NULL DEFAULT 'peringatan'
            COMMENT 'aktif = kedua indikator terpenuhi'
        ");
    }
};
