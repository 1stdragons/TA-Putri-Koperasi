<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan fondasi aktivasi monitoring usaha.
     *
     * Alur final:
     * 1. Usaha dibuat dengan status belum_aktif.
     * 2. DPS memeriksa dokumen akad.
     * 3. Setelah akad disetujui:
     *    - status_validasi_syariah = disetujui
     *    - status_usaha = aktif
     *    - tanggal_aktivasi_monitoring diisi otomatis
     * 4. Periode monitoring pertama dihitung sejak tanggal aktivasi tersebut.
     */
    public function up(): void
    {
        /*
         * Tambahkan status belum_aktif ke enum status_usaha.
         *
         * DB::statement digunakan karena proyek memakai MySQL dan perubahan
         * enum lebih stabil dilakukan secara langsung.
         */
        DB::statement("
            ALTER TABLE usaha
            MODIFY status_usaha
            ENUM('belum_aktif', 'aktif', 'tutup', 'vakum')
            NOT NULL DEFAULT 'belum_aktif'
        ");

        Schema::table('usaha', function (Blueprint $table) {
            $table->date('tanggal_aktivasi_monitoring')
                ->nullable()
                ->after('tanggal_mulai')
                ->comment(
                    'Tanggal monitoring dimulai setelah akad disetujui DPS'
                );

            $table->index(
                'tanggal_aktivasi_monitoring',
                'usaha_tanggal_aktivasi_monitoring_index'
            );
        });

        /*
         * Menjaga data lama yang akadnya sudah disetujui.
         *
         * Tanggal aktivasi diambil dari tanggal persetujuan DPS pertama.
         * Data yang belum disetujui tetap bernilai null.
         */
        DB::statement("
            UPDATE usaha
            SET tanggal_aktivasi_monitoring = (
                SELECT DATE(MIN(validasi_akad.divalidasi_pada))
                FROM validasi_akad
                WHERE validasi_akad.usaha_id = usaha.id
                  AND validasi_akad.status = 'disetujui'
                  AND validasi_akad.divalidasi_pada IS NOT NULL
            )
            WHERE status_validasi_syariah = 'disetujui'
        ");

        /*
         * Pengaman untuk data lama yang sudah berstatus disetujui,
         * tetapi tidak memiliki riwayat validasi_akad.
         */
        DB::statement("
            UPDATE usaha
            SET tanggal_aktivasi_monitoring = DATE(updated_at)
            WHERE status_validasi_syariah = 'disetujui'
              AND tanggal_aktivasi_monitoring IS NULL
        ");

        /*
         * Usaha yang akadnya belum disetujui tidak boleh langsung aktif.
         */
        DB::statement("
            UPDATE usaha
            SET status_usaha = 'belum_aktif'
            WHERE status_validasi_syariah <> 'disetujui'
              AND status_usaha = 'aktif'
        ");
    }

    public function down(): void
    {
        Schema::table('usaha', function (Blueprint $table) {
            $table->dropIndex(
                'usaha_tanggal_aktivasi_monitoring_index'
            );

            $table->dropColumn('tanggal_aktivasi_monitoring');
        });

        /*
         * Sebelum menghapus nilai belum_aktif dari enum,
         * ubah kembali datanya menjadi vakum agar rollback tidak gagal.
         */
        DB::table('usaha')
            ->where('status_usaha', 'belum_aktif')
            ->update([
                'status_usaha' => 'vakum',
            ]);

        DB::statement("
            ALTER TABLE usaha
            MODIFY status_usaha
            ENUM('aktif', 'tutup', 'vakum')
            NOT NULL DEFAULT 'aktif'
        ");
    }
};
