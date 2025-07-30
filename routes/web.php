<?php

use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get("/", function () {
    return redirect()->route('login');
});



Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/home', function () {
        return Inertia::render('Home');
    })->name('home');

    Route::resource('institutions', InstitutionController::class);
    Route::post('institutions/{institution}/users/attach', [InstitutionController::class, 'attachUser'])->name('institutions.users.attach');
    Route::delete('institutions/{institution}/users/detach', [InstitutionController::class, 'detachUser'])->name('institutions.users.detach');
    
    Route::resource('dashboards', DashboardController::class);
    Route::post('dashboards/{dashboard}/users/attach', [DashboardController::class, 'attachUser'])->name('dashboards.users.attach');
    Route::delete('dashboards/{dashboard}/users/detach', [DashboardController::class, 'detachUser'])->name('dashboards.users.detach');
});



require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
