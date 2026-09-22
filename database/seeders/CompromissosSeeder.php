<?php

namespace Database\Seeders;

use App\Models\Compromisso;
use App\Models\Processo;
use App\Models\User;
use Illuminate\Database\Seeder;

class CompromissosSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake('pt_BR');
        $usuarios = User::query()->where('perfil_acesso', 2)->pluck('id');

        if ($usuarios->isEmpty()) {
            return;
        }

        $tipos = array_keys(Compromisso::TIPO_OPTIONS);
        $titulosPorTipo = [
            Compromisso::TIPO_AUDIENCIA => 'Audiência de instrução e julgamento',
            Compromisso::TIPO_PRAZO_FATAL => 'Prazo fatal para contestação',
            Compromisso::TIPO_REUNIAO => 'Reunião com o cliente',
            Compromisso::TIPO_OUTRO => 'Compromisso diverso',
        ];

        // Um compromisso por processo que ainda não tenha nenhum, para manter
        // o seeder idempotente mesmo com registros manuais já existentes na base.
        Processo::doesntHave('compromissos')->get()->each(function (Processo $processo) use ($faker, $usuarios, $tipos, $titulosPorTipo) {
            $tipo = $tipos[array_rand($tipos)];
            $dataHora = $faker->dateTimeBetween('-15 days', '+30 days');
            $responsavel = $usuarios->random();
            $status = $dataHora < now() ? Compromisso::STATUS_CONCLUIDO : Compromisso::STATUS_PENDENTE;

            Compromisso::create([
                'titulo' => $titulosPorTipo[$tipo] . ' - Processo ' . $processo->numero_processo,
                'tipo' => $tipo,
                'data_hora' => $dataHora,
                'processo_id' => $processo->id,
                'responsavel_id' => $responsavel,
                'created_by' => $responsavel,
                'status' => $status,
                'observacoes' => $faker->boolean(30) ? $faker->sentence(8) : null,
            ]);
        });
    }
}
