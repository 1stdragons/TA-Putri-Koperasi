<?php

namespace App\Http\Requests\Anggota;

use App\Models\Usaha;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class StoreUsahaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check()
            && Auth::user()?->anggota !== null;
    }

    public function rules(): array
    {
        return [
            'nama_usaha' => [
                'required',
                'string',
                'max:150',
            ],
            'jenis_usaha' => [
                'required',
                'in:perdagangan,jasa,produksi,pertanian,lainnya',
            ],
            'deskripsi' => [
                'nullable',
                'string',
                'max:3000',
            ],
            'alamat_usaha' => [
                'required',
                'string',
                'max:2000',
            ],
            'modal_awal' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999999999.99',
            ],
            'tanggal_mulai' => [
                'required',
                'date',
                'before_or_equal:today',
            ],
            'jenis_akad' => [
                'required',
                'in:Mudharabah,Musyarakah,Murabahah,Ijarah,Qardh,Lainnya',
            ],
            'file_akad' => [
                'required',
                'file',
                'mimes:pdf,doc,docx',
                'max:5120',
            ],
            'catatan' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $anggota = Auth::user()?->anggota;

                if (! $anggota) {
                    $validator->errors()->add(
                        'nama_usaha',
                        'Data anggota untuk akun Anda belum tersedia.'
                    );

                    return;
                }

                $punyaUsahaAktif = Usaha::query()
                    ->where('anggota_id', $anggota->id)
                    ->where('status_usaha', 'aktif')
                    ->where('status_validasi_syariah', 'disetujui')
                    ->exists();

                if (! $punyaUsahaAktif) {
                    $validator->errors()->add(
                        'nama_usaha',
                        'Anda belum dapat menambahkan usaha baru karena belum memiliki usaha aktif yang telah disetujui DPS.'
                    );
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'nama_usaha.required' => 'Nama usaha wajib diisi.',
            'nama_usaha.string' => 'Nama usaha harus berupa teks.',
            'nama_usaha.max' => 'Nama usaha maksimal 150 karakter.',
            'jenis_usaha.required' => 'Jenis usaha wajib dipilih.',
            'jenis_usaha.in' => 'Jenis usaha yang dipilih tidak valid.',
            'deskripsi.string' => 'Deskripsi usaha harus berupa teks.',
            'deskripsi.max' => 'Deskripsi usaha maksimal 3000 karakter.',
            'alamat_usaha.required' => 'Alamat usaha wajib diisi.',
            'alamat_usaha.string' => 'Alamat usaha harus berupa teks.',
            'alamat_usaha.max' => 'Alamat usaha maksimal 2000 karakter.',
            'modal_awal.required' => 'Modal awal wajib diisi.',
            'modal_awal.numeric' => 'Modal awal harus berupa angka.',
            'modal_awal.min' => 'Modal awal tidak boleh bernilai negatif.',
            'modal_awal.max' => 'Nilai modal awal terlalu besar.',
            'tanggal_mulai.required' => 'Tanggal mulai usaha wajib diisi.',
            'tanggal_mulai.date' => 'Format tanggal mulai usaha tidak valid.',
            'tanggal_mulai.before_or_equal' => 'Tanggal mulai usaha tidak boleh melewati hari ini.',
            'jenis_akad.required' => 'Jenis akad syariah wajib dipilih.',
            'jenis_akad.in' => 'Jenis akad syariah yang dipilih tidak valid.',
            'file_akad.required' => 'Dokumen akad wajib diunggah.',
            'file_akad.file' => 'Dokumen akad yang diunggah tidak valid.',
            'file_akad.mimes' => 'Dokumen akad harus berformat PDF, DOC, atau DOCX.',
            'file_akad.max' => 'Ukuran dokumen akad maksimal 5 MB.',
            'catatan.string' => 'Catatan harus berupa teks.',
            'catatan.max' => 'Catatan maksimal 2000 karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nama_usaha' => 'nama usaha',
            'jenis_usaha' => 'jenis usaha',
            'deskripsi' => 'deskripsi usaha',
            'alamat_usaha' => 'alamat usaha',
            'modal_awal' => 'modal awal',
            'tanggal_mulai' => 'tanggal mulai usaha',
            'jenis_akad' => 'jenis akad syariah',
            'file_akad' => 'dokumen akad',
            'catatan' => 'catatan',
        ];
    }
}
