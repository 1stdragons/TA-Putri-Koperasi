<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Validator;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'password_lama'          => ['required', 'string'],
            'password_baru'          => ['required', 'string', 'min:8', 'confirmed'],
            'password_baru_confirmation' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'password_lama.required' => 'Password saat ini wajib diisi.',
            'password_baru.required' => 'Password baru wajib diisi.',
            'password_baru.min'      => 'Password baru minimal 8 karakter.',
            'password_baru.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password_baru_confirmation.required' => 'Konfirmasi password baru wajib diisi.',
        ];
    }

    /**
     * Validasi tambahan setelah aturan dasar lolos:
     * 1. Password lama harus cocok dengan hash yang tersimpan.
     * 2. Password baru tidak boleh sama persis dengan password lama.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $akun = Auth::user();

            if (! $akun) {
                return;
            }

            if ($this->filled('password_lama') && ! Hash::check($this->password_lama, $akun->password)) {
                $validator->errors()->add('password_lama', 'Password saat ini yang Anda masukkan salah.');
            }

            if (
                $this->filled('password_lama') &&
                $this->filled('password_baru') &&
                Hash::check($this->password_baru, $akun->password)
            ) {
                $validator->errors()->add('password_baru', 'Password baru tidak boleh sama dengan password saat ini.');
            }
        });
    }
}
