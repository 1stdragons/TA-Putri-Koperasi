<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel riwayat validasi akad syariah oleh DPS.
     *
     * ALASAN TABEL TERPISAH (bukan hanya field di usaha):
     * 1. DPS bisa meminta revisi berkali-kali → ada riwayat lengkap
     * 2. Setiap iterasi "Upload → Review DPS → Perlu Revisi → Upload Ulang → Review Ulang"
     *    harus tercatat agar dapat diaudit
     * 3. Tabel ini siap dipakai Modul DPS tanpa perlu perubahan database lagi
     *
     * HUBUNGAN DENGAN TABEL VALIDASI:
     * - Tabel `validasi` : validasi laporan 14 hari oleh PENGURUS (tidak berubah)
     * - Tabel `validasi_akad` : validasi dokumen akad syariah oleh DPS (tabel baru ini)
     * Keduanya berjalan PARALEL, tidak saling memblokir.
     */
    public function up(): void
    {
        Schema::create('validasi_akad', function (Blueprint $table) {
            $table->id();

            // Usaha yang dokumen akadnya divalidasi
            $table->foreignId('usaha_id')
                ->constrained('usaha')
                ->onDelete('cascade');

            // DPS yang melakukan validasi (nullable: bisa belum ada yang menangani)
            $table->foreignId('dps_id')
                ->nullable()
                ->constrained('akun')
                ->nullOnDelete();

            // File dokumen akad yang divalidasi pada iterasi ini
            // (anggota bisa upload ulang → file berbeda per iterasi)
            $table->string('file_akad_divalidasi')
                ->comment('Salinan path file akad pada saat iterasi validasi ini');

            // Jenis akad pada saat iterasi ini
            $table->string('jenis_akad_divalidasi', 50)->nullable();

            // Hasil keputusan DPS
            $table->enum('status', ['menunggu', 'disetujui', 'perlu_revisi'])
                ->default('menunggu');

            // Catatan DPS (wajib diisi jika status = perlu_revisi)
            $table->text('catatan_dps')->nullable();

            // Kapan DPS memberikan keputusan
            $table->timestamp('divalidasi_pada')->nullable();

            // Nomor iterasi (1 = pertama kali, 2 = setelah revisi pertama, dst)
            $table->unsignedTinyInteger('iterasi')->default(1)
                ->comment('Urutan iterasi validasi. Bertambah setiap anggota upload ulang.');

            $table->timestamps();

            // Index untuk query cepat
            $table->index(['usaha_id', 'iterasi']);
            $table->index(['dps_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validasi_akad');
    }
};
