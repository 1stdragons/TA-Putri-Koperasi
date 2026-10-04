<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('akun_id')
                  ->constrained('akun')
                  ->onDelete('cascade');

            $table->string('judul', 150);
            $table->text('pesan');

            $table->enum('tipe', ['info', 'sukses', 'peringatan', 'bahaya'])
                  ->default('info');

            $table->string('url_aksi')->nullable()
                  ->comment('URL tujuan jika notifikasi diklik');

            $table->boolean('dibaca')->default(false);

            $table->timestamp('dibaca_pada')->nullable();

            $table->timestamps();

            // Index untuk query cepat notifikasi belum dibaca per akun
            $table->index(['akun_id', 'dibaca']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};