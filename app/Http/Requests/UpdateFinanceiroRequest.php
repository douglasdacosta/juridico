<?php

namespace App\Http\Requests;

use App\Models\Processo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateFinanceiroRequest extends FormRequest
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
            'id' => 'required|integer|exists:financeiros,id',
            'cliente_id' => 'required|exists:clientes,id',
            'valor_causa' => 'required|numeric|min:0.01',
            'honorarios' => 'nullable|numeric|min:0',
            'reembolso' => 'nullable|numeric|min:0',
            'parcelado' => 'sometimes|boolean',
            'numero_parcelas' => 'required_if:parcelado,1|nullable|integer|min:1',
            'valor_parcela' => 'required_if:parcelado,1|nullable|numeric|min:0.01',
            'data_primeira_parcela' => 'required_if:parcelado,1|nullable|date',
            'data_pagamento' => 'nullable|date',
            'observacoes' => 'nullable|string',
            'processos' => 'required|array|min:1',
            'processos.*' => 'exists:processos,id',
        ];
    }

    public function messages()
    {
        return [
            'cliente_id.required' => 'O cliente é obrigatório.',
            'cliente_id.exists' => 'Cliente inválido.',
            'valor_causa.required' => 'O valor da causa é obrigatório.',
            'valor_causa.min' => 'O valor da causa deve ser maior que zero.',
            //'status_pagamento.required' => 'O status de pagamento é obrigatório.',
            //'status_pagamento.in' => 'Status de pagamento inválido.',
            'numero_parcelas.required_if' => 'Informe o número de parcelas quando for parcelado.',
            'valor_parcela.required_if' => 'Informe o valor da parcela quando for parcelado.',
            'data_primeira_parcela.required_if' => 'Informe a data da primeira parcela quando for parcelado.',
            'processos.required' => 'Selecione ao menos um processo.',
            'processos.min' => 'Selecione ao menos um processo.',
            'processos.*.exists' => 'Processo inválido.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator)
    {
        if (! $this->isMethod('post')) {
            return;
        }

        $validator->after(function (Validator $validator) {
            $clienteId = (int) $this->input('cliente_id');
            $processosIds = array_filter((array) $this->input('processos', []));

            if ($clienteId && ! empty($processosIds)) {
                $foraDoCliente = Processo::whereIn('id', $processosIds)
                    ->whereDoesntHave('clientes', fn ($q) => $q->where('clientes.id', $clienteId))
                    ->exists();

                if ($foraDoCliente) {
                    $validator->errors()->add('processos', 'Todos os processos vinculados devem pertencer ao cliente selecionado.');
                }
            }

            // status agora é calculado automaticamente; validações específicas de status foram removidas
        });
    }
}
