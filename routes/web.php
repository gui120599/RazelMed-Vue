<?php

use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/home', function () {
    return Inertia::render('Home');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::resource('institutions',InstitutionController::class);

    Route::resource('dashboards', DashboardController::class);
});



require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
