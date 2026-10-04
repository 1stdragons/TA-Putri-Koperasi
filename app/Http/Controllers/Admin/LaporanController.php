<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLaporanRequest;
use App\Http\Requests\Admin\UpdateLaporanRequest;
use App\Models\AktivitasLog;
use App\Models\Laporan;
use App\Models\Usaha;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class LaporanController extends Controller
{
    private const CACHE_PERIODE_KEY = 'admin.laporan.periode-list';

    private array $allowedSorts = [
        'created_at',
        'nama_usaha',
        'omzet',
        'laba_bersih',
        'tanggal_mulai',
    ];

    public function index(Request $request): View
    {
        $query = Laporan::query()
            ->join('usaha', 'laporan.usaha_id', '=', 'usaha.id')
            ->join('anggota', 'usaha.anggota_id', '=', 'anggota.id')
            ->join('akun', 'anggota.akun_id', '=', 'akun.id')
            ->select([
                'laporan.*',
                'usaha.nama_usaha',
                'usaha.jenis_usaha',
                'anggota.nomor_anggota',
                DB::raw('akun.nama as nama_anggota'),
            ]);

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));

            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('usaha.nama_usaha', 'like', "%{$search}%")
                    ->orWhere('akun.nama', 'like', "%{$search}%")
                    ->orWhere('anggota.nomor_anggota', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where(
                'laporan.status_laporan',
                $request->input('status')
            );
        }

        if ($request->filled('jenis_usaha')) {
            $query->where(
                'usaha.jenis_usaha',
                $request->input('jenis_usaha')
            );
        }

        if ($request->filled('periode')) {
            $periode = (string) $request->input('periode');

            if (preg_match('/^\d{4}-\d{2}$/', $periode) === 1) {
                [$tahun, $bulan] = explode('-', $periode);

                $query
                    ->whereYear('laporan.tanggal_mulai', (int) $tahun)
                    ->whereMonth('laporan.tanggal_mulai', (int) $bulan);
            }
        }

        $sort = in_array(
            $request->input('sort'),
            $this->allowedSorts,
            true
        )
            ? $request->input('sort')
            : 'created_at';

        $direction = $request->input('dir') === 'asc'
            ? 'asc'
            : 'desc';

        $sortMap = [
            'nama_usaha' => 'usaha.nama_usaha',
            'omzet' => 'laporan.omzet',
            'laba_bersih' => 'laporan.laba_bersih',
            'created_at' => 'laporan.created_at',
            'tanggal_mulai' => 'laporan.tanggal_mulai',
        ];

        $query->orderBy(
            $sortMap[$sort] ?? 'laporan.created_at',
            $direction
        );

        $laporanList = $query
            ->with('validasi')
            ->paginate(10)
            ->withQueryString();

        $periodeList = Cache::remember(
            self::CACHE_PERIODE_KEY,
            now()->addMinutes(5),
            fn() => Laporan::query()
                ->selectRaw("DATE_FORMAT(tanggal_mulai, '%Y-%m') as periode")
                ->selectRaw("DATE_FORMAT(tanggal_mulai, '%M %Y') as label")
                ->whereNotNull('tanggal_mulai')
                ->groupBy('periode', 'label')
                ->orderByDesc('periode')
                ->pluck('label', 'periode')
        );

        return view('admin.laporan.index', [
            'laporanList' => $laporanList,
            'periodeList' => $periodeList,
            'search' => $request->input('search'),
            'filterStatus' => $request->input('status'),
            'filterJenis' => $request->input('jenis_usaha'),
            'filterPeriode' => $request->input('periode'),
            'sort' => $sort,
            'dir' => $direction,
        ]);
    }

    public function create(): View
    {
        $usahaList = Usaha::query()
            ->select([
                'id',
                'anggota_id',
                'nama_usaha',
                'status_usaha',
            ])
            ->where('status_usaha', 'aktif')
            ->with([
                'anggota:id,akun_id,nomor_anggota',
                'anggota.akun:id,nama',
            ])
            ->orderBy('nama_usaha')
            ->get();

        return view('admin.laporan.create', compact('usahaList'));
    }

    public function store(
        StoreLaporanRequest $request
    ): RedirectResponse {
        $validated = $request->validated();
        $fileBaru = null;

        if ($request->hasFile('file_bukti')) {
            $fileBaru = $this->uploadFileBukti(
                $request->file('file_bukti')
            );
        }

        try {
            $laporan = DB::transaction(function () use (
                $validated,
                $request,
                $fileBaru
            ): Laporan {
                $usaha = Usaha::query()
                    ->whereKey($validated['usaha_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $data = $validated;
                $data['periode_ke'] = $usaha->periode_berikutnya;
                $data['laba_bersih'] =
                    (float) $data['omzet']
                    - (float) $data['biaya_operasional'];

                if ($fileBaru) {
                    $data['file_bukti'] = $fileBaru;
                }

                $laporan = Laporan::create($data);
                $laporan->load('usaha.anggota.akun');

                AktivitasLog::create([
                    'akun_id' => Auth::id(),
                    'aktivitas' => 'Tambah Laporan',
                    'keterangan' => 'Menambahkan laporan periode ke-'
                        . $laporan->periode_ke
                        . ' untuk usaha: '
                        . $laporan->usaha->nama_usaha
                        . ' — anggota: '
                        . $laporan->usaha->anggota->akun->nama,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                return $laporan;
            });
        } catch (Throwable $exception) {
            if ($fileBaru) {
                $this->hapusFileBukti($fileBaru);
            }

            report($exception);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Laporan belum berhasil ditambahkan. Silakan coba kembali.'
                );
        }

        $this->bersihkanCachePeriode();

        return redirect()
            ->route('admin.laporan.index')
            ->with(
                'success',
                'Laporan periode ke-'
                    . $laporan->periode_ke
                    . ' untuk usaha "'
                    . $laporan->usaha->nama_usaha
                    . '" berhasil ditambahkan.'
            );
    }

    public function show(Laporan $laporan): View
    {
        $laporan->load([
            'usaha.anggota.akun',
            'validasi.validator',
            'validasi.dps',
        ]);

        return view('admin.laporan.show', compact('laporan'));
    }

    public function edit(Laporan $laporan): View
    {
        $laporan->load('usaha.anggota.akun');

        $usahaList = Usaha::query()
            ->select([
                'id',
                'anggota_id',
                'nama_usaha',
                'status_usaha',
            ])
            ->where(function ($query) use ($laporan) {
                $query
                    ->where('status_usaha', 'aktif')
                    ->orWhere('id', $laporan->usaha_id);
            })
            ->with([
                'anggota:id,akun_id,nomor_anggota',
                'anggota.akun:id,nama',
            ])
            ->orderBy('nama_usaha')
            ->get();

        return view(
            'admin.laporan.edit',
            compact('laporan', 'usahaList')
        );
    }

    public function update(
        UpdateLaporanRequest $request,
        Laporan $laporan
    ): RedirectResponse {
        $validated = $request->validated();
        $fileLama = $laporan->file_bukti;
        $fileBaru = null;

        if ($request->hasFile('file_bukti')) {
            $fileBaru = $this->uploadFileBukti(
                $request->file('file_bukti')
            );
        }

        try {
            $laporan = DB::transaction(function () use (
                $laporan,
                $validated,
                $request,
                $fileBaru
            ): Laporan {
                $laporanTerkunci = Laporan::query()
                    ->whereKey($laporan->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $data = $validated;
                $data['laba_bersih'] =
                    (float) $data['omzet']
                    - (float) $data['biaya_operasional'];

                if ($fileBaru) {
                    $data['file_bukti'] = $fileBaru;
                }

                $laporanTerkunci->update($data);
                $laporanTerkunci->load('usaha.anggota.akun');

                AktivitasLog::create([
                    'akun_id' => Auth::id(),
                    'aktivitas' => 'Edit Laporan',
                    'keterangan' => 'Memperbarui laporan periode ke-'
                        . $laporanTerkunci->periode_ke
                        . ' usaha: '
                        . $laporanTerkunci->usaha->nama_usaha,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                return $laporanTerkunci;
            });
        } catch (Throwable $exception) {
            if ($fileBaru) {
                $this->hapusFileBukti($fileBaru);
            }

            report($exception);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Laporan belum berhasil diperbarui. File lama tetap aman.'
                );
        }

        if ($fileBaru && $fileLama && $fileLama !== $fileBaru) {
            $this->hapusFileBukti($fileLama);
        }

        $this->bersihkanCachePeriode();

        return redirect()
            ->route('admin.laporan.index')
            ->with('success', 'Laporan berhasil diperbarui.');
    }

    public function destroy(
        Request $request,
        Laporan $laporan
    ): RedirectResponse {
        $laporan->load('usaha');

        $fileBukti = $laporan->file_bukti;
        $info = 'Periode ke-'
            . $laporan->periode_ke
            . ' — '
            . $laporan->usaha->nama_usaha;

        try {
            DB::transaction(function () use (
                $request,
                $laporan,
                $info
            ): void {
                $laporan->delete();

                AktivitasLog::create([
                    'akun_id' => Auth::id(),
                    'aktivitas' => 'Hapus Laporan',
                    'keterangan' => 'Menghapus laporan: ' . $info,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            });
        } catch (Throwable $exception) {
            report($exception);

            return back()->with(
                'error',
                'Laporan belum berhasil dihapus.'
            );
        }

        $this->hapusFileBukti($fileBukti);
        $this->bersihkanCachePeriode();

        return redirect()
            ->route('admin.laporan.index')
            ->with(
                'success',
                'Laporan "' . $info . '" berhasil dihapus.'
            );
    }

    private function uploadFileBukti(
        UploadedFile $file
    ): string {
        $folder = public_path('uploads/laporan');

        if (! is_dir($folder)) {
            mkdir($folder, 0755, true);
        }

        $filename = 'laporan_'
            . Str::random(10)
            . '_'
            . time()
            . '.'
            . strtolower($file->getClientOriginalExtension());

        $file->move($folder, $filename);

        return $filename;
    }

    private function hapusFileBukti(
        ?string $filename
    ): void {
        if (! $filename) {
            return;
        }

        $path = public_path(
            'uploads/laporan/' . $filename
        );

        if (file_exists($path)) {
            @unlink($path);
        }
    }

    private function bersihkanCachePeriode(): void
    {
        Cache::forget(self::CACHE_PERIODE_KEY);
    }
}
