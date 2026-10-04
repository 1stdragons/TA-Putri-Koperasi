<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnggotaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'akun_id'             => ['required', 'exists:akun,id', 'unique:anggota,akun_id'],
            'nik'                 => ['required', 'digits:16', 'unique:anggota,nik'],
            'tempat_lahir'        => ['required', 'string', 'max:100'],
            'tanggal_lahir'       => ['required', 'date', 'before:today'],
            'jenis_kelamin'       => ['required', 'in:L,P'],
            'alamat'              => ['required', 'string', 'max:500'],
            'rt'                  => ['nullable', 'string', 'max:5'],
            'rw'                  => ['nullable', 'string', 'max:5'],
            'kelurahan'           => ['nullable', 'string', 'max:100'],
            'kecamatan'           => ['nullable', 'string', 'max:100'],
            'kabupaten'           => ['nullable', 'string', 'max:100'],
            'provinsi'            => ['nullable', 'string', 'max:100'],
            'telepon'             => ['required', 'string', 'max:20'],
            'pendidikan_terakhir' => ['nullable', 'in:SD,SMP,SMA/SMK,D1,D2,D3,D4,S1,S2,S3'],
            'pekerjaan_sampingan' => ['nullable', 'string', 'max:150'],
            'tanggal_bergabung'   => ['required', 'date'],
            'status_anggota'      => ['required', 'in:aktif,peringatan,tidak_aktif'],
            'catatan'             => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'akun_id.required'            => 'Akun wajib dipilih.',
            'akun_id.exists'              => 'Akun yang dipilih tidak ditemukan.',
            'akun_id.unique'              => 'Akun ini sudah terdaftar sebagai anggota.',
            'nik.required'                => 'NIK wajib diisi.',
            'nik.digits'                  => 'NIK harus berupa 16 digit angka.',
            'nik.unique'                  => 'NIK sudah terdaftar pada anggota lain.',
            'tempat_lahir.required'       => 'Tempat lahir wajib diisi.',
            'tanggal_lahir.required'      => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.before'        => 'Tanggal lahir harus sebelum hari ini.',
            'jenis_kelamin.required'      => 'Jenis kelamin wajib dipilih.',
            'alamat.required'             => 'Alamat wajib diisi.',
            'telepon.required'            => 'Nomor telepon wajib diisi.',
            'tanggal_bergabung.required'  => 'Tanggal bergabung wajib diisi.',
            'status_anggota.required'     => 'Status anggota wajib dipilih.',
            'pendidikan_terakhir.in'      => 'Pendidikan terakhir tidak valid.',
            'jenis_kelamin.in'            => 'Jenis kelamin harus L atau P.',
            'status_anggota.in'           => 'Status anggota tidak valid.',
        ];
    }
}
