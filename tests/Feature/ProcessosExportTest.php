<?php

namespace Tests\Feature;

use App\Models\Filial;
use App\Models\Cliente;
use App\Models\Processo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProcessosExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_export_processos_csv(): void
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin-export@example.com',
            'password' => Hash::make('12345678'),
            'perfil_acesso' => 1,
        ]);

        $cliente = Cliente::create([
            'nome' => 'Cliente Teste',
            'email' => 'cliente@example.com',
            'status' => 'A',
        ]);

        $filial = Filial::create([
            'nome' => 'Matriz',
            'ativo' => true,
        ]);

        $processo = Processo::create([
            'numero_processo' => '000001-11.2026.8.26.0001',
            'vara_tribunal' => '1ª Vara',
            'tipo_acao' => 'Cível',
            'data_abertura' => now()->toDateString(),
            'status' => 'ativo',
            'responsavel_id' => $user->id,
        ]);

        $processo->clientes()->attach($cliente->id, ['papel_cliente' => 'principal']);
        $processo->filiais()->attach($filial->id);

        $response = $this->actingAs($user)->get('/exportar-processos-csv');

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $response->assertSee('000001-11.2026.8.26.0001');
    }
}
