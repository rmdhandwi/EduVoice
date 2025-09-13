<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RespondenController as AdminRespondenController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IKMController;
use App\Http\Controllers\Kuesioner\KuesionerController;
use App\Http\Controllers\Kuesioner\PertanyaanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\User\RespondenController as UserRespondenController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/thankyou', fn() => Inertia::render('ThankYou'))->name('thankyou');
});

Route::middleware('guest')
    ->prefix('kuesioner')
    ->name('user.kuesioner.')
    ->group(function () {
        Route::get('/', [UserRespondenController::class, 'index'])->name('index');
        Route::get('/{id}/survey', [UserRespondenController::class, 'show'])->name('show');
        Route::get('/kritik-saran', [UserRespondenController::class, 'kritikSaran'])->name('kritik.saran');
        Route::post('/{id}/send-survey', [UserRespondenController::class, 'submit'])->name('submit');
    });

Route::middleware('guest')
    ->group(function () {
        Route::get('/kritik-saran', [UserRespondenController::class, 'kritikSaran'])->name('user.kritik.saran');
    });

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });

Route::prefix('admin/kuesioner')
    ->name('admin.kuesioner.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/', [KuesionerController::class, 'index'])->name('index');
        Route::post('/', [KuesionerController::class, 'store'])->name('store');
        Route::get('/{kuesioner}', [KuesionerController::class, 'show'])->name('show');
        Route::put('/{kuesioner}', [KuesionerController::class, 'update'])->name('update');
    });

Route::prefix('admin/kuesioner/{kuesioner}/pertanyaan')
    ->name('admin.kuesioner.pertanyaan.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/', [PertanyaanController::class, 'index'])->name('index');
        Route::get('/create', [PertanyaanController::class, 'create'])->name('create');
        Route::post('/store', [PertanyaanController::class, 'store'])->name('store');
        Route::get('/{pertanyaan}/edit', [PertanyaanController::class, 'edit'])->name('edit');
        Route::put('/{pertanyaan}', [PertanyaanController::class, 'update'])->name('update');
        Route::delete('/{pertanyaan}', [PertanyaanController::class, 'destroy'])->name('destroy');

        Route::get('/bulk-edit', [PertanyaanController::class, 'bulkEdit'])->name('bulkEdit');
        Route::post('/bulk-update', [PertanyaanController::class, 'bulkUpdate'])->name('bulkUpdate');
        Route::post('/bulk-destroy', [PertanyaanController::class, 'bulkDestroy'])->name('bulkDestroy');
    });


Route::prefix('admin/responden')
    ->name('admin.responden.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/', [AdminRespondenController::class, 'index'])->name('index');
        Route::get('/{responden}/detail', [AdminRespondenController::class, 'show'])->name('show');
    });


Route::prefix('admin/ikm')
    ->name('admin.ikm.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/', [IKMController::class, 'index'])->name('index');
        Route::get('/{ikm}/detail', [IKMController::class, 'show'])->name('show');
    });


Route::prefix('kepala')
    ->name('kepala.')
    ->middleware(['auth', 'role:kepala'])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/{laporan}/detail', [LaporanController::class, 'show'])->name('laporan.show');
    });

Route::middleware(['auth'])->group(function () {
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings/update', [SettingController::class, 'update'])->name('settings.update');
    Route::put('/settings/password', [SettingController::class, 'updatePassword'])->name('settings.password.update');
});


require __DIR__ . '/auth.php';
