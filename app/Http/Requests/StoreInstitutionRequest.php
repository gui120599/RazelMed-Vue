<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // <-- Importar a classe Rule

class StoreInstitutionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'reduced_name' => ['nullable', 'string', 'max:255'],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                // Regra unique que só verifica unicidade para valores NÃO NULOS
                Rule::unique('institutions', 'email')->where(fn($query) => $query->whereNotNull('email')),
            ],
            // Ajuste o max para o tamanho sem máscara
            'phone' => ['nullable', 'string', 'max:11'], // Telefone sem máscara (ex: 11 dígitos)
            'cnpj' => [
                'nullable',
                'string',
                'max:14', // CPF (11) / CNPJ (14) sem máscara
                // Regra unique que só verifica unicidade para valores NÃO NULOS
                Rule::unique('institutions', 'cnpj')->where(fn($query) => $query->whereNotNull('cnpj')),
            ],
            'address' => ['nullable', 'string'],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
        ];
    }

    /**
     * Prepare the data for validation.
     * Este método é executado antes das regras de validação.
     */
    protected function prepareForValidation(): void
    {
        // Limpar máscaras
        $phone = preg_replace('/[^0-9]/', '', $this->phone);
        $cnpj = preg_replace('/[^0-9]/', '', $this->cnpj);

        // Converter strings vazias (após a limpeza) para NULL
        $email = $this->email === '' ? null : $this->email; // Garante que e-mail vazio seja null
        $phone = $phone === '' ? null : $phone;
        $cnpj = $cnpj === '' ? null : $cnpj;

        $this->merge([
            'email' => $email,
            'phone' => $phone,
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
            'name.max' => 'O nome não pode ter mais de :max caracteres.',
            'email.email' => 'O e-mail deve ser um endereço de e-mail válido.',
            'email.unique' => 'Este e-mail já está em uso.',
            'cnpj.unique' => 'Este CNPJ já está cadastrado.',
            'profile_photo.image' => 'O arquivo deve ser uma imagem.',
            'profile_photo.max' => 'A imagem não pode ter mais de :max kilobytes.',
        ];
    }
}