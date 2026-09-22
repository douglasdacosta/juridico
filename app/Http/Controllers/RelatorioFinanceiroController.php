<?php

namespace App\Http\Controllers;

use App\Models\Despesa;
use App\Models\Financeiro;
use App\Models\FinanceiroParcela;
use App\Services\FluxoCaixaCalculator;
use App\Support\PdfExporter;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RelatorioFinanceiroController extends Controller
{
    /**
     * Fluxo de caixa: entradas (financeiro pago) x saídas (despesas pagas) por mês.
     */
    public function fluxoCaixa(Request $request)
    {
        $meses = max(1, min(24, (int) $request->input('meses', 6)));
        $linhas = FluxoCaixaCalculator::calcular($meses);

        return view('relatorios.fluxo-caixa', [
            'linhas' => $linhas,
            'meses' => $meses,
            'totalEntradas' => array_sum(array_column($linhas, 'entradas')),
            'totalSaidas' => array_sum(array_column($linhas, 'saidas')),
        ]);
    }

    public function exportarFluxoCaixaPdf(Request $request)
    {
        $meses = max(1, min(24, (int) $request->input('meses', 6)));
        $linhas = FluxoCaixaCalculator::calcular($meses);

        return PdfExporter::stream('relatorios.fluxo-caixa-pdf', [
            'linhas' => $linhas,
            'meses' => $meses,
            'totalEntradas' => array_sum(array_column($linhas, 'entradas')),
            'totalSaidas' => array_sum(array_column($linhas, 'saidas')),
        ], 'fluxo-caixa.pdf');
    }

    /**
     * Relatório de inadimplência: lançamentos, parcelas e despesas vencidas, ordenados por dias de atraso.
     */
    public function inadimplencia()
    {
        $hoje = now()->startOfDay();

        $lancamentosVencidos = Financeiro::query()
            ->with('cliente')
            ->where('parcelado', false)
            ->whereNull('valor_pago')
            ->whereNotNull('data_pagamento')
            ->where('data_pagamento', '<', $hoje)
            ->get()
            ->map(fn (Financeiro $f) => [
                'tipo' => 'Honorário (à vista)',
                'descricao' => $f->cliente->nome ?? '-',
                'valor' => (float) $f->valor_causa,
                'vencimento' => Carbon::parse($f->data_pagamento),
                'dias_atraso' => (int) Carbon::parse($f->data_pagamento)->diffInDays($hoje),
                'link' => route('alterar-financeiro', ['id' => $f->id]),
            ]);

        $parcelasVencidas = FinanceiroParcela::query()
            ->with('financeiro.cliente')
            ->whereNull('data_pagamento')
            ->where('data_vencimento', '<', $hoje)
            ->get()
            ->map(fn (FinanceiroParcela $p) => [
                'tipo' => 'Parcela nº ' . $p->numero,
                'descricao' => $p->financeiro->cliente->nome ?? '-',
                'valor' => (float) $p->valor,
                'vencimento' => Carbon::parse($p->data_vencimento),
                'dias_atraso' => (int) Carbon::parse($p->data_vencimento)->diffInDays($hoje),
                'link' => route('alterar-financeiro', ['id' => $p->financeiro_id]),
            ]);

        $despesasVencidas = Despesa::atrasadas()
            ->get()
            ->map(fn (Despesa $d) => [
                'tipo' => 'Despesa (' . ($d->categoria ?? 'Sem categoria') . ')',
                'descricao' => $d->descricao,
                'valor' => (float) $d->valor,
                'vencimento' => Carbon::parse($d->data_vencimento),
                'dias_atraso' => (int) Carbon::parse($d->data_vencimento)->diffInDays($hoje),
                'link' => route('alterar-despesas', ['id' => $d->id]),
            ]);

        $itens = $lancamentosVencidos
            ->concat($parcelasVencidas)
            ->concat($despesasVencidas)
            ->sortByDesc('dias_atraso')
            ->values();

        return view('relatorios.inadimplencia', [
            'itens' => $itens,
            'totalReceberAtraso' => $lancamentosVencidos->sum('valor') + $parcelasVencidas->sum('valor'),
            'totalPagarAtraso' => $despesasVencidas->sum('valor'),
        ]);
    }
}
