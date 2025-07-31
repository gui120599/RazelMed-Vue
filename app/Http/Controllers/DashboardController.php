<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDashboardRequest;
use App\Http\Requests\UpdateDashboardRequest;
use App\Models\Dashboard;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dashboards = Dashboard::orderBy('name')->with('institution', 'users')->get();
        $institutions = Institution::orderBy('name')->get();
        $allUsers = User::all();

        return Inertia::render('dashboard/Index', ['dashboards' => $dashboards, 'institutions' => $institutions, 'allUsers' => $allUsers]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDashboardRequest $request)
    {
        $data = $request->validated();

        Dashboard::create($data);

        return redirect()->route('dashboards.index')->with('success', 'Dashboard cadastrado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Dashboard $dashboard)
    {
        $user = Auth::user();

        // Carrega o relacionamento 'institution' para o dashboard
        $dashboard->load('institution');

        // 1. Defina a condição de acesso
        $hasAccess = false;

        // Se o usuário for super admin, ele tem acesso total
        if ($user && $user->isSuperAdmin()) {
            $hasAccess = true;
        }

        // Se não for super admin, verifique se ele tem acesso ao dashboard E à instituição
        elseif ($user && $user->dashboards->contains($dashboard) && $user->institutions->contains($dashboard->institution)) {
            $hasAccess = true;
        }

        // 2. Verifique se a condição foi atendida
        if ($hasAccess) {
            return Inertia::render('dashboard/Show', [
                'dashboard' => $dashboard,
            ]);
        }

        // 3. Se não tiver permissão, retorne um erro 403
        abort(403, 'Usuário sem permissão de acesso.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Dashboard $dashboard)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDashboardRequest $request, Dashboard $dashboard)
    {
        $data = $request->validated();

        $dashboard->update($data);

        return redirect()->route('dashboards.index')->with('success', 'Dashboard atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dashboard $dashboard)
    {

        $dashboard->delete();
        return redirect()->route('dashboards.index')->with('success', 'Dashboard cadastrado com sucesso!');

    }

    /**
     * Anexar um usuário a um dashboard.
     */
    public function attachUser(Dashboard $dashboard, Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $dashboard->users()->syncWithoutDetaching([$request->user_id]);

        return redirect()->back()->with('success', 'Usuário adicionado ao dashboard com sucesso!');
    }

    /**
     * Desanexar um usuário de um dashboard.
     */
    public function detachUser(Dashboard $dashboard, Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $dashboard->users()->detach($request->user_id);

        return redirect()->back()->with('success', 'Usuário removido do dashboard com sucesso!');
    }
}
