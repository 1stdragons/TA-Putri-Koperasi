<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAkunRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $akunId = $this->route('akun')->id;

        return [
            'nama'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:100', Rule::unique('akun', 'email')->ignore($akunId)],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'role'     => ['required', 'in:admin,anggota,pengurus,dps,ketua'],
            'status'   => ['required', 'in:aktif,nonaktif'],
            'foto'     => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required'      => 'Nama wajib diisi.',
            'nama.max'           => 'Nama maksimal 100 karakter.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email sudah digunakan akun lain.',
            'password.min'       => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
            'role.required'      => 'Role wajib dipilih.',
            'role.in'            => 'Role tidak valid.',
            'status.required'    => 'Status wajib dipilih.',
            'status.in'          => 'Status tidak valid.',
            'foto.image'         => 'File harus berupa gambar.',
            'foto.mimes'         => 'Format gambar harus jpg, jpeg, png, atau webp.',
            'foto.max'           => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}