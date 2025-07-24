<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateInstitutionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('manage-platform'); // Apenas Super Admin pode atualizar instituições
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Pega o ID da instituição da rota para ignorar na validação unique
        $institutionId = $this->route('institution');

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('institutions')->ignore($institutionId)],
            'cnpj' => ['nullable', 'string', 'max:18', Rule::unique('institutions')->ignore($institutionId)],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
            'remove_profile_photo' => ['boolean'], // Para indicar se a foto deve ser removida
        ];
    }
}