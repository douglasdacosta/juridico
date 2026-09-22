<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            TestUsersSeeder::class,
            UsuariosSeeder::class,
            FiliaisSeeder::class,
            ModelosDocumentoSeeder::class,
            ClientesSeeder::class,
            ProcessosSeeder::class,
            AndamentosSeeder::class,
            DocumentosSeeder::class,
            FinanceiroSeeder::class,
            DespesasSeeder::class,
            CompromissosSeeder::class,
        ]);
    }
}
