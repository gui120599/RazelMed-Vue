<?php

namespace App\Http\Controllers;

use App\Models\Dashboard;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
// Importe os Form Requests
use App\Http\Requests\StoreDashboardRequest;
use App\Http\Requests\UpdateDashboardRequest;

class DashboardController extends Controller
{
    public function __construct()
    {
        // Protege as ações de gerenciamento; index e show são acessíveis por usuários autenticados
        /*$this->middleware(function ($request, $next) {
            if (in_array($request->route()->getName(), ['dashboards.store', 'dashboards.update', 'dashboards.destroy'])) {
                if (Gate::denies('manage-platform')) {
                    abort(403, 'Unauthorized action.');
                }
            }
            return $next($request);
        }); // Sem `except` aqui, o Gate dentro dos requests e no middleware cuida disso*/
    }

    /**
     * Display a listing of the dashboards for the admin panel.
     */
    public function indexAdmin(Request $request)
    {
        $dashboards = Dashboard::with('institution')->paginate(10);
        $allInstitutions = Institution::all(); // <-- Adicione isso

        return Inertia::render('Admin/Dashboards/Index', [
            'dashboards' => $dashboards,
            'allInstitutions' => $allInstitutions, // <-- Passe para a página
        ]);
    }

    /**
     * Display a listing of the dashboards relevant to the authenticated user.
     * (Accessible by regular users and Super Admin)
     */
    public function index()
    {
        /*if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        $dashboards = collect();

        if ($user->is_super_admin) {
            $dashboards = Dashboard::with('institution')->get();
        } else {
            if ($user->prefer_institution_id) {
                $hasAccessToPreferredInstitution = $user->institutions()
                                                        ->where('institution_id', $user->prefer_institution_id)
                                                        ->exists();

                if ($hasAccessToPreferredInstitution) {
                    $dashboards = $user->accessibleDashboards()
                                        ->where('institution_id', $user->prefer_institution_id)
                                        ->get();
                }
            }
        }

        return response()->json($dashboards);*/

        // Certifique-se de que a relação 'institution' está sendo carregada
        $dashboards = Dashboard::with('institution')->paginate(10); // Exemplo de paginação
        return Inertia::render('Admin/Dashboards/Index', ['dashboards' => $dashboards]);
    }

    /**
     * Store a newly created dashboard in storage. (Super Admin only)
     * Use StoreDashboardRequest para validação.
     */
    public function store(StoreDashboardRequest $request)
    {
        $dashboard = Dashboard::create($request->validated());
        return response()->json($dashboard, 201);
    }

    /**
     * Display the specified dashboard.
     * (Accessible by regular users with permission and Super Admin)
     */
    public function show(Dashboard $dashboard)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = Auth::user();

        if ($user->is_super_admin) {
            return response()->json($dashboard->load('institution'));
        }

        $canAccessDashboard = $user->prefer_institution_id === $dashboard->institution_id &&
            $user->accessibleDashboards()->where('dashboard_id', $dashboard->id)->exists() &&
            $user->institutions()->where('institution_id', $user->prefer_institution_id)->exists();

        if ($canAccessDashboard) {
            return response()->json($dashboard->load('institution'));
        }

        return response()->json(['message' => 'Forbidden'], 403);
    }

    /**
     * Update the specified dashboard in storage. (Super Admin only)
     * Use UpdateDashboardRequest para validação.
     */
    public function update(UpdateDashboardRequest $request, Dashboard $dashboard)
    {
        $dashboard->update($request->validated());
        return response()->json($dashboard);
    }

    /**
     * Remove the specified dashboard from storage. (Super Admin only)
     */
    public function destroy(Dashboard $dashboard)
    {
        // A autorização é feita no construtor via Gate
        $dashboard->delete();
        return response()->json(null, 204);
    }
}