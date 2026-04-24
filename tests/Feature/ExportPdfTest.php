<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Filial;
use App\Models\Processo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ExportPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_export_clientes_pdf(): void
    {
        $user = User::create([
            'name' => 'Admin PDF',
            'email' => 'pdf-clientes@example.com',
            'password' => Hash::make('12345678'),
            'perfil_acesso' => 1,
        ]);

        Cliente::create([
            'nome' => 'Cliente PDF',
            'email' => 'cliente.pdf@example.com',
            'status' => 'A',
        ]);

        $response = $this->actingAs($user)->get('/exportar-clientes-pdf');

        $response->assertOk();
        $response->assertSee('Relatório de Clientes');
        $response->assertSee('Cliente PDF');
    }

    public function test_authenticated_user_can_export_processos_pdf(): void
    {
        $user = User::create([
            'name' => 'Admin PDF Proc',
            'email' => 'pdf-processos@example.com',
            'password' => Hash::make('12345678'),
            'perfil_acesso' => 1,
        ]);

        $cliente = Cliente::create([
            'nome' => 'Cliente Processo PDF',
            'email' => 'cliente.proc.pdf@example.com',
            'status' => 'A',
        ]);

        $filial = Filial::create([
            'nome' => 'Filial PDF',
            'ativo' => true,
        ]);

        $processo = Processo::create([
            'numero_processo' => '000777-11.2026.8.26.0001',
            'vara_tribunal' => '7ª Vara',
            'tipo_acao' => 'Trabalhista',
            'data_abertura' => now()->toDateString(),
            'status' => 'ativo',
            'responsavel_id' => $user->id,
        ]);

        $processo->clientes()->attach($cliente->id, ['papel_cliente' => 'principal']);
        $processo->filiais()->attach($filial->id);

        $response = $this->actingAs($user)->get('/exportar-processos-pdf');

        $response->assertOk();
        $response->assertSee('Relatório de Processos');
        $response->assertSee('000777-11.2026.8.26.0001');
    }
}
