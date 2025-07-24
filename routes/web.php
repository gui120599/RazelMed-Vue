<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstitutionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    //return Inertia::render('Welcome');
    return redirect()->route('login');
})->name('home');



Route::middleware(['auth', 'verified'])->group(function () {
    // Rota principal da Home (onde o dashboard index será carregado)
    Route::get('/home', function () {
        return Inertia::render('Home'); // Sua página Dashboard
    })->name('home');

    // Rota para mostrar um dashboard específico
    Route::get('/dashboard/{dashboard}', [DashboardController::class, 'show'])->name('dashboard.show');

    // --- Rotas para o Super Admin ---
    Route::prefix('admin')->middleware('can:manage-platform')->group(function () {
        // Gerenciamento de Instituições
        Route::get('/institutions', function () {
            return Inertia::render('Admin/Institutions/Index'); // Sua página Vue para gerenciar instituições
        })->name('admin.institutions.index');
        // Você pode ter APIs para store/update/delete no seu api.php apontando para InstitutionController

        // Gerenciamento de Dashboards
        Route::get('/dashboards', function () {
            return Inertia::render('Admin/Dashboards/Index'); // Sua página Vue para gerenciar dashboards
        })->name('admin.dashboards.index');
        // APIs para store/update/delete de dashboards

        // Gerenciamento de Usuários
        Route::get('/users', function () {
            return Inertia::render('Admin/Users/Index'); // Sua página Vue para gerenciar usuários
        })->name('admin.users.index');
        // APIs para update/delete de usuários
    });

    // API Routes for CRUD operations (assuming you're doing Inertia with API for data)
    // Make sure these routes are also protected by auth:sanctum if they are in api.php
    Route::apiResource('institutions', InstitutionController::class);
    Route::get('/user-institutions', [InstitutionController::class, 'indexUsersInstitutions']);
    Route::apiResource('dashboards', DashboardController::class);
    Route::apiResource('users', UserController::class)->except(['store']);
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
