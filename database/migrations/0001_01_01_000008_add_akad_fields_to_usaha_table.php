<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan field akad syariah ke tabel usaha.
     *
     * CATATAN ARSITEKTUR:
     * - jenis_akad        : jenis kontrak syariah usaha ini (informasi, tidak mempengaruhi evaluasi 14 hari)
     * - file_akad         : path file dokumen ketentuan akad yang diupload anggota
     * - status_validasi_syariah : status review DPS terhadap dokumen akad (TERPISAH dari status_usaha)
     *
     * Status validasi syariah TIDAK memblokir monitoring usaha.
     * Anggota tetap dapat menginput laporan 14 hari terlepas dari status validasi syariah.
     */
    public function up(): void
    {
        Schema::table('usaha', function (Blueprint $table) {

            // Jenis akad syariah — nullable karena modul usaha belum wajibkan ini
            $table->enum('jenis_akad', [
                'Mudharabah',
                'Musyarakah',
                'Murabahah',
                'Ijarah',
                'Qardh',
                'Lainnya',
            ])->nullable()->after('modal_awal')
                ->comment('Jenis kontrak syariah usaha. Tidak mempengaruhi evaluasi 14 hari.');

            // Path file dokumen akad (PDF/DOC/DOCX)
            $table->string('file_akad')->nullable()->after('jenis_akad')
                ->comment('Path file dokumen ketentuan akad yang diupload anggota');

            // Status validasi syariah oleh DPS — TERPISAH dari status_usaha
            $table->enum('status_validasi_syariah', [
                'belum_diupload',   // Anggota belum upload dokumen akad
                'menunggu',         // Dokumen sudah diupload, menunggu review DPS
                'disetujui',        // DPS menyetujui kesesuaian akad
                'perlu_revisi',     // DPS meminta perbaikan dokumen akad
            ])->default('belum_diupload')->after('file_akad')
                ->comment('Status review syariah oleh DPS. Tidak mempengaruhi status usaha maupun evaluasi 14 hari.');

            // Index untuk filtering di dashboard
            $table->index('jenis_akad');
            $table->index('status_validasi_syariah');
        });
    }

    public function down(): void
    {
        Schema::table('usaha', function (Blueprint $table) {
            $table->dropIndex(['jenis_akad']);
            $table->dropIndex(['status_validasi_syariah']);
            $table->dropColumn(['jenis_akad', 'file_akad', 'status_validasi_syariah']);
        });
    }
};
