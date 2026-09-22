<?php

namespace Database\Seeders;

use App\Models\Despesa;
use App\Models\Filial;
use Illuminate\Database\Seeder;

class DespesasSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake('pt_BR');
        $filiais = Filial::all();

        $categorias = ['Aluguel', 'Água/Luz/Internet', 'Material de Escritório', 'Software/Assinaturas', 'Custas Judiciais', 'Salários'];
        $descricoesPorCategoria = [
            'Aluguel' => 'Aluguel do escritório',
            'Água/Luz/Internet' => 'Conta de água, luz e internet',
            'Material de Escritório' => 'Compra de material de escritório',
            'Software/Assinaturas' => 'Assinatura de software jurídico',
            'Custas Judiciais' => 'Pagamento de custas processuais',
            'Salários' => 'Folha de pagamento da equipe',
        ];

        for ($i = 0; $i < 12; $i++) {
            $categoria = $categorias[$i % count($categorias)];
            $vencimento = now()->copy()->subMonths(6)->addMonths($i);
            $paga = $vencimento->lt(now());
            $valor = $faker->randomFloat(2, 150, 8000);
            $descricao = $descricoesPorCategoria[$categoria] . ' - ' . $vencimento->format('m/Y');

            Despesa::firstOrCreate(
                ['descricao' => $descricao],
                [
                    'categoria' => $categoria,
                    'valor' => $valor,
                    'valor_pago' => $paga ? $valor : null,
                    'data_vencimento' => $vencimento,
                    'data_pagamento' => $paga ? $vencimento : null,
                    'status' => $paga ? Despesa::STATUS_PAGO : Despesa::STATUS_PENDENTE,
                    'filial_id' => $filiais->isNotEmpty() ? $filiais->random()->id : null,
                    'observacoes' => $faker->boolean(30) ? $faker->sentence(6) : null,
                ]
            );
        }
    }
}
