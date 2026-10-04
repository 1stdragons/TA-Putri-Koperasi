<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\Laporan\RekapMonitoringController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\AkunController;
use App\Http\Controllers\Admin\AnggotaController;
use App\Http\Controllers\Admin\UsahaController;
use App\Http\Controllers\Admin\LaporanController as AdminLaporanController;
use App\Http\Controllers\Admin\EvaluasiController as AdminEvaluasiController;
use App\Http\Controllers\Admin\AktivitasLogController;

use App\Http\Controllers\Anggota\DashboardController as AnggotaDashboard;
use App\Http\Controllers\Anggota\UsahaSayaController;
use App\Http\Controllers\Anggota\LaporanController as AnggotaLaporanController;
use App\Http\Controllers\Anggota\EvaluasiController as AnggotaEvaluasiController;
use App\Http\Controllers\Anggota\RekapLaporanController;

use App\Http\Controllers\Pengurus\DashboardController as PengurusDashboard;
use App\Http\Controllers\Pengurus\ValidasiLaporanController;
use App\Http\Controllers\Pengurus\EvaluasiController as PengurusEvaluasiController;

use App\Http\Controllers\Dps\DashboardController as DpsDashboard;
use App\Http\Controllers\Dps\ValidasiSyariahController;
use App\Http\Controllers\Dps\RekapSyariahController;
use App\Http\Controllers\Dps\DataUsahaController;

use App\Http\Controllers\Ketua\DashboardController as KetuaDashboard;
use App\Http\Controllers\Ketua\MonitoringController as KetuaMonitoringController;

/*
|--------------------------------------------------------------------------
| Autentikasi
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get(
        '/',
        [LoginController::class, 'showLoginForm']
    )->name('login');

    Route::get(
        '/login',
        [LoginController::class, 'showLoginForm']
    );

    Route::post(
        '/login',
        [LoginController::class, 'login']
    )->name('login.post');
});

Route::post(
    '/logout',
    [LoginController::class, 'logout']
)
    ->name('logout')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| Notifikasi
|--------------------------------------------------------------------------
|
| Digunakan oleh seluruh pengguna yang sudah login.
|
*/

Route::middleware(['auth'])->group(function () {
    Route::prefix('notifikasi')
        ->name('notifikasi.')
        ->group(function () {
            Route::get(
                '/',
                [NotifikasiController::class, 'index']
            )->name('index');

            Route::get(
                '/poll',
                [NotifikasiController::class, 'pollCount']
            )->name('poll');

            Route::get(
                '/{notifikasi}',
                [NotifikasiController::class, 'show']
            )->name('show');

            Route::post(
                '/{notifikasi}/read',
                [NotifikasiController::class, 'read']
            )->name('read');

            Route::post(
                '/read-all',
                [NotifikasiController::class, 'readAll']
            )->name('read-all');
        });
});

/*
|--------------------------------------------------------------------------
| Profil Pengguna
|--------------------------------------------------------------------------
|
| Digunakan oleh seluruh pengguna yang sudah login.
|
*/

