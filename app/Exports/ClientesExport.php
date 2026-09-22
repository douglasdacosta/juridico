<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ClientesExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private Collection $clientes)
    {
    }

    public function collection(): Collection
    {
        return $this->clientes;
    }

    public function headings(): array
    {
        return ['ID', 'Nome', 'E-mail', 'Telefone', 'Status', 'Cidade', 'Estado'];
    }

    public function map($cliente): array
    {
        return [
            $cliente->id,
            $cliente->nome,
            $cliente->email,
            $cliente->telefone,
            $cliente->status,
            $cliente->cidade,
            $cliente->estado,
        ];
    }
}
