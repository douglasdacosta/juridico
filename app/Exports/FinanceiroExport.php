<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FinanceiroExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private Collection $lancamentos)
    {
    }

    public function collection(): Collection
    {
        return $this->lancamentos;
    }

    public function headings(): array
    {
        return ['ID', 'Cliente', 'Processos', 'Valor da Causa', 'Honorários', 'Reembolso', 'Data Pagamento', 'Status'];
    }

    public function map($lancamento): array
    {
        return [
            $lancamento->id,
            $lancamento->cliente->nome ?? '-',
            $lancamento->processos->pluck('numero_processo')->implode(' | '),
            (float) $lancamento->valor_causa,
            $lancamento->honorarios !== null ? (float) $lancamento->honorarios : null,
            $lancamento->reembolso !== null ? (float) $lancamento->reembolso : null,
            $lancamento->data_pagamento?->format('d/m/Y'),
            $lancamento->computed_status_label,
        ];
    }
}