Route::middleware(['auth'])->group(function () {
    Route::prefix('profil')
        ->name('profil.')
        ->group(function () {
            Route::get(
                '/',
                [ProfilController::class, 'index']
            )->name('index');

            Route::get(
                '/edit',
                [ProfilController::class, 'edit']
            )->name('edit');

            Route::put(
                '/',
                [ProfilController::class, 'update']
            )->name('update');

            Route::get(
                '/password',
                [ProfilController::class, 'editPassword']
            )->name('password.edit');

            Route::put(
                '/password',
                [ProfilController::class, 'updatePassword']
            )->name('password.update');

            Route::delete(
                '/foto',
                [ProfilController::class, 'destroyFoto']
            )->name('foto.destroy');
        });
});

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get(
            '/dashboard',
            [AdminDashboard::class, 'index']
        )->name('dashboard');

        Route::get(
            '/aktivitas-log',
            [AktivitasLogController::class, 'index']
        )->name('aktivitas-log.index');

        /*
         * Akun.
         */
        Route::prefix('akun')
            ->name('akun.')
            ->group(function () {
                Route::get(
                    '/',
                    [AkunController::class, 'index']
                )->name('index');

                Route::get(
                    '/create',
                    [AkunController::class, 'create']
                )->name('create');

                Route::post(
                    '/',
                    [AkunController::class, 'store']
                )->name('store');

                Route::get(
                    '/{akun}',
                    [AkunController::class, 'show']
                )->name('show');

                Route::get(
                    '/{akun}/edit',
                    [AkunController::class, 'edit']
                )->name('edit');

                Route::put(
                    '/{akun}',
                    [AkunController::class, 'update']
                )->name('update');

                Route::delete(
                    '/{akun}',
                    [AkunController::class, 'destroy']
                )->name('destroy');
            });

        /*
         * Anggota.
         */
        Route::prefix('anggota')
            ->name('anggota.')
            ->group(function () {
                Route::get(
                    '/',
                    [AnggotaController::class, 'index']
                )->name('index');

                Route::get(
                    '/create',
                    [AnggotaController::class, 'create']
                )->name('create');

                Route::post(
                    '/',
                    [AnggotaController::class, 'store']
                )->name('store');

                Route::get(
                    '/{anggota}',
                    [AnggotaController::class, 'show']
                )->name('show');

                Route::get(
                    '/{anggota}/edit',
                    [AnggotaController::class, 'edit']
                )->name('edit');

                Route::put(
                    '/{anggota}',
                    [AnggotaController::class, 'update']
                )->name('update');

                Route::delete(
                    '/{anggota}',
                    [AnggotaController::class, 'destroy']
                )->name('destroy');
            });

        /*
         * Usaha.
         */
        Route::prefix('usaha')
            ->name('usaha.')
            ->group(function () {
                Route::get(
                    '/',
                    [UsahaController::class, 'index']
                )->name('index');

                Route::get(
                    '/create',
                    [UsahaController::class, 'create']
                )->name('create');

                Route::post(
                    '/',
                    [UsahaController::class, 'store']
                )->name('store');

                Route::get(
                    '/{usaha}',
                    [UsahaController::class, 'show']
                )->name('show');

                Route::get(
                    '/{usaha}/edit',
                    [UsahaController::class, 'edit']
                )->name('edit');

                Route::put(
                    '/{usaha}',
                    [UsahaController::class, 'update']
                )->name('update');

                Route::delete(
                    '/{usaha}',
                    [UsahaController::class, 'destroy']
                )->name('destroy');
            });

        /*
         * Laporan.
         */
        Route::prefix('laporan')
            ->name('laporan.')
            ->group(function () {
                Route::get(
                    '/',
                    [AdminLaporanController::class, 'index']
                )->name('index');

                Route::get(
                    '/create',
                    [AdminLaporanController::class, 'create']
                )->name('create');

                Route::post(
                    '/',
                    [AdminLaporanController::class, 'store']
                )->name('store');

                Route::get(
                    '/{laporan}',
                    [AdminLaporanController::class, 'show']
                )->name('show');

                Route::get(
                    '/{laporan}/edit',
                    [AdminLaporanController::class, 'edit']
                )->name('edit');

                Route::put(
                    '/{laporan}',
                    [AdminLaporanController::class, 'update']
                )->name('update');

                Route::delete(
                    '/{laporan}',
                    [AdminLaporanController::class, 'destroy']
                )->name('destroy');
            });

        /*
         * Evaluasi.
         */
        Route::prefix('evaluasi')
            ->name('evaluasi.')
            ->group(function () {
                Route::get(
                    '/',
                    [AdminEvaluasiController::class, 'index']
                )->name('index');

                Route::get(
                    '/{validasi}',
                    [AdminEvaluasiController::class, 'show']
                )->name('show');
            });

        /*
         * Rekap Monitoring.
         *
         * Route /pdf harus diletakkan sebelum /{laporan}/pdf.
         */
        Route::prefix('rekap-monitoring')
            ->name('rekap-monitoring.')
            ->group(function () {
                Route::get(
                    '/',
                    [RekapMonitoringController::class, 'index']
                )->name('index');

                Route::get(
                    '/pdf',
                    [RekapMonitoringController::class, 'monitoringPdf']
                )->name('pdf');

                Route::get(
                    '/{laporan}/pdf',
                    [RekapMonitoringController::class, 'detailPdf']
                )->name('detail-pdf');
            });
    });

