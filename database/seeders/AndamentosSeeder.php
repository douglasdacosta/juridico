<?php

namespace Database\Seeders;

use App\Models\Andamento;
use App\Models\Processo;
use App\Models\TipoAcao;
use App\Models\User;
use Illuminate\Database\Seeder;

class AndamentosSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake('pt_BR');
        $tiposAcao = TipoAcao::all();
        $usuarios = User::query()->where('perfil_acesso', 2)->pluck('id');

        if ($tiposAcao->isEmpty() || $usuarios->isEmpty()) {
            return;
        }

        $descricoes = [
            'Petição inicial protocolada junto ao tribunal.',
            'Audiência de conciliação realizada sem acordo entre as partes.',
            'Decisão judicial deferindo o pedido de tutela de urgência.',
            'Intimação das partes para manifestação sobre laudo pericial.',
            'Recurso de apelação interposto pela parte requerida.',
            'Juntada de documentos complementares ao processo.',
        ];

        Processo::doesntHave('andamentos')->get()->each(function (Processo $processo) use ($faker, $tiposAcao, $usuarios, $descricoes) {
            $quantidade = rand(1, 4);

            for ($i = 0; $i < $quantidade; $i++) {
                Andamento::create([
                    'processo_id' => $processo->id,
                    'tipo' => 'outro',
                    'tipo_acao_id' => $tiposAcao->random()->id,
                    'data_andamento' => $faker->dateTimeBetween($processo->data_abertura, 'now'),
                    'descricao' => $descricoes[array_rand($descricoes)],
                    'usuario_id' => $usuarios->random(),
                    'created_by' => $usuarios->random(),
                ]);
            }
        });
    }
}
