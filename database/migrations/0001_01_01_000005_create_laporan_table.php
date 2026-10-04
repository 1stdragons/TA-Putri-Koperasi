<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('usaha_id')
                  ->constrained('usaha')
                  ->onDelete('cascade');

            $table->unsignedInteger('periode_ke')
                  ->comment('Urutan periode laporan ke-N');

            $table->date('tanggal_mulai')
                  ->comment('Tanggal awal periode 14 hari');

            $table->date('tanggal_selesai')
                  ->comment('Tanggal akhir periode 14 hari');

            $table->decimal('omzet', 15, 2)->default(0)
                  ->comment('Pendapatan kotor periode ini');

            $table->decimal('biaya_operasional', 15, 2)->default(0)
                  ->comment('Total biaya operasional periode ini');

            $table->decimal('laba_bersih', 15, 2)->default(0)
                  ->comment('Otomatis: omzet - biaya_operasional');

            $table->text('keterangan')->nullable()
                  ->comment('Keterangan tambahan dari anggota');

            $table->string('file_bukti')->nullable()
                  ->comment('Path file bukti laporan (foto/PDF)');

            $table->enum('status_laporan', [
                'draft',
                'dikirim',
                'divalidasi',
                'ditolak',
            ])->default('draft');

            $table->text('catatan_pengurus')->nullable()
                  ->comment('Catatan dari pengurus saat validasi');

            // Pastikan setiap usaha hanya memiliki satu laporan per periode
            $table->unique(['usaha_id', 'periode_ke'], 'unique_usaha_periode');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};