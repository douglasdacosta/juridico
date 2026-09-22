<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientesSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake('pt_BR');
        $responsaveis = User::query()->where('perfil_acesso', 2)->pluck('id');

        for ($i = 1; $i <= 15; $i++) {
            $tipoPessoa = $i % 3 === 0 ? 'J' : 'F';
            $nome = $tipoPessoa === 'J' ? $faker->company() : $faker->name();

            $cliente = Cliente::firstOrCreate(
                ['email' => 'cliente' . $i . '@exemplo.com'],
                [
                    'nome' => $nome,
                    'tipo_pessoa' => $tipoPessoa,
                    'cpf' => $tipoPessoa === 'F' ? $faker->cpf() : null,
                    'cnpj' => $tipoPessoa === 'J' ? $faker->cnpj() : null,
                    'endereco' => $faker->streetName(),
                    'numero' => (string) $faker->buildingNumber(),
                    'bairro' => $faker->citySuffix(),
                    'cidade' => $faker->city(),
                    'estado' => $faker->stateAbbr(),
                    'cep' => $faker->postcode(),
                    'telefone' => $faker->cellphoneNumber(),
                    'status' => 1,
                    'ativo' => true,
                ]
            );

            if ($responsaveis->isNotEmpty() && ! $cliente->responsaveis()->exists()) {
                $cliente->responsaveis()->attach(
                    $responsaveis->random(),
                    ['papel' => 'Advogado Responsável']
                );
            }
        }
    }
}
