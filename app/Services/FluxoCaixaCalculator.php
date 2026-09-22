<?php

namespace App\Services;

use App\Models\Despesa;
use App\Models\Financeiro;
use App\Models\FinanceiroParcela;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Agrega entradas (Financeiro pago) e saídas (Despesa paga) por mês.
 * Reaproveitado pelo relatório de Fluxo de Caixa e pelo Dashboard.
 */
class FluxoCaixaCalculator
{
    /**
     * @return array<int, array{mes: string, entradas: float, saidas: float, saldo: float}>
     */
    public static function calcular(int $meses): array
    {
        $meses = max(1, min(24, $meses));
        $inicio = now()->startOfMonth()->subMonths($meses - 1);

        // Agrupamento por mês feito em PHP (em vez de DATE_FORMAT/strftime no SQL) para manter
        // compatibilidade entre MySQL (produção) e SQLite (suíte de testes).
        $agruparPorMes = fn (Collection $registros, string $campoData, string $campoValor) => $registros
            ->groupBy(fn ($registro) => Carbon::parse($registro->{$campoData})->format('Y-m'))
            ->map(fn ($grupo) => $grupo->sum($campoValor));

        $entradasAvista = $agruparPorMes(
            Financeiro::query()
                ->where('parcelado', false)
                ->whereNotNull('valor_pago')
                ->whereNotNull('data_pagamento')
                ->where('data_pagamento', '>=', $inicio)
                ->get(['data_pagamento', 'valor_pago']),
            'data_pagamento',
            'valor_pago'
        );

        $entradasParceladas = $agruparPorMes(
            FinanceiroParcela::query()
                ->whereNotNull('data_pagamento')
                ->where('data_pagamento', '>=', $inicio)
                ->get(['data_pagamento', 'valor_pago']),
            'data_pagamento',
            'valor_pago'
        );

        $saidas = $agruparPorMes(
            Despesa::query()
                ->whereNotNull('data_pagamento')
                ->where('data_pagamento', '>=', $inicio)
                ->get(['data_pagamento', 'valor_pago']),
            'data_pagamento',
            'valor_pago'
        );

        $linhas = [];
        $cursor = $inicio->copy();

        for ($i = 0; $i < $meses; $i++) {
            $chave = $cursor->format('Y-m');
            $entradas = (float) ($entradasAvista[$chave] ?? 0) + (float) ($entradasParceladas[$chave] ?? 0);
            $saida = (float) ($saidas[$chave] ?? 0);

            $linhas[] = [
                'mes' => $cursor->translatedFormat('M/Y'),
                'entradas' => $entradas,
                'saidas' => $saida,
                'saldo' => $entradas - $saida,
            ];

            $cursor->addMonth();
        }

        return $linhas;
    }
}
