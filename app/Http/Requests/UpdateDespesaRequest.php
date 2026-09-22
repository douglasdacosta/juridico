<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDespesaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        if (! $this->isMethod('post')) {
            return [];
        }

        return [
            'id' => 'required|integer|exists:despesas,id',
            'descricao' => 'required|string|max:255',
            'categoria' => 'nullable|string|max:50',
            'valor' => 'required|numeric|min:0.01',
            'data_vencimento' => 'required|date',
            'filial_id' => 'nullable|exists:filiais,id',
            'observacoes' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'descricao.required' => 'A descrição é obrigatória.',
            'valor.required' => 'O valor é obrigatório.',
            'valor.min' => 'O valor deve ser maior que zero.',
            'data_vencimento.required' => 'A data de vencimento é obrigatória.',
            'filial_id.exists' => 'Filial inválida.',
        ];
    }
}
