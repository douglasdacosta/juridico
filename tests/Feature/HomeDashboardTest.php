<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Compromisso;
use App\Models\Despesa;
use App\Models\Financeiro;
use App\Models\Processo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class HomeDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_open_home_dashboard(): void
    {
        $user = User::create([
            'name' => 'Dashboard User',
            'email' => 'dashboard@example.com',
            'password' => Hash::make('12345678'),
            'perfil_acesso' => 1,
        ]);

        $this->actingAs($user)
            ->get('/home')
            ->assertOk()
            ->assertSee('Dashboard Jurídico')
            ->assertSee('Acesso rápido - Consulta de processos');
    }

    public function test_home_dashboard_filters_processes_by_cliente_and_numero(): void
    {
        $user = User::create([
            'name' => 'Dashboard User 2',
            'email' => 'dashboard2@example.com',
            'password' => Hash::make('12345678'),
            'perfil_acesso' => 1,
        ]);

        $clienteA = Cliente::create([
            'nome' => 'Cliente A',
            'email' => 'cliente.a@example.com',
            'status' => 'A',
        ]);

        $clienteB = Cliente::create([
            'nome' => 'Cliente B',
            'email' => 'cliente.b@example.com',
            'status' => 'A',
        ]);

        $processoA = Processo::create([
            'numero_processo' => '000100-11.2026.8.26.0001',
            'vara_tribunal' => '1ª Vara',
            'tipo_acao' => 'Cível',
            'data_abertura' => now()->toDateString(),
            'status' => 'ativo',
            'responsavel_id' => $user->id,
        ]);

        $processoB = Processo::create([
            'numero_processo' => '000200-11.2026.8.26.0001',
            'vara_tribunal' => '2ª Vara',
            'tipo_acao' => 'Cível',
            'data_abertura' => now()->toDateString(),
            'status' => 'ativo',
            'responsavel_id' => $user->id,
        ]);

        $processoA->clientes()->attach($clienteA->id, ['papel_cliente' => 'principal']);
        $processoB->clientes()->attach($clienteB->id, ['papel_cliente' => 'principal']);

        $this->actingAs($user)
            ->get('/home?cliente_id=' . $clienteA->id . '&numero_processo=000100')
            ->assertOk()
            ->assertSee('000100-11.2026.8.26.0001')
            ->assertDontSee('000200-11.2026.8.26.0001');
    }

    public function test_dashboard_renders_financial_kpis_on_fresh_install_without_data(): void
    {
        $user = User::create([
            'name' => 'Dashboard User Vazio',
            'email' => 'dashboard.vazio@example.com',
            'password' => Hash::make('12345678'),
            'perfil_acesso' => 1,
        ]);

        $this->actingAs($user)
            ->get('/home')
            ->assertOk()
            ->assertSee('A receber (pendente)')
            ->assertSee('Recebido este mês')
            ->assertSee('A receber em atraso')
            ->assertSee('A pagar em atraso')
            ->assertSee('Nenhum compromisso agendado.');
    }

    public function test_dashboard_shows_financial_kpis_and_proximos_compromissos_com_dados(): void
    {
        $user = User::create([
            'name' => 'Dashboard User Financeiro',
            'email' => 'dashboard.financeiro@example.com',
            'password' => Hash::make('12345678'),
            'perfil_acesso' => 1,
        ]);

        $cliente = Cliente::create([
            'nome' => 'Cliente Dashboard',
            'email' => 'cliente.dashboard@example.com',
            'status' => 'A',
        ]);

        Financeiro::create([
            'cliente_id' => $cliente->id,
            'valor_causa' => 1000,
            'parcelado' => false,
            'data_pagamento' => now(),
            'valor_pago' => 1000,
        ]);

        Despesa::create([
            'descricao' => 'Aluguel',
            'valor' => 400,
            'data_vencimento' => now()->subDays(3),
        ]);

        Compromisso::create([
            'titulo' => 'Audiência importante',
            'tipo' => Compromisso::TIPO_AUDIENCIA,
            'data_hora' => now()->addDays(2),
            'responsavel_id' => $user->id,
            'created_by' => $user->id,
        ]);

        $this->actingAs($user)
            ->get('/home')
            ->assertOk()
            ->assertSee('1.000,00')
            ->assertSee('400,00')
            ->assertSee('Audiência importante');
    }
}
