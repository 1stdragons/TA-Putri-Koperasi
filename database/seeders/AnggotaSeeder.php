<?php

namespace Database\Seeders;

use App\Models\Akun;
use App\Models\Anggota;
use App\Models\Usaha;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AnggotaSeeder extends Seeder
{
    public function run(): void
    {
        // ── Data anggota dummy ──────────────────────────────────
        $dataAnggota = [
            [
                'akun' => [
                    'nama'     => 'Siti Rahma Wati',
                    'email'    => 'siti@koperasi.com',
                    'password' => Hash::make('anggota123'),
                    'role'     => 'anggota',
                    'status'   => 'aktif',
                ],
                'anggota' => [
                    'nomor_anggota'      => 'PKM-0001',
                    'nik'                => '1171010101900001',
                    'tempat_lahir'       => 'Banda Aceh',
                    'tanggal_lahir'      => '1990-01-15',
                    'jenis_kelamin'      => 'P',
                    'alamat'             => 'Jl. Teuku Umar No. 12',
                    'rt'                 => '003',
                    'rw'                 => '002',
                    'kelurahan'          => 'Kampung Baru',
                    'kecamatan'          => 'Baiturrahman',
                    'kabupaten'          => 'Kota Banda Aceh',
                    'provinsi'           => 'Aceh',
                    'telepon'            => '081234560001',
                    'pendidikan_terakhir' => 'S1',
                    'pekerjaan_sampingan' => null,
                    'tanggal_bergabung'  => '2022-03-01',
                    'status_anggota'     => 'aktif',
                ],
                'usaha' => [
                    'nama_usaha'    => 'Warung Kue Siti',
                    'jenis_usaha'   => 'perdagangan',
                    'deskripsi'     => 'Menjual kue tradisional dan kue kering khas Aceh',
                    'alamat_usaha'  => 'Pasar Aceh, Jl. Cut Nyak Dhien No. 5',
                    'modal_awal'    => 5000000,
                    'tanggal_mulai' => '2022-03-15',
                    'status_usaha'  => 'aktif',
                ],
            ],
            [
                'akun' => [
                    'nama'     => 'Muhammad Fauzi',
                    'email'    => 'fauzi@koperasi.com',
                    'password' => Hash::make('anggota123'),
                    'role'     => 'anggota',
                    'status'   => 'aktif',
                ],
                'anggota' => [
                    'nomor_anggota'      => 'PKM-0002',
                    'nik'                => '1171010201880002',
                    'tempat_lahir'       => 'Lhokseumawe',
                    'tanggal_lahir'      => '1988-06-20',
                    'jenis_kelamin'      => 'L',
                    'alamat'             => 'Jl. Diponegoro No. 45',
                    'rt'                 => '001',
                    'rw'                 => '004',
                    'kelurahan'          => 'Peunayong',
                    'kecamatan'          => 'Kuta Alam',
                    'kabupaten'          => 'Kota Banda Aceh',
                    'provinsi'           => 'Aceh',
                    'telepon'            => '081234560002',
                    'pendidikan_terakhir' => 'SMA/SMK',
                    'pekerjaan_sampingan' => 'Ojek Online',
                    'tanggal_bergabung'  => '2022-05-10',
                    'status_anggota'     => 'aktif',
                ],
                'usaha' => [
                    'nama_usaha'    => 'Bengkel Las Fauzi',
                    'jenis_usaha'   => 'jasa',
                    'deskripsi'     => 'Bengkel las dan fabricasi logam ringan',
                    'alamat_usaha'  => 'Jl. Diponegoro No. 47, Banda Aceh',
                    'modal_awal'    => 15000000,
                    'tanggal_mulai' => '2022-06-01',
                    'status_usaha'  => 'aktif',
                ],
            ],
            [
                'akun' => [
                    'nama'     => 'Nurhaliza Putri',
                    'email'    => 'nurhaliza@koperasi.com',
                    'password' => Hash::make('anggota123'),
                    'role'     => 'anggota',
                    'status'   => 'aktif',
                ],
                'anggota' => [
                    'nomor_anggota'      => 'PKM-0003',
                    'nik'                => '1171010301920003',
                    'tempat_lahir'       => 'Sigli',
                    'tanggal_lahir'      => '1992-11-08',
                    'jenis_kelamin'      => 'P',
                    'alamat'             => 'Jl. Sri Ratu Safiatuddin No. 7',
                    'rt'                 => '002',
                    'rw'                 => '001',
                    'kelurahan'          => 'Lampriet',
                    'kecamatan'          => 'Kuta Alam',
                    'kabupaten'          => 'Kota Banda Aceh',
                    'provinsi'           => 'Aceh',
                    'telepon'            => '081234560003',
                    'pendidikan_terakhir' => 'D3',
                    'pekerjaan_sampingan' => null,
                    'tanggal_bergabung'  => '2023-01-15',
                    'status_anggota'     => 'peringatan',
                ],
                'usaha' => [
                    'nama_usaha'    => 'Konveksi Nurhaliza',
                    'jenis_usaha'   => 'produksi',
                    'deskripsi'     => 'Produksi baju muslim dan seragam sekolah',
                    'alamat_usaha'  => 'Jl. Sri Ratu Safiatuddin No. 9, Banda Aceh',
                    'modal_awal'    => 20000000,
                    'tanggal_mulai' => '2023-02-01',
                    'status_usaha'  => 'aktif',
                ],
            ],
        ];

        foreach ($dataAnggota as $data) {
            // Buat atau update akun
            $akun = Akun::updateOrCreate(
                ['email' => $data['akun']['email']],
                $data['akun']
            );

            // Buat atau update data anggota
            $anggota = Anggota::updateOrCreate(
                ['akun_id' => $akun->id],
                array_merge($data['anggota'], ['akun_id' => $akun->id])
            );

            // Buat usaha jika belum ada
            Usaha::updateOrCreate(
                [
                    'anggota_id' => $anggota->id,
                    'nama_usaha' => $data['usaha']['nama_usaha'],
                ],
                array_merge($data['usaha'], ['anggota_id' => $anggota->id])
            );
        }
    }
}