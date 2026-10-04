<?php

namespace App\Http\Requests\Anggota;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateLaporanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check()
            && Auth::user()?->anggota !== null;
    }

    public function rules(): array
    {
        return [
            'omzet' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999999999.99',
            ],
            'biaya_operasional' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999999999.99',
            ],
            'keterangan' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'file_bukti' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'omzet.required' => 'Omzet wajib diisi.',
            'omzet.numeric' => 'Omzet harus berupa angka.',
            'omzet.min' => 'Omzet tidak boleh bernilai negatif.',
            'omzet.max' => 'Nilai omzet terlalu besar.',

            'biaya_operasional.required' => 'Biaya operasional wajib diisi.',
            'biaya_operasional.numeric' => 'Biaya operasional harus berupa angka.',
            'biaya_operasional.min' => 'Biaya operasional tidak boleh bernilai negatif.',
            'biaya_operasional.max' => 'Nilai biaya operasional terlalu besar.',

            'keterangan.string' => 'Keterangan harus berupa teks.',
            'keterangan.max' => 'Keterangan maksimal 1000 karakter.',

            'file_bukti.file' => 'Bukti laporan yang diunggah tidak valid.',
            'file_bukti.mimes' => 'Format bukti harus PDF, JPG, JPEG, atau PNG.',
            'file_bukti.max' => 'Ukuran bukti laporan maksimal 5 MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'omzet' => 'omzet',
            'biaya_operasional' => 'biaya operasional',
            'keterangan' => 'keterangan',
            'file_bukti' => 'bukti laporan',
        ];
    }
}
