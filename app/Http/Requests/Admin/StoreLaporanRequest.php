<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreLaporanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'usaha_id'          => ['required', 'exists:usaha,id'],
            'tanggal_mulai'     => ['required', 'date'],
            'tanggal_selesai'   => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'omzet'             => ['required', 'numeric', 'min:0'],
            'biaya_operasional' => ['required', 'numeric', 'min:0'],
            'keterangan'        => ['nullable', 'string', 'max:1000'],
            'file_bukti'        => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'status_laporan'    => ['required', 'in:draft,dikirim,divalidasi,ditolak'],
        ];
    }

    public function messages(): array
    {
        return [
            'usaha_id.required'          => 'Usaha wajib dipilih.',
            'usaha_id.exists'            => 'Usaha yang dipilih tidak ditemukan.',
            'tanggal_mulai.required'     => 'Tanggal mulai periode wajib diisi.',
            'tanggal_mulai.date'         => 'Format tanggal mulai tidak valid.',
            'tanggal_selesai.required'   => 'Tanggal selesai periode wajib diisi.',
            'tanggal_selesai.date'       => 'Format tanggal selesai tidak valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'omzet.required'             => 'Omzet wajib diisi.',
            'omzet.numeric'              => 'Omzet harus berupa angka.',
            'omzet.min'                  => 'Omzet tidak boleh negatif.',
            'biaya_operasional.required' => 'Biaya operasional wajib diisi.',
            'biaya_operasional.numeric'  => 'Biaya operasional harus berupa angka.',
            'biaya_operasional.min'      => 'Biaya operasional tidak boleh negatif.',
            'file_bukti.file'            => 'File bukti harus berupa dokumen atau gambar.',
            'file_bukti.mimes'           => 'Format bukti laporan harus PDF, JPG, JPEG, atau PNG.',
            'file_bukti.max'             => 'Ukuran file bukti maksimal 5 MB.',
            'status_laporan.required'    => 'Status laporan wajib dipilih.',
            'status_laporan.in'          => 'Status laporan tidak valid.',
        ];
    }
}
