<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AddTestUsers extends Command
{
    protected $signature = 'users:add-test';
    protected $description = 'Adiciona usuários de teste para desenvolvimento';

    public function handle()
    {
        $users = [
            [
                'name' => 'Douglas costa',
                'email' => 'douglas.costa@example.com',
                'perfil_acesso' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Usuário Teste',
                'email' => 'test@example.com',
                'perfil_acesso' => 2,
                'status' => 1,
            ],
            [
                'name' => 'Usuário Inativo',
                'email' => 'inativo@example.com',
                'perfil_acesso' => 2,
                'status' => 0,
            ],
            [
                'name' => 'Douglas Silva',
                'email' => 'douglas2@example.com',
                'perfil_acesso' => 2,
                'status' => 1,
            ],
            [
                'name' => 'João Costa',
                'email' => 'costa@example.com',
                'perfil_acesso' => 2,
                'status' => 1,
            ],
        ];

        $password = Hash::make('password123');

        foreach ($users as $userData) {
            try {
                $user = User::updateOrCreate(
                    ['email' => $userData['email']],
                    [
                        'name' => $userData['name'],
                        'password' => $password,
                        'perfil_acesso' => $userData['perfil_acesso'],
                        'status' => $userData['status'],
                    ]
                );

                $this->info("✓ Usuário '{$user->name}' ({$user->email}) inserido com sucesso!");
            } catch (\Exception $e) {
                $this->error("✗ Erro ao inserir usuário '{$userData['email']}': " . $e->getMessage());
            }
        }

        $count = User::count();
        $this->info("\n✓ Total de usuários no banco: $count");
        $this->line("\nTodos os usuários usam a senha: password123");
    }
}
