<?php

namespace App\Http\Requests;

use App\Models\Compromisso;
use Illuminate\Foundation\Http\FormRequest;

class StoreCompromissoRequest extends FormRequest
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
            'titulo' => 'required|string|max:255',
            'tipo' => 'required|in:' . implode(',', array_keys(Compromisso::TIPO_OPTIONS)),
            'data_hora' => 'required|date',
            'processo_id' => 'nullable|exists:processos,id',
            'responsavel_id' => 'nullable|exists:users,id',
            'observacoes' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'titulo.required' => 'O título é obrigatório.',
            'tipo.required' => 'O tipo é obrigatório.',
            'tipo.in' => 'Tipo de compromisso inválido.',
            'data_hora.required' => 'A data e hora são obrigatórias.',
            'processo_id.exists' => 'Processo inválido.',
            'responsavel_id.exists' => 'Responsável inválido.',
        ];
    }
}
