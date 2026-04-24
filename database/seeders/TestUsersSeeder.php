<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar usuário com o nome da busca
        User::firstOrCreate(
            ['email' => 'douglas.costa@example.com'],
            [
                'name' => 'Douglas costa',
                'password' => Hash::make('password123'),
                'perfil_acesso' => 1,
                'status' => 1,
            ]
        );

        // Criar mais alguns usuários de teste
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Usuário Teste',
                'password' => Hash::make('password123'),
                'perfil_acesso' => 2,
                'status' => 1,
            ]
        );

        User::firstOrCreate(
            ['email' => 'inativo@example.com'],
            [
                'name' => 'Usuário Inativo',
                'password' => Hash::make('password123'),
                'perfil_acesso' => 2,
                'status' => 0,
            ]
        );

        User::firstOrCreate(
            ['email' => 'douglas2@example.com'],
            [
                'name' => 'Douglas Silva',
                'password' => Hash::make('password123'),
                'perfil_acesso' => 2,
                'status' => 1,
            ]
        );

        User::firstOrCreate(
            ['email' => 'costa@example.com'],
            [
                'name' => 'João Costa',
                'password' => Hash::make('password123'),
                'perfil_acesso' => 2,
                'status' => 1,
            ]
        );
    }
}
