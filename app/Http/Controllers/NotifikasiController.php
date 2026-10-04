<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotifikasiController extends Controller
{
    /**
     * Daftar notifikasi milik akun yang sedang login.
     */
    public function index(Request $request): View
    {
        $query = Notifikasi::where('akun_id', Auth::id());

        // ── Filter ───────────────────────────────────────────────
        $filter = $request->get('filter', 'semua');
        match ($filter) {
            'belum_dibaca' => $query->belumDibaca(),
            'sudah_dibaca' => $query->sudahDibaca(),
            'hari_ini'     => $query->hariIni(),
            'minggu_ini'   => $query->mingguIni(),
            default        => null,
        };

        // ── Search ───────────────────────────────────────────────
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('judul', 'like', "%{$s}%")
                    ->orWhere('pesan', 'like', "%{$s}%");
            });
        }

        $notifikasiList = $query
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $jumlahBelumDibaca = Notifikasi::where('akun_id', Auth::id())->belumDibaca()->count();

        return view('notifikasi.index', [
            'notifikasiList'    => $notifikasiList,
            'filter'            => $filter,
            'search'            => $request->search,
            'jumlahBelumDibaca' => $jumlahBelumDibaca,
        ]);
    }

    /**
     * Detail satu notifikasi — otomatis menandai dibaca, lalu redirect
     * ke url_aksi jika ada, atau tampilkan halaman detail.
     */
    public function show(Notifikasi $notifikasi): View|RedirectResponse
    {
        $this->cekKepemilikan($notifikasi);

        if (! $notifikasi->dibaca) {
            $notifikasi->update(['dibaca' => true, 'dibaca_pada' => now()]);
        }

        if ($notifikasi->url_aksi) {
            return redirect($notifikasi->url_aksi);
        }

        return view('notifikasi.show', compact('notifikasi'));
    }

    /**
     * Tandai satu notifikasi sebagai sudah dibaca (tanpa redirect ke url_aksi).
     */
    public function read(Notifikasi $notifikasi): RedirectResponse
    {
        $this->cekKepemilikan($notifikasi);

        if (! $notifikasi->dibaca) {
            $notifikasi->update(['dibaca' => true, 'dibaca_pada' => now()]);
        }

        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    /**
     * Tandai seluruh notifikasi milik akun sebagai sudah dibaca.
     */
    public function readAll(): RedirectResponse
    {
        Notifikasi::where('akun_id', Auth::id())
            ->belumDibaca()
            ->update(['dibaca' => true, 'dibaca_pada' => now()]);

        return back()->with('success', 'Semua notifikasi telah ditandai sudah dibaca.');
    }

    /**
     * Endpoint ringan untuk polling navbar (JSON).
     */
    public function pollCount()
    {
        $jumlah = Notifikasi::where('akun_id', Auth::id())->belumDibaca()->count();

        $terbaru = Notifikasi::where('akun_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'judul', 'pesan', 'tipe', 'dibaca', 'created_at'])
            ->map(function ($n) {
                return [
                    'id'     => $n->id,
                    'judul'  => $n->judul,
                    'pesan'  => \Illuminate\Support\Str::limit($n->pesan, 70),
                    'tipe'   => $n->tipe,
                    'dibaca' => $n->dibaca,
                    'waktu'  => $n->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'jumlah'  => $jumlah,
            'terbaru' => $terbaru,
        ]);
    }

    // ── Helper ───────────────────────────────────────────────────

    private function cekKepemilikan(Notifikasi $notifikasi): void
    {
        if ($notifikasi->akun_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke notifikasi ini.');
        }
    }
}
