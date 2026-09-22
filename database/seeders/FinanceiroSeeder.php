<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Financeiro;
use App\Models\FinanceiroParcela;
use App\Models\Processo;
use Illuminate\Database\Seeder;

class FinanceiroSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake('pt_BR');
        $clientes = Cliente::doesntHave('financeiros')->get();

        foreach ($clientes as $cliente) {
            $parcelado = (bool) rand(0, 1);
            $valorCausa = $faker->randomFloat(2, 2000, 80000);
            $honorarios = round($valorCausa * 0.2, 2);

            $financeiro = Financeiro::create([
                'cliente_id' => $cliente->id,
                'valor_causa' => $valorCausa,
                'honorarios' => $honorarios,
                'reembolso' => $faker->boolean(30) ? $faker->randomFloat(2, 100, 1500) : null,
                'data_pagamento' => $parcelado ? null : $faker->dateTimeBetween('-3 months', '+2 months'),
                'status_pagamento' => $parcelado ? Financeiro::STATUS_NAO_PAGO : Financeiro::STATUS_EM_DIA,
                'parcelado' => $parcelado,
                'numero_parcelas' => $parcelado ? rand(2, 6) : null,
                'valor_parcela' => null,
                'valor_pago' => ! $parcelado && $faker->boolean(50) ? $honorarios : null,
                'data_primeira_parcela' => $parcelado ? $faker->dateTimeBetween('-2 months', '+1 month') : null,
                'observacoes' => $faker->boolean(40) ? $faker->sentence(8) : null,
            ]);

            $processo = Processo::inRandomOrder()->first();
            if ($processo && ! $financeiro->processos()->exists()) {
                $financeiro->processos()->attach($processo->id);
            }

            if ($parcelado) {
                $numeroParcelas = $financeiro->numero_parcelas;
                $valorParcela = round(($honorarios ?: $valorCausa) / $numeroParcelas, 2);
                $financeiro->update(['valor_parcela' => $valorParcela]);

                $dataBase = $financeiro->data_primeira_parcela;

                for ($numero = 1; $numero <= $numeroParcelas; $numero++) {
                    $vencimento = (clone $dataBase)->modify('+' . ($numero - 1) . ' months');
                    $paga = $faker->boolean(40);

                    FinanceiroParcela::create([
                        'financeiro_id' => $financeiro->id,
                        'numero' => $numero,
                        'valor' => $valorParcela,
                        'valor_pago' => $paga ? $valorParcela : null,
                        'data_vencimento' => $vencimento,
                        'data_pagamento' => $paga ? $vencimento : null,
                        'status' => $paga ? 'pago' : 'pendente',
                    ]);
                }
            }
        }
    }
}
