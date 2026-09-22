<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProcessosExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private Collection $processos)
    {
    }

    public function collection(): Collection
    {
        return $this->processos;
    }

    public function headings(): array
    {
        return ['ID', 'Número', 'Vara/Tribunal', 'Tipo', 'Status', 'Responsável', 'Clientes', 'Filiais'];
    }

    public function map($processo): array
    {
        return [
            $processo->id,
            $processo->numero_processo,
            $processo->vara_tribunal,
            $processo->tipoAcao?->nome ?? ($processo->tipo_acao ?? ''),
            $processo->status,
            $processo->responsavel->name ?? '',
            $processo->clientes->pluck('nome')->implode(' | '),
            $processo->filiais->pluck('nome')->implode(' | '),
        ];
    }
}
