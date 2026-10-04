<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateProfilRequest extends FormRequest
{
    /**
     * Otorisasi: pengguna hanya boleh mengedit profilnya sendiri.
     * Karena controller selalu mengambil Auth::user() (bukan dari parameter URL),
     * cukup pastikan pengguna sudah login.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        $akunId = Auth::id();

        return [
            'nama'  => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('akun', 'email')->ignore($akunId),
            ],
            'foto' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required'  => 'Nama wajib diisi.',
            'nama.max'       => 'Nama maksimal 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email ini sudah digunakan oleh akun lain.',
            'foto.file'      => 'Foto profil harus berupa file.',
            'foto.mimes'     => 'Format foto harus JPG, JPEG, PNG, atau WEBP.',
            'foto.max'       => 'Ukuran foto maksimal 2 MB.',
        ];
    }
}