/*
|--------------------------------------------------------------------------
| Anggota
|--------------------------------------------------------------------------
*/

Route::prefix('anggota')
    ->name('anggota.')
    ->middleware(['auth', 'role:anggota'])
    ->group(function () {
        Route::get(
            '/dashboard',
            [AnggotaDashboard::class, 'index']
        )->name('dashboard');

        /*
         * Usaha Saya.
         */
        Route::prefix('usaha-saya')
            ->name('usaha-saya.')
            ->group(function () {
                Route::get(
                    '/',
                    [UsahaSayaController::class, 'index']
                )->name('index');

                Route::get(
                    '/create',
                    [UsahaSayaController::class, 'create']
                )->name('create');

                Route::post(
                    '/',
                    [UsahaSayaController::class, 'store']
                )->name('store');

                Route::get(
                    '/{usaha}',
                    [UsahaSayaController::class, 'show']
                )->name('show');
            });

        /*
         * Laporan.
         */
        Route::prefix('laporan')
            ->name('laporan.')
            ->group(function () {
                Route::get(
                    '/',
                    [AnggotaLaporanController::class, 'index']
                )->name('index');

                Route::get(
                    '/create',
                    [AnggotaLaporanController::class, 'create']
                )->name('create');

                Route::post(
                    '/',
                    [AnggotaLaporanController::class, 'store']
                )->name('store');

                Route::get(
                    '/{laporan}',
                    [AnggotaLaporanController::class, 'show']
                )->name('show');

                Route::get(
                    '/{laporan}/edit',
                    [AnggotaLaporanController::class, 'edit']
                )->name('edit');

                Route::put(
                    '/{laporan}',
                    [AnggotaLaporanController::class, 'update']
                )->name('update');
            });

        /*
         * Evaluasi.
         */
        Route::prefix('evaluasi')
            ->name('evaluasi.')
            ->group(function () {
                Route::get(
                    '/',
                    [AnggotaEvaluasiController::class, 'index']
                )->name('index');

                Route::get(
                    '/{validasi}',
                    [AnggotaEvaluasiController::class, 'show']
                )->name('show');
            });

        /*
         * Rekap Laporan Pribadi.
         *
         * Route /pdf harus diletakkan sebelum /{laporan}/pdf.
         */
        Route::prefix('rekap-laporan')
            ->name('rekap-laporan.')
            ->group(function () {
                Route::get(
                    '/',
                    [RekapLaporanController::class, 'index']
                )->name('index');

                Route::get(
                    '/pdf',
                    [RekapLaporanController::class, 'pdf']
                )->name('pdf');

                Route::get(
                    '/{laporan}/pdf',
                    [RekapLaporanController::class, 'detailPdf']
                )->name('detail-pdf');
            });
    });

/*
|--------------------------------------------------------------------------
| Pengurus
|--------------------------------------------------------------------------
*/

Route::prefix('pengurus')
    ->name('pengurus.')
    ->middleware(['auth', 'role:pengurus'])
    ->group(function () {
        Route::get(
            '/dashboard',
            [PengurusDashboard::class, 'index']
        )->name('dashboard');

        /*
         * Validasi Laporan.
         */
        Route::prefix('validasi-laporan')
            ->name('validasi-laporan.')
            ->group(function () {
                Route::get(
                    '/',
                    [ValidasiLaporanController::class, 'index']
                )->name('index');

                Route::get(
                    '/{laporan}',
                    [ValidasiLaporanController::class, 'show']
                )->name('show');

                Route::post(
                    '/{laporan}/setujui',
                    [ValidasiLaporanController::class, 'setujui']
                )->name('setujui');

                Route::post(
                    '/{laporan}/tolak',
                    [ValidasiLaporanController::class, 'tolak']
                )->name('tolak');
            });

        /*
         * Evaluasi.
         */
        Route::prefix('evaluasi')
            ->name('evaluasi.')
            ->group(function () {
                Route::get(
                    '/',
                    [PengurusEvaluasiController::class, 'index']
                )->name('index');

                Route::get(
                    '/{validasi}',
                    [PengurusEvaluasiController::class, 'show']
                )->name('show');
            });

        /*
         * Rekap Monitoring.
         */
        Route::prefix('rekap-monitoring')
            ->name('rekap-monitoring.')
            ->group(function () {
                Route::get(
                    '/',
                    [RekapMonitoringController::class, 'index']
                )->name('index');

                Route::get(
                    '/pdf',
                    [RekapMonitoringController::class, 'monitoringPdf']
                )->name('pdf');

                Route::get(
                    '/{laporan}/pdf',
                    [RekapMonitoringController::class, 'detailPdf']
                )->name('detail-pdf');
            });
    });

