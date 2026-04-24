<?php

namespace Tests\Feature;

use App\Models\Andamento;
use App\Models\Processo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AndamentosOwnershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_owner_non_admin_cannot_update_andamento(): void
    {
        $owner = User::create([
            'name' => 'Owner',
            'email' => 'owner@example.com',
            'password' => Hash::make('12345678'),
            'perfil_acesso' => 2,
        ]);

        $other = User::create([
            'name' => 'Other',
            'email' => 'other@example.com',
            'password' => Hash::make('12345678'),
            'perfil_acesso' => 2,
        ]);

        $processo = Processo::create([
            'numero_processo' => '000002-11.2026.8.26.0001',
            'vara_tribunal' => '2ª Vara',
            'tipo_acao' => 'Cível',
            'data_abertura' => now()->toDateString(),
            'status' => 'ativo',
            'responsavel_id' => $owner->id,
        ]);

        $andamento = Andamento::create([
            'processo_id' => $processo->id,
            'tipo' => 'outro',
            'data_andamento' => now()->toDateString(),
            'descricao' => 'Original',
            'usuario_id' => $owner->id,
            'created_by' => $owner->id,
        ]);

        $this->actingAs($other)
            ->post('/alterar-andamentos', [
                'id' => $andamento->id,
                'processo_id' => $processo->id,
                'tipo' => 'outro',
                'data_andamento' => now()->toDateString(),
                'descricao' => 'Alterado por terceiro',
                'usuario_id' => $other->id,
            ])
            ->assertStatus(403);
    }

    public function test_admin_can_update_andamento_created_by_other_user(): void
    {
        $owner = User::create([
            'name' => 'Owner',
            'email' => 'owner-admin@example.com',
            'password' => Hash::make('12345678'),
            'perfil_acesso' => 2,
        ]);

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('12345678'),
            'perfil_acesso' => 1,
        ]);

        $processo = Processo::create([
            'numero_processo' => '000003-11.2026.8.26.0001',
            'vara_tribunal' => '3ª Vara',
            'tipo_acao' => 'Cível',
            'data_abertura' => now()->toDateString(),
            'status' => 'ativo',
            'responsavel_id' => $owner->id,
        ]);

        $andamento = Andamento::create([
            'processo_id' => $processo->id,
            'tipo' => 'outro',
            'data_andamento' => now()->toDateString(),
            'descricao' => 'Original',
            'usuario_id' => $owner->id,
            'created_by' => $owner->id,
        ]);

        $this->actingAs($admin)
            ->post('/alterar-andamentos', [
                'id' => $andamento->id,
                'processo_id' => $processo->id,
                'tipo' => 'outro',
                'data_andamento' => now()->toDateString(),
                'descricao' => 'Atualizado por admin',
                'usuario_id' => $admin->id,
            ])
            ->assertRedirect('/andamentos');

        $andamento->refresh();
        $this->assertSame('Atualizado por admin', $andamento->descricao);
    }
}
