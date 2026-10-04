<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUsahaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'anggota_id'     => ['required', 'exists:anggota,id'],
            'nama_usaha'     => ['required', 'string', 'max:255'],
            'jenis_usaha'    => ['required', 'in:perdagangan,jasa,produksi,pertanian,lainnya'],
            'jenis_akad'     => ['required', 'in:Mudharabah,Musyarakah,Murabahah,Ijarah,Qardh,Lainnya'],
            'file_akad'      => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'deskripsi'      => ['nullable', 'string'],
            'alamat_usaha'   => ['nullable', 'string', 'max:500'],
            'modal_awal'     => ['required', 'numeric', 'min:0'],
            'tanggal_mulai'  => ['nullable', 'date'],
            'status_usaha'   => ['required', 'in:belum_aktif,aktif,vakum,tutup'],
            'catatan'        => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'anggota_id.required'   => 'Anggota wajib dipilih.',
            'anggota_id.exists'     => 'Anggota yang dipilih tidak ditemukan.',
            'nama_usaha.required'   => 'Nama usaha wajib diisi.',
            'nama_usaha.max'        => 'Nama usaha maksimal 255 karakter.',
            'jenis_usaha.required'  => 'Jenis usaha wajib dipilih.',
            'jenis_usaha.in'        => 'Jenis usaha tidak valid.',
            'jenis_akad.required'   => 'Jenis akad wajib dipilih.',
            'jenis_akad.in'         => 'Jenis akad tidak valid.',
            'file_akad.file'        => 'File yang diupload harus berupa dokumen.',
            'file_akad.mimes'       => 'Format dokumen akad harus PDF, DOC, atau DOCX.',
            'file_akad.max'         => 'Ukuran dokumen akad maksimal 5 MB.',
            'modal_awal.required'   => 'Modal awal wajib diisi.',
            'modal_awal.numeric'    => 'Modal awal harus berupa angka.',
            'modal_awal.min'        => 'Modal awal tidak boleh negatif.',
            'tanggal_mulai.date'    => 'Format tanggal mulai tidak valid.',
            'status_usaha.required' => 'Status usaha wajib dipilih.',
            'status_usaha.in'       => 'Status usaha tidak valid.',
            'catatan.max'           => 'Catatan maksimal 1000 karakter.',
        ];
    }
}
