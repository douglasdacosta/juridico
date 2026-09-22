<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\ModeloDocumento;
use App\Models\Processo;

/**
 * Substitui os placeholders de um ModeloDocumento pelos dados reais de um
 * processo/cliente, gerando o HTML final que será convertido em PDF.
 */
class GeradorDocumentoService
{
    public function gerar(ModeloDocumento $modelo, Processo $processo, ?Cliente $cliente = null): string
    {
        $cliente = $cliente ?? $processo->clientes->first();

        $placeholders = [
            '{{cliente.nome}}' => $cliente->nome ?? '',
            '{{cliente.cpf}}' => $cliente->cpf ?? '',
            '{{cliente.cnpj}}' => $cliente->cnpj ?? '',
            '{{cliente.endereco}}' => $this->enderecoCompleto($cliente),
            '{{cliente.telefone}}' => $cliente->telefone ?? '',
            '{{cliente.email}}' => $cliente->email ?? '',
            '{{processo.numero}}' => $processo->numero_processo ?? '',
            '{{processo.vara}}' => $processo->vara_tribunal ?? '',
            '{{processo.tipo_acao}}' => $processo->tipoAcao?->nome ?? ($processo->tipo_acao ?? ''),
            '{{processo.status}}' => ucfirst($processo->status ?? ''),
            '{{data.hoje}}' => now()->format('d/m/Y'),
            '{{escritorio.nome}}' => config('adminlte.logo_img_alt', env('APP_NAME')),
        ];

        return str_replace(array_keys($placeholders), array_values($placeholders), $modelo->corpo);
    }

    /**
     * Placeholders conhecidos, exibidos na tela de cadastro do modelo para consulta rápida.
     */
    public function placeholdersDisponiveis(): array
    {
        return [
            '{{cliente.nome}}', '{{cliente.cpf}}', '{{cliente.cnpj}}', '{{cliente.endereco}}',
            '{{cliente.telefone}}', '{{cliente.email}}',
            '{{processo.numero}}', '{{processo.vara}}', '{{processo.tipo_acao}}', '{{processo.status}}',
            '{{data.hoje}}', '{{escritorio.nome}}',
        ];
    }

    private function enderecoCompleto(?Cliente $cliente): string
    {
        if (! $cliente) {
            return '';
        }

        $partes = array_filter([
            $cliente->endereco,
            $cliente->numero,
            $cliente->bairro,
            $cliente->cidade,
            $cliente->estado,
            $cliente->cep,
        ]);

        return implode(', ', $partes);
    }
}
