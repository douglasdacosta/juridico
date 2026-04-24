<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProcessosRequest extends FormRequest
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
            'id' => 'required|integer|exists:processos,id',
            'numero_processo' => 'required|string|max:50|unique:processos,numero_processo,' . $this->input('id') . ',id',
            'vara_tribunal' => 'required|string|max:255',
            'tipo_acao' => 'required|string|max:100',
            'data_abertura' => 'required|date',
            'data_encerramento' => 'nullable|date',
            'status' => 'required|in:ativo,encerrado,suspenso,arquivado',
            'responsavel_id' => 'nullable|exists:users,id',
            'observacoes' => 'nullable|string',
            'clientes' => 'required|array|min:1',
            'clientes.*' => 'exists:clientes,id',
            'filiais' => 'nullable|array',
            'filiais.*' => 'exists:filiais,id',
        ];
    }

    public function messages()
    {
        return [
            'numero_processo.required' => 'O número do processo é obrigatório.',
            'numero_processo.unique' => 'Já existe um processo com esse número.',
            'vara_tribunal.required' => 'A vara/tribunal é obrigatória.',
            'tipo_acao.required' => 'O tipo de ação é obrigatório.',
            'data_abertura.required' => 'A data de abertura é obrigatória.',
            'status.required' => 'O status é obrigatório.',
            'id.required' => 'O identificador do processo é obrigatório.',
            'clientes.required' => 'Selecione ao menos um cliente vinculado ao processo.',
        ];
    }
}
