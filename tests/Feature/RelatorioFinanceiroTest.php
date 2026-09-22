<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Despesa;
use App\Models\Financeiro;
use App\Models\FinanceiroParcela;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RelatorioFinanceiroTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): User
    {
        return User::create([
            'name' => 'Advogado Financeiro',
            'email' => 'relatorio@example.com',
            'password' => Hash::make('12345678'),
            'perfil_acesso' => 1,
        ]);
    }

    public function test_fluxo_de_caixa_soma_entradas_e_saidas_do_mes_corrente(): void
    {
        $user = $this->actingUser();

        $cliente = Cliente::create([
            'nome' => 'Cliente Fluxo',
            'email' => 'cliente.fluxo@example.com',
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
            'valor_pago' => 400,
            'data_vencimento' => now(),
            'data_pagamento' => now(),
        ]);

        $response = $this->actingAs($user)->get('/relatorios/fluxo-caixa');

        $response->assertOk();
        $response->assertSee('1.000,00');
        $response->assertSee('400,00');
    }

    public function test_inadimplencia_lista_lancamentos_parcelas_e_despesas_vencidos_ordenados(): void
    {
        $user = $this->actingUser();

        $cliente = Cliente::create([
            'nome' => 'Cliente Inadimplente',
            'email' => 'cliente.inadimplente@example.com',
            'status' => 'A',
        ]);

        $financeiroVencido = Financeiro::create([
            'cliente_id' => $cliente->id,
            'valor_causa' => 500,
            'parcelado' => false,
            'data_pagamento' => now()->subDays(10),
        ]);

        $financeiroParcelado = Financeiro::create([
            'cliente_id' => $cliente->id,
            'valor_causa' => 900,
            'parcelado' => true,
            'numero_parcelas' => 1,
        ]);

        FinanceiroParcela::create([
            'financeiro_id' => $financeiroParcelado->id,
            'numero' => 1,
            'valor' => 300,
            'data_vencimento' => now()->subDays(20),
        ]);

        Despesa::create([
            'descricao' => 'Conta atrasada',
            'valor' => 150,
            'data_vencimento' => now()->subDays(5),
        ]);

        $response = $this->actingAs($user)->get('/relatorios/inadimplencia');

        $response->assertOk();
        $response->assertSeeInOrder(['Parcela nº 1', 'Honorário (à vista)', 'Conta atrasada']);
    }

    public function test_fluxo_de_caixa_pode_ser_exportado_em_pdf(): void
    {
        $user = $this->actingUser();

        $response = $this->actingAs($user)->get('/relatorios/fluxo-caixa/pdf');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_total_em_atraso_do_financeiro_soma_avista_e_parcelas_vencidas(): void
    {
        $cliente = Cliente::create([
            'nome' => 'Cliente Total Atraso',
            'email' => 'cliente.totalatraso@example.com',
            'status' => 'A',
        ]);

        Financeiro::create([
            'cliente_id' => $cliente->id,
            'valor_causa' => 200,
            'parcelado' => false,
            'data_pagamento' => now()->subDays(3),
        ]);

        $financeiroParcelado = Financeiro::create([
            'cliente_id' => $cliente->id,
            'valor_causa' => 900,
            'parcelado' => true,
            'numero_parcelas' => 1,
        ]);

        FinanceiroParcela::create([
            'financeiro_id' => $financeiroParcelado->id,
            'numero' => 1,
            'valor' => 300,
            'data_vencimento' => now()->subDays(1),
        ]);

        $this->assertEquals(500.0, Financeiro::totalEmAtraso());
    }
}
