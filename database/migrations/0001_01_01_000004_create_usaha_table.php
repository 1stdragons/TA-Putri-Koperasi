<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usaha', function (Blueprint $table) {
            $table->id();

            $table->foreignId('anggota_id')
                  ->constrained('anggota')
                  ->onDelete('cascade');

            $table->string('nama_usaha', 150);

            $table->enum('jenis_usaha', [
                'perdagangan',
                'jasa',
                'produksi',
                'pertanian',
                'lainnya',
            ]);

            $table->text('deskripsi')->nullable();
            $table->text('alamat_usaha')->nullable();

            $table->decimal('modal_awal', 15, 2)->default(0)
                  ->comment('Modal awal usaha dalam rupiah');

            $table->date('tanggal_mulai');

            $table->enum('status_usaha', ['aktif', 'tutup', 'vakum'])
                  ->default('aktif');

            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usaha');
    }
};