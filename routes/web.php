<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Kuesioner\KuesionerController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Route::get('kuesioner', [KuesionerController::class, 'index'])->name('kuesioner');
        // Route::get('kuesioner/create', [KuesionerController::class, 'create'])->name('kuesioner.create');

        // Route::post('kuesioner/store', [KuesionerController::class, 'store'])->name('kuesioner.store');
        // Route::post('kuesioner/edit', [KuesionerController::class, 'edit'])->name('kuesioner.edit');
        // Route::put('kuesioner/update', [KuesionerController::class, 'update'])->name('kuesioner.update');

        // Route::delete('/admin/kuesioner/{kuesioner}', [KuesionerController::class, 'destroy'])
        //     ->name('kuesioner.destroy');

        // Route::post('/admin/kuesioner/bulk-destroy', [KuesionerController::class, 'bulkDestroy'])
        //     ->name('kuesioner.bulkDestroy');
    });

Route::prefix('admin/kuesioner')
    ->name('admin.kuesioner.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/', [KuesionerController::class, 'index'])->name('index');
        Route::get('/create', [KuesionerController::class, 'create'])->name('create');
        Route::post('/', [KuesionerController::class, 'store'])->name('store');
        Route::get('/{kuesioner}/edit', [KuesionerController::class, 'edit'])->name('edit');
        Route::put('/{kuesioner}', [KuesionerController::class, 'update'])->name('update');
        Route::delete('/{kuesioner}', [KuesionerController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-destroy', [KuesionerController::class, 'bulkDestroy'])->name('bulkDestroy');
    });


Route::prefix('kepala')
    ->name('kepala.')
    ->middleware(['auth', 'role:kepala'])
    ->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });

Route::middleware(['auth'])->group(function () {
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings/update', [SettingController::class, 'update'])->name('settings.update');
    Route::put('/settings/password', [SettingController::class, 'updatePassword'])->name('settings.password.update');
});


require __DIR__ . '/auth.php';
