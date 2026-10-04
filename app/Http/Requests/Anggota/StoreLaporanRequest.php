<?php

namespace App\Http\Requests\Anggota;

use App\Models\Usaha;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class StoreLaporanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check()
            && Auth::user()?->anggota !== null;
    }

    public function rules(): array
    {
        return [
            'usaha_id' => [
                'required',
                'integer',
                'exists:usaha,id',
            ],
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

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $anggota = Auth::user()?->anggota;

                if (! $anggota) {
                    $validator->errors()->add(
                        'usaha_id',
                        'Data anggota untuk akun Anda tidak ditemukan.'
                    );

                    return;
                }

                $usahaId = $this->integer('usaha_id');

                if (! $usahaId) {
                    return;
                }

                $usaha = Usaha::query()
                    ->whereKey($usahaId)
                    ->where('anggota_id', $anggota->id)
                    ->first();

                if (! $usaha) {
                    $validator->errors()->add(
                        'usaha_id',
                        'Usaha yang dipilih bukan milik Anda.'
                    );

                    return;
                }

                if ($usaha->status_validasi_syariah !== 'disetujui') {
                    $validator->errors()->add(
                        'usaha_id',
                        'Akad usaha belum disetujui DPS sehingga monitoring belum dapat dimulai.'
                    );

                    return;
                }

                if ($usaha->status_usaha !== 'aktif') {
                    $validator->errors()->add(
                        'usaha_id',
                        'Laporan hanya dapat dibuat untuk usaha yang berstatus aktif.'
                    );

                    return;
                }

                if (! $usaha->tanggal_aktivasi_monitoring) {
                    $validator->errors()->add(
                        'periode',
                        'Tanggal aktivasi monitoring belum tersedia. Hubungi pengurus atau DPS.'
                    );

                    return;
                }

                if (! $usaha->boleh_membuat_laporan_baru) {
                    $validator->errors()->add(
                        'periode',
                        $usaha->pesan_periode_monitoring
                    );
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'usaha_id.required' => 'Usaha wajib dipilih.',
            'usaha_id.integer' => 'Usaha yang dipilih tidak valid.',
            'usaha_id.exists' => 'Usaha yang dipilih tidak ditemukan.',

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
            'usaha_id' => 'usaha',
            'omzet' => 'omzet',
            'biaya_operasional' => 'biaya operasional',
            'keterangan' => 'keterangan',
            'file_bukti' => 'bukti laporan',
        ];
    }
}
