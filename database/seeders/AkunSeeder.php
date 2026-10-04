<?php

namespace Database\Seeders;

use App\Models\Akun;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AkunSeeder extends Seeder
{
    public function run(): void
    {
        $akun = [
            [
                'nama'     => 'Administrator',
                'email'    => 'admin@koperasi.com',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
                'status'   => 'aktif',
            ],
            [
                'nama'     => 'Budi Pengurus',
                'email'    => 'pengurus@koperasi.com',
                'password' => Hash::make('pengurus123'),
                'role'     => 'pengurus',
                'status'   => 'aktif',
            ],
            [
                'nama'     => 'Ustaz Syariah',
                'email'    => 'dps@koperasi.com',
                'password' => Hash::make('dps12345'),
                'role'     => 'dps',
                'status'   => 'aktif',
            ],
            [
                'nama'     => 'H. Ketua Koperasi',
                'email'    => 'ketua@koperasi.com',
                'password' => Hash::make('ketua123'),
                'role'     => 'ketua',
                'status'   => 'aktif',
            ],
            [
                'nama'     => 'Siti Anggota',
                'email'    => 'anggota@koperasi.com',
                'password' => Hash::make('anggota123'),
                'role'     => 'anggota',
                'status'   => 'aktif',
            ],
        ];

        foreach ($akun as $data) {
            Akun::updateOrCreate(['email' => $data['email']], $data);
        }
    }
}