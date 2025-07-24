<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('manage-platform'); // Apenas Super Admin pode atualizar usuários
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Pega o ID do usuário da rota
        $userId = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
            'password' => ['nullable', 'string', 'min:8'],
            'prefer_institution_id' => ['nullable', 'exists:institutions,id'],
            // A validação de 'is_super_admin' precisa de lógica extra para evitar desabilitar a si mesmo
            'is_super_admin' => [
                'boolean',
                // Regra condicional: se o usuário atual é o mesmo que está sendo atualizado E ele é Super Admin,
                // ele não pode desabilitar seu próprio status de Super Admin.
                // Se for outro Super Admin desabilitando este, ou não for Super Admin, a regra 'boolean' normal aplica.
                function ($attribute, $value, $fail) use ($userId) {
                    if (Auth::id() === (int) $userId && Auth::user()->is_super_admin && $value === false) {
                        $fail('Você não pode remover seu próprio status de Super Admin.');
                    }
                },
            ],
            'institution_ids' => ['array'],
            'institution_ids.*' => ['exists:institutions,id'],
            'dashboard_ids' => ['array'],
            'dashboard_ids.*' => ['exists:dashboards,id'],
        ];
    }
}