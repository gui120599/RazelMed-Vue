<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInstitutionRequest;
use App\Http\Requests\UpdateInstitutionRequest;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class InstitutionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Recupera todos os institutiones, ordenados pelo nome e paginados
        $institutions = Institution::with('users') 
            ->orderBy('name')
            ->get();
        $allUsers = User::all(); // Carrega TODOS os usuários do sistema

        // Retorna a view 'Institutions/Index' do Vue com os dados dos institutiones
        return Inertia::render('institution/Index', [
            'institutions' => $institutions,
             'allUsers' => $allUsers,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Retorna a view 'Institutions/Create' do Vue
        return Inertia::render('institution/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInstitutionRequest $request)
    {
        // Os dados já foram validados pela StoreInstitutionRequest
        $data = $request->validated();

        // Lógica para upload da imagem
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photos', 'public'); // Armazena em storage/app/public/profile-photos
            $data['profile_photo_path'] = $path; // Salva o caminho no array de dados
        }

        // Cria o novo institutione no banco de dados
        Institution::create($data);

        // Redireciona de volta para a lista de institutiones com uma mensagem de sucesso
        return redirect()->route('institutions.index')
            ->with('success', 'Instituição cadastrada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Institution $institution)
    {
        // Retorna a view 'Institutions/Show' do Vue com os dados do institutione específico
        return Inertia::render('Institutions/Show', [
            'institution' => $institution,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Institution $institution)
    {
        // Retorna a view 'Institutions/Edit' do Vue com os dados do institutione para edição
        return Inertia::render('Institutions/Edit', [
            'institution' => $institution,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInstitutionRequest $request, Institution $institution)
    {
        // Os dados já foram validados pela UpdateInstitutionRequest
        $data = $request->validated();

        // Lógica para upload/atualização da imagem
        if ($request->hasFile('profile_photo')) {
            // Se já existir uma foto, a deleta para evitar lixo
            if ($institution->profile_photo_path) {
                Storage::disk('public')->delete($institution->profile_photo_path);
            }
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $data['profile_photo_path'] = $path;
        } elseif (isset($data['profile_photo_remove']) && $data['profile_photo_remove']) {
            // Se o usuário marcou para remover a foto existente
            if ($institution->profile_photo_path) {
                Storage::disk('public')->delete($institution->profile_photo_path);
            }
            $data['profile_photo_path'] = null; // Define o caminho como nulo no BD
        } else {
            // Se não tem nova foto e não marcou para remover, mantém a existente
            unset($data['profile_photo_path']); // Remove do array de dados para não sobrescrever
        }

        // Atualiza o institutione no banco de dados
        $institution->update($data);

        // Redireciona de volta para a lista de institutiones com uma mensagem de sucesso
        return redirect()->route('institutions.index')
            ->with('success', 'Institutione atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Institution $institution)
    {
        // softDelete o institutione
        $institution->delete();

        // Redireciona de volta para a lista de institutiones com uma mensagem de sucesso
        return redirect()->route('institutions.index')
            ->with('success', 'Institutione deletado com sucesso!');
    }

    // NOVO: Método para anexar um usuário a uma instituição
    public function attachUser(Institution $institution, Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $institution->users()->syncWithoutDetaching([$request->user_id]);

        return redirect()->back()->with('success', 'Usuário adicionado à instituição com sucesso!');
    }

    // NOVO: Método para desanexar um usuário de uma instituição
    public function detachUser(Institution $institution, Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $institution->users()->detach($request->user_id);

        return redirect()->back()->with('success', 'Usuário removido da instituição com sucesso!');
    }
}
