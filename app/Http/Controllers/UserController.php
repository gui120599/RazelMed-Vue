<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Institution;
use App\Models\Dashboard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

// Importe o Form Request
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    public function __construct()
    {
        // Todas as ações deste controller são restritas ao Super Admin
        /*$this->middleware(function ($request, $next) {
            if (Gate::denies('manage-platform')) {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        });*/
    }

    /**
     * Display a listing of the users for the admin panel.
     */
    public function indexAdmin(Request $request)
    {
        $users = User::with(['institutions', 'accessibleDashboards', 'preferredInstitution'])->paginate(10);
        $allInstitutions = Institution::all(); // <-- Adicione isso
        $allDashboards = Dashboard::all(); // <-- Adicione isso

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'allInstitutions' => $allInstitutions, // <-- Passe para a página
            'allDashboards' => $allDashboards,     // <-- Passe para a página
        ]);
    }

    /**
     * Display a listing of all users (for Super Admin).
     */
    public function index()
    {
        /*$users = User::with(['institutions', 'accessibleDashboards', 'preferredInstitution'])->get();
        return response()->json($users);*/

        // Certifique-se de que as relações estão sendo carregadas
        $users = User::with(['institutions', 'accessibleDashboards', 'preferredInstitution'])->paginate(10);
        return Inertia::render('Admin/Users/Index', ['users' => $users]);
    }

    /**
     * Display the specified user. (for Super Admin)
     */
    public function show(User $user)
    {
        return response()->json($user->load(['institutions', 'accessibleDashboards', 'preferredInstitution']));
    }

    public function store(Request $request)
    {
        // Validação
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'is_super_admin' => 'boolean',
            'institutions' => 'array', // Array de IDs
            'institutions.*' => 'exists:institutions,id', // Cada ID deve existir na tabela institutions
            'accessible_dashboards' => 'array', // Array de IDs
            'accessible_dashboards.*' => 'exists:dashboards,id', // Cada ID deve existir na tabela dashboards
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'is_super_admin' => $validatedData['is_super_admin'] ?? false,
        ]);

        // Sincronizar relações many-to-many
        if (isset($validatedData['institutions'])) {
            $user->institutions()->sync($validatedData['institutions']);
        }
        if (isset($validatedData['accessible_dashboards'])) {
            $user->accessibleDashboards()->sync($validatedData['accessible_dashboards']);
        }

        return redirect()->back()
            ->with('success', 'Usuário criado com sucesso!');
    }

    /**
     * Update the specified user's details and permissions. (for Super Admin)
     * Use UpdateUserRequest para validação.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        DB::transaction(function () use ($request, $user) {
            // Os dados validados já vêm do request.
            $validatedData = $request->validated();

            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            if (isset($validatedData['password'])) { // Use isset para verificar se a senha foi fornecida
                $user->password = Hash::make($validatedData['password']);
            }
            $user->prefer_institution_id = $validatedData['prefer_institution_id'] ?? null; // Nullable
            $user->is_super_admin = $validatedData['is_super_admin']; // A validação condicional já está no Request
            $user->save();

            // Sincronizar instituições às quais o usuário tem acesso
            if (isset($validatedData['institution_ids'])) {
                $user->institutions()->sync($validatedData['institution_ids']);
            } else {
                $user->institutions()->detach();
            }

            // Sincronizar permissões de dashboards
            if (isset($validatedData['dashboard_ids'])) {
                $user->accessibleDashboards()->sync($validatedData['dashboard_ids']);
            } else {
                $user->accessibleDashboards()->detach();
            }
        });

        return response()->json($user->load(['institutions', 'accessibleDashboards', 'preferredInstitution']));
    }

    /**
     * Remove the specified user from storage. (for Super Admin)
     */
    public function destroy(User $user)
    {
        // A autorização para não deletar Super Admin já está no Gate ou pode ser adicionada aqui
        // No Request UpdateUserRequest já tem uma validação para impedir que um Super Admin remova seu próprio status,
        // mas aqui é uma camada para impedir a deleção completa.
        if (Auth::id() === $user->id) {
            return response()->json(['message' => 'Você não pode deletar sua própria conta de Super Admin.'], 403);
        }
        if ($user->is_super_admin) {
            return response()->json(['message' => 'Você não pode deletar outra conta de Super Admin.'], 403);
        }

        $user->delete();
        return response()->json(null, 204);
    }
}