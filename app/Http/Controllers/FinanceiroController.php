<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFinanceiroRequest;
use App\Http\Requests\UpdateFinanceiroRequest;
use App\Models\Cliente;
use App\Models\Financeiro;
use App\Models\FinanceiroParcela;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinanceiroController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->baseQuery($request);

        return view('financeiro', [
            'tela' => 'pesquisa',
            'nome_tela' => 'Financeiro',
            'rotaAlterar' => 'alterar-financeiro',
            'rotaIncluir' => 'incluir-financeiro',
            'lancamentos' => $query->orderByDesc('id')->get(),
            'statusOptions' => Financeiro::STATUS_OPTIONS,
            'request' => $request,
        ]);
    }

    public function incluir(StoreFinanceiroRequest $request)
    {
        if ($request->isMethod('post')) {
            $financeiro = Financeiro::create([
                'cliente_id' => $request->input('cliente_id'),
                'valor_causa' => $request->input('valor_causa'),
                'honorarios' => $request->input('honorarios'),
                'reembolso' => $request->input('reembolso'),
                'data_pagamento' => $request->input('data_pagamento'),
                'parcelado' => (bool) $request->input('parcelado', false),
                'numero_parcelas' => $request->input('numero_parcelas'),
                'valor_parcela' => $request->input('valor_parcela'),
                'data_primeira_parcela' => $request->input('data_primeira_parcela'),
                'observacoes' => $request->input('observacoes'),
            ]);

            $financeiro->processos()->sync($request->input('processos', []));

            // gerar parcelas se parcelado
            if ((bool) $financeiro->parcelado) {
                // remover parcelas pré-existentes por segurança
                $financeiro->parcelas()->delete();

                $numero = (int) $financeiro->numero_parcelas;
                $valorParcela = $financeiro->valor_parcela;
                $dataPrimeira = $financeiro->data_primeira_parcela ? Carbon::parse($financeiro->data_primeira_parcela) : null;

                for ($i = 0; $i < $numero; $i++) {
                    $vencimento = $dataPrimeira ? $dataPrimeira->copy()->addMonths($i) : null;

                    FinanceiroParcela::create([
                        'financeiro_id' => $financeiro->id,
                        'numero' => $i + 1,
                        'valor' => $valorParcela,
                        'data_vencimento' => $vencimento ? $vencimento->format('Y-m-d') : now()->format('Y-m-d'),
                    ]);
                }
            }

            // calcular e gravar status com base nas parcelas / pagamento
            $financeiro->status_pagamento = $financeiro->computeStatus();
            $financeiro->save();

            return redirect()->route('financeiro')->with('success', 'Lançamento financeiro incluído com sucesso.');
        }

        return view('financeiro', $this->formData('incluir'));
    }

    public function alterar(UpdateFinanceiroRequest $request)
    {
        if ($request->isMethod('post')) {
            $financeiro = Financeiro::query()->with('parcelas')->findOrFail((int) $request->input('id'));

            $requestedParcelado = (bool) $request->input('parcelado', false);

            // impedir alteração do modo quando já está parcelado
            if ($financeiro->parcelado && ! $requestedParcelado) {
                return redirect()->back()->withInput()->with('error', 'Não é permitido alterar um lançamento parcelado para à vista após a criação das parcelas.');
            }

            $financeiro->update([
                'cliente_id' => $request->input('cliente_id'),
                'valor_causa' => $request->input('valor_causa'),
                'honorarios' => $request->input('honorarios'),
                'reembolso' => $request->input('reembolso'),
                'data_pagamento' => $request->input('data_pagamento'),
                'parcelado' => $requestedParcelado,
                'numero_parcelas' => $request->input('numero_parcelas'),
                'valor_parcela' => $request->input('valor_parcela'),
                'data_primeira_parcela' => $request->input('data_primeira_parcela'),
                'observacoes' => $request->input('observacoes'),
            ]);

            $financeiro->processos()->sync($request->input('processos', []));

            // Gerenciar parcelas sem apagar as existentes: apenas criar novas se necessário.
            if ($requestedParcelado) {
                $existingCount = $financeiro->parcelas()->count();
                $numero = (int) $financeiro->numero_parcelas;
                $valorParcela = $financeiro->valor_parcela;
                $dataPrimeira = $financeiro->data_primeira_parcela ? Carbon::parse($financeiro->data_primeira_parcela) : null;

                if ($existingCount === 0) {
                    // criar todas as parcelas
                    for ($i = 0; $i < $numero; $i++) {
                        $vencimento = $dataPrimeira ? $dataPrimeira->copy()->addMonths($i) : null;

                        FinanceiroParcela::create([
                            'financeiro_id' => $financeiro->id,
                            'numero' => $i + 1,
                            'valor' => $valorParcela,
                            'data_vencimento' => $vencimento ? $vencimento->format('Y-m-d') : now()->format('Y-m-d'),
                        ]);
                    }
                } elseif ($numero > $existingCount) {
                    // adicionar apenas as parcelas faltantes
                    for ($i = $existingCount; $i < $numero; $i++) {
                        $vencimento = $dataPrimeira ? $dataPrimeira->copy()->addMonths($i) : null;

                        FinanceiroParcela::create([
                            'financeiro_id' => $financeiro->id,
                            'numero' => $i + 1,
                            'valor' => $valorParcela,
                            'data_vencimento' => $vencimento ? $vencimento->format('Y-m-d') : now()->format('Y-m-d'),
                        ]);
                    }
                } elseif ($numero < $existingCount) {
                    // não permitir reduzir o número de parcelas após criação
                    return redirect()->back()->withInput()->with('error', 'Redução do número de parcelas não é permitida após a criação das parcelas.');
                }
            } else {
                // se não parcelado e não era parcelado antes, nada a fazer; se era parcelado, bloqueado acima
            }

                // recalcula e grava status
                $financeiro->status_pagamento = $financeiro->computeStatus();
                $financeiro->save();

            return redirect()->route('financeiro')->with('success', 'Lançamento financeiro atualizado com sucesso.');
        }

        $financeiro = Financeiro::query()->with(['cliente', 'processos'])->findOrFail((int) $request->input('id'));

        return view('financeiro', array_merge($this->formData('alterar'), [
            'financeiro' => $financeiro,
        ]));
    }

    public function pagarParcela(Request $request)
    {
        $validated = $request->validate([
            'parcela_id' => 'required|integer|exists:financeiro_parcelas,id',
            'data_pagamento' => 'nullable|date',
            'valor_pago' => 'nullable|numeric|min:0',
        ]);

        $parcela = FinanceiroParcela::findOrFail((int) $validated['parcela_id']);
        $parcela->data_pagamento = $validated['data_pagamento'] ?? now()->format('Y-m-d');
        $parcela->status = 'pago';
        if (isset($validated['valor_pago'])) {
            $parcela->valor_pago = $validated['valor_pago'];
        } else {
            $parcela->valor_pago = $parcela->valor;
        }
        $parcela->save();

        // atualizar status do financeiro pai
        $financeiro = $parcela->financeiro()->first();
        if ($financeiro) {
            $financeiro->status_pagamento = $financeiro->computeStatus();
            $financeiro->save();
        }

        return response()->json(['success' => true]);
    }

    public function pagarLancamento(Request $request)
    {
        $validated = $request->validate([
            'financeiro_id' => 'required|integer|exists:financeiros,id',
            'data_pagamento' => 'nullable|date',
            'valor_pago' => 'nullable|numeric|min:0',
        ]);

        $financeiro = Financeiro::findOrFail((int) $validated['financeiro_id']);

        $financeiro->data_pagamento = $validated['data_pagamento'] ?? now()->format('Y-m-d');
        $financeiro->valor_pago = $validated['valor_pago'] ?? $financeiro->valor_causa;
        $financeiro->status_pagamento = $financeiro->computeStatus();
        $financeiro->save();

        return response()->json(['success' => true]);
    }

    public function excluir(Request $request)
    {
        $validated = $request->validate(['id' => 'required|integer']);

        $financeiro = Financeiro::findOrFail((int) $validated['id']);
        $financeiro->processos()->detach();
        $financeiro->delete();

        return redirect()->route('financeiro')->with('success', 'Lançamento financeiro excluído com sucesso.');
    }

    private function formData(string $tela): array
    {
        return [
            'tela' => $tela,
            'nome_tela' => 'Financeiro',
            'rotaAlterar' => 'alterar-financeiro',
            'rotaIncluir' => 'incluir-financeiro',
            'statusOptions' => Financeiro::STATUS_OPTIONS,
        ];
    }

    private function baseQuery(Request $request)
    {
        $query = Financeiro::query()->with(['cliente', 'processos']);

        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', (int) $request->input('cliente_id'));
        }

        if ($request->filled('numero_processo')) {
            $numeroProcesso = trim((string) $request->input('numero_processo'));
            $query->whereHas('processos', fn ($q) => $q->where('numero_processo', 'like', "%{$numeroProcesso}%"));
        }

        if ($request->filled('status_pagamento')) {
            $query->where('status_pagamento', $request->input('status_pagamento'));
        }

        return $query;
    }
}
