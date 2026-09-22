<?php

namespace Database\Seeders;

use App\Models\ModeloDocumento;
use Illuminate\Database\Seeder;

class ModelosDocumentoSeeder extends Seeder
{
    public function run(): void
    {
        $modelos = [
            [
                'nome' => 'Contrato de Honorários Advocatícios',
                'tipo' => ModeloDocumento::TIPO_CONTRATO,
                'corpo' => '<h3>CONTRATO DE HONORÁRIOS ADVOCATÍCIOS</h3>'
                    . '<p>CONTRATANTE: {{cliente.nome}}, CPF/CNPJ {{cliente.cpf}}{{cliente.cnpj}}, residente em {{cliente.endereco}}.</p>'
                    . '<p>Processo nº {{processo.numero}}, em trâmite na {{processo.vara}}.</p>'
                    . '<p>Data: {{data.hoje}}</p><p>{{escritorio.nome}}</p>',
            ],
            [
                'nome' => 'Petição Inicial Padrão',
                'tipo' => ModeloDocumento::TIPO_PETICAO,
                'corpo' => '<h3>EXCELENTÍSSIMO(A) SENHOR(A) DOUTOR(A) JUIZ(A) DE DIREITO DA {{processo.vara}}</h3>'
                    . '<p>{{cliente.nome}}, por meio de seu(sua) advogado(a), vem respeitosamente à presença de Vossa Excelência propor a presente ação de {{processo.tipo_acao}}.</p>'
                    . '<p>Nestes termos, pede deferimento.</p><p>{{data.hoje}}</p>',
            ],
            [
                'nome' => 'Procuração Ad Judicia',
                'tipo' => ModeloDocumento::TIPO_OUTRO,
                'corpo' => '<h3>PROCURAÇÃO</h3>'
                    . '<p>OUTORGANTE: {{cliente.nome}}, telefone {{cliente.telefone}}, e-mail {{cliente.email}}.</p>'
                    . '<p>Confere amplos poderes para representá-lo(a) no processo nº {{processo.numero}}.</p>'
                    . '<p>{{data.hoje}}</p>',
            ],
        ];

        foreach ($modelos as $modelo) {
            ModeloDocumento::firstOrCreate(
                ['nome' => $modelo['nome']],
                ['tipo' => $modelo['tipo'], 'corpo' => $modelo['corpo'], 'ativo' => true]
            );
        }
    }
}
