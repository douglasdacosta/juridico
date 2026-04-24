<?php

namespace Tests\Feature;

use App\Http\Middleware\AfterAuthMiddleware;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ClientesCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(AfterAuthMiddleware::class);
    }

    public function test_authenticated_user_can_create_cliente(): void
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'clientes-create@example.com',
            'password' => Hash::make('12345678'),
            'perfil_acesso' => 1,
        ]);

        $response = $this->actingAs($user)->post('/incluir-clientes', [
            'nome' => 'Cliente Novo',
            'email' => 'cliente.novo@example.com',
            'status' => 'A',
            'telefone' => '11999990000',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
        ]);

        $response->assertRedirect('/clientes');

        $this->assertDatabaseHas('clientes', [
            'nome' => 'Cliente Novo',
            'email' => 'cliente.novo@example.com',
            'status' => 'A',
        ]);
    }

    public function test_authenticated_user_can_update_cliente(): void
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'clientes-update@example.com',
            'password' => Hash::make('12345678'),
            'perfil_acesso' => 1,
        ]);

        $cliente = Cliente::create([
            'nome' => 'Cliente Antigo',
            'email' => 'cliente.antigo@example.com',
            'status' => 'A',
        ]);

        $response = $this->actingAs($user)->post('/alterar-clientes', [
            'id' => $cliente->id,
            'nome' => 'Cliente Atualizado',
            'email' => 'cliente.atualizado@example.com',
            'status' => 'I',
        ]);

        $response->assertRedirect('/clientes');

        $this->assertDatabaseHas('clientes', [
            'id' => $cliente->id,
            'nome' => 'Cliente Atualizado',
            'email' => 'cliente.atualizado@example.com',
            'status' => 'I',
        ]);
    }

    public function test_authenticated_user_can_export_clientes_csv(): void
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'clientes-export@example.com',
            'password' => Hash::make('12345678'),
            'perfil_acesso' => 1,
        ]);

        Cliente::create([
            'nome' => 'Cliente Export',
            'email' => 'cliente.export@example.com',
            'status' => 'A',
        ]);

        $response = $this->actingAs($user)->get('/exportar-clientes-csv');

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $response->assertSee('Cliente Export');
    }
}
