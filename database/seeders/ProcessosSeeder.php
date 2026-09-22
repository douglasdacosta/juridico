<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Filial;
use App\Models\Processo;
use App\Models\TipoAcao;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProcessosSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake('pt_BR');
        $clientes = Cliente::all();
        $filiais = Filial::all();
        $tiposAcao = TipoAcao::pluck('id');
        $responsaveis = User::query()->where('perfil_acesso', 2)->pluck('id');

        if ($clientes->isEmpty()) {
            return;
        }

        $varas = [
            '1ª Vara Cível de São Paulo',
            '2ª Vara Cível do Rio de Janeiro',
            '3ª Vara do Trabalho de Belo Horizonte',
            'Vara de Família e Sucessões de São Paulo',
            'Vara da Fazenda Pública de Curitiba',
            'Juizado Especial Cível de Campinas',
        ];

        $status = ['ativo', 'ativo', 'ativo', 'suspenso', 'arquivado', 'encerrado'];

        for ($i = 1; $i <= 12; $i++) {
            $dataAbertura = $faker->dateTimeBetween('-2 years', '-1 month');
            $statusProcesso = $status[array_rand($status)];

            // Número determinístico (sem aleatoriedade) para que o seeder seja
            // idempotente: rodar novamente não deve duplicar os processos.
            $processo = Processo::firstOrCreate(
                ['numero_processo' => sprintf('%07d-00.2024.8.26.%04d', $i, $i)],
                [
                    'vara_tribunal' => $varas[array_rand($varas)],
                    'tipo_acao' => (string) ($tiposAcao->isNotEmpty() ? $tiposAcao->random() : ''),
                    'data_abertura' => $dataAbertura,
                    'data_encerramento' => $statusProcesso === 'encerrado' ? $faker->dateTimeBetween($dataAbertura, 'now') : null,
                    'status' => $statusProcesso,
                    'responsavel_id' => $responsaveis->isNotEmpty() ? $responsaveis->random() : null,
                    'observacoes' => $faker->sentence(12),
                ]
            );

            if (! $processo->clientes()->exists()) {
                $processo->clientes()->attach(
                    $clientes->random(rand(1, 2))->pluck('id')->toArray(),
                    ['papel_cliente' => 'Autor']
                );
            }

            if ($filiais->isNotEmpty() && ! $processo->filiais()->exists()) {
                $processo->filiais()->attach($filiais->random()->id);
            }
        }
    }
}
