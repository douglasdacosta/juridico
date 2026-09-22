<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuariosSeeder extends Seeder
{
    /**
     * Usuários internos do escritório (advogados, secretária, estagiário),
     * usados como responsáveis em processos, andamentos e compromissos.
     */
    public function run(): void
    {
        $usuarios = [
            ['name' => 'Ana Paula Ribeiro', 'email' => 'ana.ribeiro@escritorio.com', 'perfil_acesso' => 2, 'status' => 1],
            ['name' => 'Carlos Eduardo Mendes', 'email' => 'carlos.mendes@escritorio.com', 'perfil_acesso' => 2, 'status' => 1],
            ['name' => 'Fernanda Souza Lima', 'email' => 'fernanda.lima@escritorio.com', 'perfil_acesso' => 2, 'status' => 1],
            ['name' => 'Juliana Alves Pereira', 'email' => 'juliana.pereira@escritorio.com', 'perfil_acesso' => 3, 'status' => 1],
            ['name' => 'Rafael Torres Almeida', 'email' => 'rafael.almeida@escritorio.com', 'perfil_acesso' => 4, 'status' => 1],
        ];

        foreach ($usuarios as $usuario) {
            User::firstOrCreate(
                ['email' => $usuario['email']],
                [
                    'name' => $usuario['name'],
                    'password' => Hash::make('password123'),
                    'perfil_acesso' => $usuario['perfil_acesso'],
                    'status' => $usuario['status'],
                ]
            );
        }
    }
}
