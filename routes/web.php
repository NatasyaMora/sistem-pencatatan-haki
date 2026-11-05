<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminFacultyController;
use App\Http\Controllers\Admin\AdminHakiController;
use App\Http\Controllers\Admin\AdminJenisHakiController;
use App\Http\Controllers\Admin\AdminRenewalRequestController;
use App\Http\Controllers\Admin\AdminStudyProgramController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PublicDashboardController;
use App\Http\Controllers\User\MyHakiController;
use App\Http\Controllers\User\RenewalRequestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dashboard Publik
|--------------------------------------------------------------------------
*/

Route::get('/', [PublicDashboardController::class, 'index'])
    ->name('public.dashboard');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::get('/login', [LoginController::class, 'showLoginForm'])
    ->name('login');

Route::post('/login', [LoginController::class, 'login']);

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Halaman Pengguna: Mahasiswa dan Dosen
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:user'])
    ->prefix('my-haki')
    ->name('user.haki.')
    ->group(function () {

        // Daftar HAKI milik pengguna
        Route::get('/', [MyHakiController::class, 'index'])
            ->name('index');

        // Form pencatatan HAKI baru
        Route::get('/create', [MyHakiController::class, 'create'])
            ->name('create');

        // Menyimpan pencatatan HAKI baru
        Route::post('/', [MyHakiController::class, 'store'])
            ->name('store');

        // Riwayat pengajuan pembaruan HAKI
        Route::get('/riwayat-pembaruan', [
            RenewalRequestController::class,
            'index',
        ])->name('renewal-history.index');

        // Form pengajuan pembaruan HAKI
        Route::get('/{haki}/renewal', [
            RenewalRequestController::class,
            'create',
        ])->whereNumber('haki')
          ->name('renewal.create');

        // Mengirim pengajuan pembaruan HAKI
        Route::post('/{haki}/renewal', [
            RenewalRequestController::class,
            'store',
        ])->whereNumber('haki')
          ->name('renewal.store');

        // Detail HAKI milik pengguna
        Route::get('/{haki}', [
            MyHakiController::class,
            'show',
        ])->whereNumber('haki')
          ->name('show');
    });

/*
|--------------------------------------------------------------------------
| Halaman Admin
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard Admin
        Route::get('/dashboard', [
            AdminDashboardController::class,
            'index',
        ])->name('dashboard');

        /*
        |------------------------------------------------------------------
        | Pengelolaan Data HAKI
        |------------------------------------------------------------------
        */

        Route::get('/haki', [
            AdminHakiController::class,
            'index',
        ])->name('haki.index');

        Route::get('/haki/{haki}/edit', [
            AdminHakiController::class,
            'edit',
        ])->whereNumber('haki')
          ->name('haki.edit');

        Route::put('/haki/{haki}', [
            AdminHakiController::class,
            'update',
        ])->whereNumber('haki')
          ->name('haki.update');

        Route::get('/haki/{haki}', [
            AdminHakiController::class,
            'show',
        ])->whereNumber('haki')
          ->name('haki.show');

        Route::patch('/haki/{haki}/toggle-status', [
            AdminHakiController::class,
            'toggleStatus',
        ])->whereNumber('haki')
          ->name('haki.toggle-status');

        Route::patch('/haki/{haki}/activate', [
            AdminHakiController::class,
            'activate',
        ])->whereNumber('haki')
          ->name('haki.activate');

        Route::patch('/haki/{haki}/deactivate', [
            AdminHakiController::class,
            'deactivate',
        ])->whereNumber('haki')
          ->name('haki.deactivate');

        /*
        |------------------------------------------------------------------
        | Master Jenis HAKI
        |------------------------------------------------------------------
        */

        Route::get('/jenis-haki', [
            AdminJenisHakiController::class,
            'index',
        ])->name('jenis-haki.index');

        Route::get('/jenis-haki/create', [
            AdminJenisHakiController::class,
            'create',
        ])->name('jenis-haki.create');

        Route::post('/jenis-haki', [
            AdminJenisHakiController::class,
            'store',
        ])->name('jenis-haki.store');

        Route::get('/jenis-haki/{jenisHaki}/edit', [
            AdminJenisHakiController::class,
            'edit',
        ])->name('jenis-haki.edit');

        Route::put('/jenis-haki/{jenisHaki}', [
            AdminJenisHakiController::class,
            'update',
        ])->name('jenis-haki.update');

        Route::patch('/jenis-haki/{jenisHaki}/toggle-status', [
            AdminJenisHakiController::class,
            'toggleStatus',
        ])->name('jenis-haki.toggle-status');

        /*
        |------------------------------------------------------------------
        | Master Fakultas
        |------------------------------------------------------------------
        */

        Route::get('/fakultas', [
            AdminFacultyController::class,
            'index',
        ])->name('fakultas.index');

        Route::get('/fakultas/create', [
            AdminFacultyController::class,
            'create',
        ])->name('fakultas.create');

        Route::post('/fakultas', [
            AdminFacultyController::class,
            'store',
        ])->name('fakultas.store');

        Route::get('/fakultas/{faculty}/edit', [
            AdminFacultyController::class,
            'edit',
        ])->name('fakultas.edit');

        Route::put('/fakultas/{faculty}', [
            AdminFacultyController::class,
            'update',
        ])->name('fakultas.update');

        Route::patch('/fakultas/{faculty}/toggle-status', [
            AdminFacultyController::class,
            'toggleStatus',
        ])->name('fakultas.toggle-status');

        /*
        |------------------------------------------------------------------
        | Master Program Studi
        |------------------------------------------------------------------
        */

        Route::get('/program-studi', [
            AdminStudyProgramController::class,
            'index',
        ])->name('program-studi.index');

        Route::get('/program-studi/create', [
            AdminStudyProgramController::class,
            'create',
        ])->name('program-studi.create');

        Route::post('/program-studi', [
            AdminStudyProgramController::class,
            'store',
        ])->name('program-studi.store');

        Route::get('/program-studi/{studyProgram}/edit', [
            AdminStudyProgramController::class,
            'edit',
        ])->name('program-studi.edit');

        Route::put('/program-studi/{studyProgram}', [
            AdminStudyProgramController::class,
            'update',
        ])->name('program-studi.update');

        Route::patch('/program-studi/{studyProgram}/toggle-status', [
            AdminStudyProgramController::class,
            'toggleStatus',
        ])->name('program-studi.toggle-status');

        /*
        |------------------------------------------------------------------
        | Pengajuan Pembaruan HAKI
        |------------------------------------------------------------------
        */

        // Daftar semua pengajuan pembaruan
        Route::get('/renewal-requests', [
            AdminRenewalRequestController::class,
            'index',
        ])->name('renewal-requests.index');

        // Detail satu pengajuan
        Route::get('/renewal-requests/{renewalRequest}', [
            AdminRenewalRequestController::class,
            'show',
        ])->whereNumber('renewalRequest')
          ->name('renewal-requests.show');

        // Konfirmasi pengajuan
        Route::post('/renewal-requests/{renewalRequest}/confirm', [
            AdminRenewalRequestController::class,
            'confirm',
        ])->whereNumber('renewalRequest')
          ->name('renewal-requests.confirm');

        // Menolak pengajuan
        Route::post('/renewal-requests/{renewalRequest}/reject', [
            AdminRenewalRequestController::class,
            'reject',
        ])->whereNumber('renewalRequest')
          ->name('renewal-requests.reject');
    });
