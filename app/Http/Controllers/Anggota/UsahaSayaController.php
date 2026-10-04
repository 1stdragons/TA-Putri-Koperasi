<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Http\Requests\Anggota\StoreUsahaRequest;
use App\Models\AktivitasLog;
use App\Models\Usaha;
use App\Models\ValidasiAkad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class UsahaSayaController extends Controller
{
    public function index(): View
    {
        $anggota = Auth::user()?->anggota;

        if (! $anggota) {
            return view('anggota.usaha-saya.index', [
                'usahaList'        => collect(),
                'bolehTambahUsaha' => false,
                'jumlahUsahaAktif' => 0,
            ]);
        }

        $usahaList = Usaha::query()
            ->where('anggota_id', $anggota->id)
            ->with([
                'laporan',
                'validasiAkadTerakhir.dps',
            ])
            ->withCount('laporan')
            ->orderByRaw("
                CASE status_usaha
                    WHEN 'aktif' THEN 1
                    WHEN 'belum_aktif' THEN 2
                    WHEN 'vakum' THEN 3
                    WHEN 'tutup' THEN 4
                    ELSE 5
                END
            ")
            ->orderBy('nama_usaha')
            ->get();

        $jumlahUsahaAktif = $usahaList
            ->where('status_usaha', 'aktif')
            ->count();

        $bolehTambahUsaha = $jumlahUsahaAktif > 0;

        return view('anggota.usaha-saya.index', compact(
            'usahaList',
            'bolehTambahUsaha',
            'jumlahUsahaAktif'
        ));
    }

    public function create(): View|RedirectResponse
    {
        $anggota = Auth::user()?->anggota;

        if (! $anggota) {
            return redirect()
                ->route('anggota.usaha-saya.index')
                ->with('error', 'Data anggota untuk akun Anda belum tersedia.');
        }

        if (! $this->anggotaPunyaUsahaAktif($anggota->id)) {
            return redirect()
                ->route('anggota.usaha-saya.index')
                ->with(
                    'error',
                    'Anda belum dapat menambahkan usaha baru. Usaha pertama harus didaftarkan oleh Admin dan disetujui DPS terlebih dahulu.'
                );
        }

        return view('anggota.usaha-saya.create');
    }

    public function store(StoreUsahaRequest $request): RedirectResponse
    {
        $anggota = Auth::user()?->anggota;

        if (! $anggota) {
            return redirect()
                ->route('anggota.usaha-saya.index')
                ->with('error', 'Data anggota untuk akun Anda belum tersedia.');
        }

        if (! $this->anggotaPunyaUsahaAktif($anggota->id)) {
            return redirect()
                ->route('anggota.usaha-saya.index')
                ->with(
                    'error',
                    'Anda belum dapat menambahkan usaha baru karena belum memiliki usaha aktif.'
                );
        }

        $fileAkad = $request->file('file_akad');

        if (! $fileAkad instanceof UploadedFile) {
            return back()
                ->withInput()
                ->with('error', 'Dokumen akad tidak ditemukan atau gagal diunggah.');
        }

        $namaFileAkad = null;

        try {
            $namaFileAkad = $this->uploadFileAkad($fileAkad);

            $usaha = DB::transaction(function () use (
                $request,
                $anggota,
                $namaFileAkad
            ): Usaha {
                $data = $request->validated();

                $data['anggota_id'] = $anggota->id;
                $data['status_usaha'] = 'belum_aktif';
                $data['status_validasi_syariah'] = 'menunggu';
                $data['tanggal_aktivasi_monitoring'] = null;
                $data['file_akad'] = $namaFileAkad;

                $usaha = Usaha::create($data);

                ValidasiAkad::buatIterasiBaru(
                    $usaha->id,
                    $usaha->file_akad,
                    $usaha->jenis_akad
                );

                AktivitasLog::create([
                    'akun_id' => Auth::id(),
                    'aktivitas' => 'Tambah Usaha Mandiri',
                    'keterangan' => 'Mengajukan usaha baru: '
                        . $usaha->nama_usaha
                        . ' untuk validasi akad oleh DPS.',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                return $usaha;
            });
        } catch (Throwable $exception) {
            if ($namaFileAkad) {
                $this->hapusFileAkad($namaFileAkad);
            }

            report($exception);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Usaha belum berhasil diajukan. Silakan coba kembali.'
                );
        }

        return redirect()
            ->route('anggota.usaha-saya.show', $usaha)
            ->with(
                'success',
                'Usaha berhasil diajukan. Status usaha masih Belum Aktif dan dokumen akad sedang menunggu validasi DPS.'
            );
    }

    public function show(Usaha $usaha): View|RedirectResponse
    {
        $anggota = Auth::user()?->anggota;

        if (! $anggota || (int) $usaha->anggota_id !== (int) $anggota->id) {
            return redirect()
                ->route('anggota.usaha-saya.index')
                ->with('error', 'Anda tidak memiliki akses ke usaha ini.');
        }

        $usaha->load([
            'anggota.akun',
            'laporan' => fn($query) => $query
                ->with('validasi')
                ->orderByDesc('periode_ke'),
            'validasiAkad.dps',
            'validasiAkadTerakhir.dps',
        ]);

        $jumlahLaporan = $usaha->laporan->count();
        $laporanAktif = $usaha->laporan->first();

        return view('anggota.usaha-saya.show', compact(
            'usaha',
            'jumlahLaporan',
            'laporanAktif'
        ));
    }

    private function anggotaPunyaUsahaAktif(int $anggotaId): bool
    {
        return Usaha::query()
            ->where('anggota_id', $anggotaId)
            ->where('status_usaha', 'aktif')
            ->where('status_validasi_syariah', 'disetujui')
            ->exists();
    }

    private function uploadFileAkad(UploadedFile $file): string
    {
        $folder = public_path('uploads/akad');

        if (! is_dir($folder)) {
            mkdir($folder, 0755, true);
        }

        $filename = 'akad_anggota_'
            . Str::random(12)
            . '_'
            . time()
            . '.'
            . strtolower($file->getClientOriginalExtension());

        $file->move($folder, $filename);

        return $filename;
    }

    private function hapusFileAkad(?string $filename): void
    {
        if (! $filename) {
            return;
        }

        $path = public_path('uploads/akad/' . $filename);

        if (file_exists($path)) {
            @unlink($path);
        }
    }
}
