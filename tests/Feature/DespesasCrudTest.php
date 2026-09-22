<?php

namespace Tests\Feature;

use App\Models\Despesa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DespesasCrudTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): User
    {
        return User::create([
            'name' => 'Advogado Financeiro',
            'email' => 'despesas@example.com',
            'password' => Hash::make('12345678'),
            'perfil_acesso' => 1,
        ]);
    }

    public function test_despesa_pode_ser_criada(): void
    {
        $user = $this->actingUser();

        $this->actingAs($user)
            ->post('/incluir-despesas', [
                'descricao' => 'Aluguel do escritório',
                'categoria' => 'Aluguel',
                'valor' => '2500.00',
                'data_vencimento' => now()->addDays(10)->format('Y-m-d'),
            ])
            ->assertRedirect('/despesas');

        $despesa = Despesa::query()->where('descricao', 'Aluguel do escritório')->firstOrFail();

        $this->assertSame(Despesa::STATUS_PENDENTE, $despesa->status);
    }

    public function test_despesa_vencida_calcula_status_atrasado(): void
    {
        $despesa = Despesa::create([
            'descricao' => 'Conta de luz',
            'valor' => 300,
            'data_vencimento' => now()->subDays(5),
        ]);

        $this->assertSame(Despesa::STATUS_ATRASADO, $despesa->computeStatus());
    }

    public function test_despesa_pode_ser_marcada_como_paga(): void
    {
        $user = $this->actingUser();

        $despesa = Despesa::create([
            'descricao' => 'Internet',
            'valor' => 150,
            'data_vencimento' => now()->subDays(1),
            'status' => Despesa::STATUS_ATRASADO,
        ]);

        $this->actingAs($user)
            ->post('/despesas/pagar', ['id' => $despesa->id])
            ->assertRedirect('/despesas');

        $despesa->refresh();

        $this->assertSame(Despesa::STATUS_PAGO, $despesa->status);
        $this->assertNotNull($despesa->data_pagamento);
    }

    public function test_total_em_atraso_soma_apenas_despesas_nao_pagas_vencidas(): void
    {
        Despesa::create([
            'descricao' => 'Vencida 1',
            'valor' => 100,
            'data_vencimento' => now()->subDays(2),
        ]);
        Despesa::create([
            'descricao' => 'Vencida 2',
            'valor' => 200,
            'data_vencimento' => now()->subDays(3),
        ]);
        Despesa::create([
            'descricao' => 'Futura',
            'valor' => 500,
            'data_vencimento' => now()->addDays(10),
        ]);
        Despesa::create([
            'descricao' => 'Vencida mas paga',
            'valor' => 999,
            'data_vencimento' => now()->subDays(1),
            'data_pagamento' => now(),
            'valor_pago' => 999,
        ]);

        $this->assertEquals(300.0, Despesa::totalEmAtraso());
    }

    public function test_tela_de_despesas_renderiza_sem_erro(): void
    {
        $user = $this->actingUser();

        Despesa::create([
            'descricao' => 'Material de escritório',
            'valor' => 80,
            'data_vencimento' => now()->addDays(15),
        ]);

        $this->actingAs($user)->get('/despesas')->assertOk()->assertSee('Contas a Pagar');
    }
}
