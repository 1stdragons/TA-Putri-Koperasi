<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('validasi', function (Blueprint $table) {
            $table->id();

            // Setiap laporan hanya punya satu record validasi
            $table->foreignId('laporan_id')
                  ->unique()
                  ->constrained('laporan')
                  ->onDelete('cascade');

            // Pengurus yang melakukan validasi teknis
            $table->foreignId('validator_id')
                  ->nullable()
                  ->constrained('akun')
                  ->nullOnDelete();

            // DPS yang melakukan validasi syariah
            $table->foreignId('dps_id')
                  ->nullable()
                  ->constrained('akun')
                  ->nullOnDelete();

            // ── Data Evaluasi 14 Hari ──────────────────────────────

            $table->decimal('omzet_sebelumnya', 15, 2)->default(0)
                  ->comment('Omzet dari periode sebelumnya untuk perbandingan');

            $table->decimal('persentase_kenaikan_omzet', 8, 2)->default(0)
                  ->comment('((omzet_sekarang - omzet_sebelumnya) / omzet_sebelumnya) * 100');

            $table->decimal('rasio_biaya', 8, 2)->default(0)
                  ->comment('(biaya_operasional / omzet) * 100');

            // ── Indikator Evaluasi ────────────────────────────────

            $table->boolean('indikator_omzet')->default(false)
                  ->comment('true jika kenaikan omzet >= 5%');

            $table->boolean('indikator_biaya')->default(false)
                  ->comment('true jika rasio biaya < 60%');

            $table->enum('hasil_evaluasi', ['aktif', 'peringatan', 'tidak_aktif'])
                  ->default('peringatan')
                  ->comment('aktif = kedua indikator terpenuhi');

            // ── Validasi Pengurus ─────────────────────────────────

            $table->timestamp('tanggal_validasi_pengurus')->nullable();

            $table->enum('status_validasi_pengurus', ['pending', 'disetujui', 'ditolak'])
                  ->default('pending');

            $table->text('catatan_pengurus')->nullable();

            // ── Validasi DPS (Syariah) ────────────────────────────

            $table->timestamp('tanggal_validasi_dps')->nullable();

            $table->enum('status_validasi_dps', ['pending', 'disetujui', 'ditolak'])
                  ->default('pending');

            $table->text('catatan_dps')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validasi');
    }
};