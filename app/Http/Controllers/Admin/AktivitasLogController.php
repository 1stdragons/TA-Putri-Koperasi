<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AktivitasLog;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AktivitasLogController extends Controller
{
    /**
     * Menampilkan seluruh log aktivitas pengguna.
     */
    public function index(Request $request): View
    {
        $query = AktivitasLog::query()
            ->with('akun');

        $this->applySearchFilter($query, $request);
        $this->applyAktivitasFilter($query, $request);
        $this->applyRoleFilter($query, $request);
        $this->applyTanggalFilter($query, $request);

        $aktivitasLogs = $query
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        $daftarAktivitas = AktivitasLog::query()
            ->whereNotNull('aktivitas')
            ->where('aktivitas', '!=', '')
            ->distinct()
            ->orderBy('aktivitas')
            ->pluck('aktivitas');

        return view('admin.aktivitas-log.index', [
            'aktivitasLogs'  => $aktivitasLogs,
            'daftarAktivitas' => $daftarAktivitas,
            'search'          => $request->input('search', ''),
            'filterAktivitas' => $request->input('aktivitas', ''),
            'filterRole'      => $request->input('role', ''),
            'filterTanggal'   => $request->input('tanggal', ''),
        ]);
    }

    /**
     * Filter pencarian berdasarkan pengguna, aktivitas,
     * keterangan, dan alamat IP.
     *
     * @param Builder<AktivitasLog> $query
     */
    private function applySearchFilter(
        Builder $query,
        Request $request
    ): void {
        if (! $request->filled('search')) {
            return;
        }

        $search = trim((string) $request->input('search'));

        $query->where(function (Builder $subQuery) use ($search): void {
            $subQuery
                ->where('aktivitas', 'like', "%{$search}%")
                ->orWhere('keterangan', 'like', "%{$search}%")
                ->orWhere('ip_address', 'like', "%{$search}%")
                ->orWhereHas('akun', function (Builder $akunQuery) use ($search): void {
                    $akunQuery
                        ->where('nama', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
        });
    }

    /**
     * Filter berdasarkan jenis aktivitas.
     *
     * @param Builder<AktivitasLog> $query
     */
    private function applyAktivitasFilter(
        Builder $query,
        Request $request
    ): void {
        if (! $request->filled('aktivitas')) {
            return;
        }

        $query->where(
            'aktivitas',
            (string) $request->input('aktivitas')
        );
    }

    /**
     * Filter berdasarkan role pengguna.
     *
     * @param Builder<AktivitasLog> $query
     */
    private function applyRoleFilter(
        Builder $query,
        Request $request
    ): void {
        if (! $request->filled('role')) {
            return;
        }

        $role = (string) $request->input('role');

        $query->whereHas(
            'akun',
            function (Builder $akunQuery) use ($role): void {
                $akunQuery->where('role', $role);
            }
        );
    }

    /**
     * Filter berdasarkan tanggal aktivitas.
     *
     * @param Builder<AktivitasLog> $query
     */
    private function applyTanggalFilter(
        Builder $query,
        Request $request
    ): void {
        if (! $request->filled('tanggal')) {
            return;
        }

        $query->whereDate(
            'created_at',
            (string) $request->input('tanggal')
        );
    }
}
