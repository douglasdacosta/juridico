<?php

namespace Database\Seeders;

use App\Models\Filial;
use Illuminate\Database\Seeder;

class FiliaisSeeder extends Seeder
{
    public function run(): void
    {
        $filiais = [
            ['nome' => 'Matriz - São Paulo', 'cnpj' => '12.345.678/0001-90', 'endereco' => 'Av. Paulista, 1000 - Bela Vista, São Paulo - SP', 'ativo' => true],
            ['nome' => 'Filial - Rio de Janeiro', 'cnpj' => '12.345.678/0002-71', 'endereco' => 'Av. Rio Branco, 156 - Centro, Rio de Janeiro - RJ', 'ativo' => true],
            ['nome' => 'Filial - Belo Horizonte', 'cnpj' => '12.345.678/0003-52', 'endereco' => 'Av. Afonso Pena, 2000 - Centro, Belo Horizonte - MG', 'ativo' => true],
        ];

        foreach ($filiais as $filial) {
            Filial::firstOrCreate(['nome' => $filial['nome']], $filial);
        }
    }
}
