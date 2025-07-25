<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\UserController;
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

    // --- Rotas para o Super Admin (Gerenciamento de CRUDS) ---
    Route::prefix('admin')->middleware('can:manage-platform')->group(function () {

        // Gerenciamento de Instituições
        Route::get('/institutions', [InstitutionController::class, 'indexAdmin'])->name('admin.institutions.index');
        // Adicione aqui as rotas para criar, editar, deletar instituições via API
        // Exemplo: Route::post('/institutions', [InstitutionController::class, 'store']);

        // Gerenciamento de Dashboards
        Route::get('/dashboards', [DashboardController::class, 'indexAdmin'])->name('admin.dashboards.index');
        // Exemplo: Route::post('/dashboards', [DashboardController::class, 'store']);

        // Gerenciamento de Usuários
        Route::get('/users', [UserController::class, 'indexAdmin'])->name('admin.users.index');
        // Exemplo: Route::patch('/users/{user}', [UserController::class, 'update']);
        // Exemplo: Route::delete('/users/{user}', [UserController::class, 'destroy']);
    });

    // --- Rotas API para as operações CRUD (sem prefíxo 'admin' para serem usadas pelos Dialogs) ---
    // Estas são as rotas que seus modais de Create/Edit/Delete vão chamar
    // Geralmente, estas usam Route::apiResource para o CRUD completo
    Route::apiResource('institutions', InstitutionController::class)
        ->only(['store', 'update', 'destroy']) // Define quais métodos são expostos
        ->middleware('can:manage-platform'); // Proteja com a mesma permissão

    Route::apiResource('dashboards', DashboardController::class)
        ->only(['store', 'update', 'destroy'])
        ->middleware('can:manage-platform');

    Route::apiResource('users', UserController::class)
        ->only(['update', 'destroy']) // Usuários não têm 'store' aqui, pois a criação é pelo registro
        ->middleware('can:manage-platform');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
