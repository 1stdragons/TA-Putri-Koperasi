<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AkunSeeder::class,    // Dari Modul 1: admin, pengurus, dps, ketua, anggota
            AnggotaSeeder::class, // Modul 2: data anggota + usaha dummy
        ]);
    }
}