<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAndamentosRequest extends FormRequest
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
            'processo_id' => 'required|exists:processos,id',
            'tipo' => 'required|string|max:100',
            'data_andamento' => 'required|date',
            'descricao' => 'required|string',
            'usuario_id' => 'required|exists:users,id',
            'created_by' => 'nullable|exists:users,id',
        ];
    }

    public function messages()
    {
        return [
            'processo_id.required' => 'O processo é obrigatório.',
            'tipo.required' => 'O tipo de andamento é obrigatório.',
            'data_andamento.required' => 'A data do andamento é obrigatória.',
            'descricao.required' => 'A descrição do andamento é obrigatória.',
            'usuario_id.required' => 'O usuário responsável é obrigatório.',
        ];
    }
}
