<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDespesaRequest;
use App\Http\Requests\UpdateDespesaRequest;
use App\Models\Despesa;
use App\Models\Filial;
use Illuminate\Http\Request;

class DespesasController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->baseQuery($request);

        return view('despesas', array_merge($this->formData('pesquisa'), [
            'despesas' => $query->orderByDesc('data_vencimento')->get(),
            'request' => $request,
        ]));
    }

    public function incluir(StoreDespesaRequest $request)
    {
        if ($request->isMethod('post')) {
            $despesa = Despesa::create([
                'descricao' => $request->input('descricao'),
                'categoria' => $request->input('categoria'),
                'valor' => $request->input('valor'),
                'data_vencimento' => $request->input('data_vencimento'),
                'filial_id' => $request->input('filial_id') ?: null,
                'observacoes' => $request->input('observacoes'),
            ]);

            $despesa->status = $despesa->computeStatus();
            $despesa->save();

            return redirect()->route('despesas')->with('success', 'Despesa incluída com sucesso.');
        }

        return view('despesas', $this->formData('incluir'));
    }

    public function alterar(UpdateDespesaRequest $request)
    {
        if ($request->isMethod('post')) {
            $despesa = Despesa::query()->findOrFail((int) $request->input('id'));

            $despesa->update([
                'descricao' => $request->input('descricao'),
                'categoria' => $request->input('categoria'),
                'valor' => $request->input('valor'),
                'data_vencimento' => $request->input('data_vencimento'),
                'filial_id' => $request->input('filial_id') ?: null,
                'observacoes' => $request->input('observacoes'),
            ]);

            $despesa->status = $despesa->computeStatus();
            $despesa->save();

            return redirect()->route('despesas')->with('success', 'Despesa atualizada com sucesso.');
        }

        $despesa = Despesa::query()->with('filial')->findOrFail((int) $request->input('id'));

        return view('despesas', array_merge($this->formData('alterar'), [
            'despesa' => $despesa,
        ]));
    }

    public function pagar(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:despesas,id',
            'data_pagamento' => 'nullable|date',
            'valor_pago' => 'nullable|numeric|min:0',
        ]);

        $despesa = Despesa::findOrFail((int) $validated['id']);
        $despesa->data_pagamento = $validated['data_pagamento'] ?? now()->format('Y-m-d');
        $despesa->valor_pago = $validated['valor_pago'] ?? $despesa->valor;
        $despesa->status = $despesa->computeStatus();
        $despesa->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('despesas')->with('success', 'Despesa marcada como paga.');
    }

    public function excluir(Request $request)
    {
        $validated = $request->validate(['id' => 'required|integer|exists:despesas,id']);

        Despesa::findOrFail((int) $validated['id'])->delete();

        return redirect()->route('despesas')->with('success', 'Despesa excluída com sucesso.');
    }

    private function formData(string $tela): array
    {
        return [
            'tela' => $tela,
            'nome_tela' => 'Despesas',
            'rotaAlterar' => 'alterar-despesas',
            'rotaIncluir' => 'incluir-despesas',
            'statusOptions' => Despesa::STATUS_OPTIONS,
            'filiaisOptions' => Filial::query()->orderBy('nome')->pluck('nome', 'id'),
        ];
    }

    private function baseQuery(Request $request)
    {
        $query = Despesa::query()->with('filial');

        if ($request->filled('descricao')) {
            $query->where('descricao', 'like', '%' . trim((string) $request->input('descricao')) . '%');
        }

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->input('categoria'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        return $query;
    }
}