/*
|--------------------------------------------------------------------------
| DPS
|--------------------------------------------------------------------------
*/

Route::prefix('dps')
    ->name('dps.')
    ->middleware(['auth', 'role:dps'])
    ->group(function () {
        Route::get(
            '/dashboard',
            [DpsDashboard::class, 'index']
        )->name('dashboard');

        /*
         * Validasi Akad Syariah.
         */
        Route::prefix('validasi-syariah')
            ->name('validasi-syariah.')
            ->group(function () {
                Route::get(
                    '/',
                    [ValidasiSyariahController::class, 'index']
                )->name('index');

                Route::get(
                    '/{usaha}',
                    [ValidasiSyariahController::class, 'show']
                )->name('show');

                Route::post(
                    '/{usaha}/proses',
                    [ValidasiSyariahController::class, 'proses']
                )->name('proses');
            });

        /*
         * Data Usaha.
         *
         * Halaman read-only untuk melihat data usaha, status monitoring,
         * laporan, evaluasi, dan riwayat validasi akad.
         */
        Route::prefix('data-usaha')
            ->name('data-usaha.')
            ->group(function () {
                Route::get(
                    '/',
                    [DataUsahaController::class, 'index']
                )->name('index');

                Route::get(
                    '/{usaha}',
                    [DataUsahaController::class, 'show']
                )->name('show');
            });

        /*
         * Rekap Validasi Syariah.
         *
         * Route /pdf harus diletakkan sebelum /{usaha}/pdf.
         */
        Route::prefix('rekap-syariah')
            ->name('rekap-syariah.')
            ->group(function () {
                Route::get(
                    '/',
                    [RekapSyariahController::class, 'index']
                )->name('index');

                Route::get(
                    '/pdf',
                    [RekapSyariahController::class, 'pdf']
                )->name('pdf');

                Route::get(
                    '/{usaha}/pdf',
                    [RekapSyariahController::class, 'detailPdf']
                )->name('detail-pdf');
            });
    });

/*
|--------------------------------------------------------------------------
| Ketua
|--------------------------------------------------------------------------
*/

Route::prefix('ketua')
    ->name('ketua.')
    ->middleware(['auth', 'role:ketua'])
    ->group(function () {
        Route::get(
            '/dashboard',
            [KetuaDashboard::class, 'index']
        )->name('dashboard');

        /*
         * Monitoring.
         */
        Route::prefix('monitoring')
            ->name('monitoring.')
            ->group(function () {
                Route::get(
                    '/',
                    [KetuaMonitoringController::class, 'index']
                )->name('index');

                Route::get(
                    '/{validasi}',
                    [KetuaMonitoringController::class, 'show']
                )->name('show');
            });

        /*
         * Rekap Monitoring.
         */
        Route::prefix('rekap-monitoring')
            ->name('rekap-monitoring.')
            ->group(function () {
                Route::get(
                    '/',
                    [RekapMonitoringController::class, 'index']
                )->name('index');

                Route::get(
                    '/pdf',
                    [RekapMonitoringController::class, 'monitoringPdf']
                )->name('pdf');

                Route::get(
                    '/{laporan}/pdf',
                    [RekapMonitoringController::class, 'detailPdf']
                )->name('detail-pdf');
            });
    });
