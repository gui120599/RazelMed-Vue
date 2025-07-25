<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreInstitutionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('manage-platform'); // Apenas Super Admin pode criar instituições
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:institutions'],
            'cnpj' => ['nullable', 'string', 'max:18', 'unique:institutions'],
            'profile_photo' => ['nullable', 'image', 'max:2048'], // 2MB max
        ];
    }

    /**
     * Prepare the data for validation.
     * Este método é executado antes das regras de validação.
     */
    protected function prepareForValidation(): void
    {
        // Limpar máscaras
        $cnpj = preg_replace('/[^0-9]/', '', $this->cnpj);

        // Converter strings vazias (após a limpeza) para NULL
        $cnpj = $cnpj === '' ? null : $cnpj;

        $this->merge([
            'cnpj' => $cnpj,
        ]);
    }

    /**
     * Get custom messages for validation errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.string' => 'O nome deve ser um texto.',
            'name.unique' => 'Já existe uma insituição com esse nome, caso não encontre, verifique se a mesma esteja ativa.',
            'name.max' => 'O nome não pode ter mais de :max caracteres.',
            'cnpj.unique' => 'Este CPF/CNPJ já está cadastrado.',
            'profile_photo.image' => 'O arquivo deve ser uma imagem.',
            'profile_photo.max' => 'A imagem não pode ter mais de :max kilobytes.',
        ];
    }
}