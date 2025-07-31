<?php

namespace App\Http\Controllers;

use App\Models\Dashboard;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class Home extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Verifica se o usuário logado é um super admin
        if ($user && $user->isSuperAdmin()) { // Assumindo que você tem um método isSuperAdmin() no seu modelo User
            // Se for super admin, retorna todos os dashboards e instituições
            $dashboards = Dashboard::orderBy('name')->with('institution')->get();
            $institutions = Institution::orderBy('name')->get();
        } else {
            // Se não for super admin, filtra os resultados

            // Retorna apenas os dashboards aos quais o usuário tem acesso
            $dashboards = $user->dashboards()->orderBy('name')->with('institution')->get();

            // Retorna apenas as instituições às quais o usuário tem acesso
            $institutions = $user->institutions()->orderBy('name')->get();
        }

        return Inertia::render('Home', [
            'dashboards' => $dashboards,
            'institutions' => $institutions
        ]);

    }
}
