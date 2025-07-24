<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
// Importe os Form Requests
use App\Http\Requests\StoreInstitutionRequest;
use App\Http\Requests\UpdateInstitutionRequest;
// use App\Notifications\NewInstitutionCreatedNotification; // Se for usar

class InstitutionController extends Controller
{
    public function __construct()
    {
        // Protege as ações de gerenciamento; indexUsersInstitutions é público para usuários autenticados
        $this->middleware(function ($request, $next) {
            if (!in_array($request->route()->getName(), ['institutions.indexUsersInstitutions'])) {
                if (Gate::denies('manage-platform')) {
                    abort(403, 'Unauthorized action.');
                }
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the institutions. (Super Admin only)
     */
    public function index()
    {
        $institutions = Institution::with(['users', 'dashboards'])->get();
        return response()->json($institutions);
    }

    /**
     * Store a newly created institution in storage. (Super Admin only)
     * Use StoreInstitutionRequest para validação.
     */
    public function store(StoreInstitutionRequest $request)
    {
        $institution = Institution::create($request->validated());

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photos/institutions', 'public');
            $institution->profile_photo_path = $path;
            $institution->save();
        }

        return response()->json($institution, 201);
    }

    /**
     * Display the specified institution. (Super Admin only)
     */
    public function show(Institution $institution)
    {
        return response()->json($institution->load(['users', 'dashboards']));
    }

    /**
     * Update the specified institution in storage. (Super Admin only)
     * Use UpdateInstitutionRequest para validação.
     */
    public function update(UpdateInstitutionRequest $request, Institution $institution)
    {
        $institution->update($request->validated());

        if ($request->hasFile('profile_photo')) {
            if ($institution->profile_photo_path) {
                Storage::disk('public')->delete($institution->profile_photo_path);
            }
            $path = $request->file('profile_photo')->store('profile-photos/institutions', 'public');
            $institution->profile_photo_path = $path;
            $institution->save();
        } elseif ($request->boolean('remove_profile_photo')) {
            if ($institution->profile_photo_path) {
                Storage::disk('public')->delete($institution->profile_photo_path);
            }
            $institution->profile_photo_path = null;
            $institution->save();
        }

        return response()->json($institution);
    }

    /**
     * Remove the specified institution from storage. (Super Admin only)
     */
    public function destroy(Institution $institution)
    {
        // Prevenção extra para não deletar instituições com Super Admins
        // if ($institution->users()->where('is_super_admin', true)->exists()) {
        //     return response()->json(['message' => 'Não é possível deletar uma instituição que contém Super Admins.'], 403);
        // }
        
        $institution->delete();
        return response()->json(null, 204);
    }

    /**
     * List all institutions that a specific user has access to.
     * (Accessible by any authenticated user)
     */
    public function indexUsersInstitutions(Request $request)
    {
        // Note: A autorização para este método é feita no Gate, pois qualquer usuário autenticado pode acessá-lo
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (Auth::user()->is_super_admin) {
            $institutions = Institution::all();
        } else {
            $institutions = Auth::user()->institutions;
        }
        return response()->json($institutions);
    }
}