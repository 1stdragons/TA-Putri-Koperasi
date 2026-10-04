<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggota', function (Blueprint $table) {
            $table->id();

            $table->foreignId('akun_id')
                  ->unique()
                  ->constrained('akun')
                  ->onDelete('cascade');

            $table->string('nomor_anggota', 20)->unique()
                  ->comment('Format: PKM-0001');

            $table->string('nik', 16)->unique()
                  ->comment('Nomor Induk Kependudukan');

            $table->string('tempat_lahir', 100);
            $table->date('tanggal_lahir');

            $table->enum('jenis_kelamin', ['L', 'P'])
                  ->comment('L = Laki-laki, P = Perempuan');

            $table->text('alamat');
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('kelurahan', 100)->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->string('kabupaten', 100)->nullable();
            $table->string('provinsi', 100)->nullable();

            $table->string('telepon', 20);

            $table->enum('pendidikan_terakhir', [
                'SD', 'SMP', 'SMA/SMK', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'
            ])->nullable();

            $table->string('pekerjaan_sampingan', 150)->nullable();

            $table->date('tanggal_bergabung');

            $table->enum('status_anggota', ['aktif', 'peringatan', 'tidak_aktif'])
                  ->default('aktif');

            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota');
    }
};