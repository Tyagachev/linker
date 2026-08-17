<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard', []);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->group(function () {
   Route::get('/admin', [\App\Http\Controllers\Admin\AdminController::class, 'index'])->name('admin.index');
});

/**
 * Конференции
 */
Route::middleware('auth')->group(function () {
    Route::get('/conferences', [\App\Http\Controllers\ConferenceController::class, 'index'])->name('conferences.index');
    Route::post('/conferences', [\App\Http\Controllers\ConferenceController::class, 'store'])->name('conferences.store');
    Route::get('/conferences/create', [\App\Http\Controllers\ConferenceController::class, 'create'])->name('conferences.create');
    Route::get('/conferences/edit', [\App\Http\Controllers\ConferenceController::class, 'edit'])->name('conferences.edit');
    Route::put('/conferences/update', [\App\Http\Controllers\ConferenceController::class, 'update'])->name('conferences.update');
    Route::delete('/conferences/{conference}', [\App\Http\Controllers\ConferenceController::class, 'destroy'])->name('conferences.destroy');
});
Route::get('/conferences/{conference}', [\App\Http\Controllers\ConferenceController::class, 'show'])->name('conferences.show');

Route::resource('/regions', \App\Http\Controllers\RegionController::class);

Route::middleware('auth')->group(function () {
    Route::get('/salons', [\App\Http\Controllers\SalonController::class, 'index'])->name('salons.index');
    Route::post('/salons', [\App\Http\Controllers\SalonController::class, 'store'])->name('salons.store');
    Route::get('/salons/create', [\App\Http\Controllers\SalonController::class, 'create'])->name('salons.create');
    Route::get('/salons/edit', [\App\Http\Controllers\SalonController::class, 'edit'])->name('salons.edit');
    Route::put('/salons/update', [\App\Http\Controllers\SalonController::class, 'update'])->name('salons.update');
    Route::delete('/salons/{salon}', [\App\Http\Controllers\SalonController::class, 'destroy'])->name('salons.destroy');
});
Route::get('/salons/{salon}', [\App\Http\Controllers\SalonController::class, 'show'])->name('salons.show');


Route::post('/conferences/salon', [\App\Http\Controllers\ConferenceSalonController::class, 'store'])
    ->name('conferences.salon.store');

require __DIR__.'/auth.php';
