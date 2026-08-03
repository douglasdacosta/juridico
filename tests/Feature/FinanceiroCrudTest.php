<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Financeiro;
use App\Models\Processo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FinanceiroCrudTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): User
    {
        return User::create([
            'name' => 'Advogado Financeiro',
            'email' => 'financeiro@example.com',
            'password' => Hash::make('12345678'),
            'perfil_acesso' => 1,
        ]);
    }

    public function test_lancamento_pode_ser_criado_vinculado_a_multiplos_processos_do_mesmo_cliente(): void
    {
        $user = $this->actingUser();

        $cliente = Cliente::create([
            'nome' => 'Cliente Financeiro',
            'email' => 'cliente.financeiro@example.com',
            'status' => 'A',
        ]);

        $processoA = Processo::create([
            'numero_processo' => '000010-11.2026.8.26.0001',
            'vara_tribunal' => '1ª Vara Cível',
            'tipo_acao' => 'Cível',
            'data_abertura' => now()->toDateString(),
            'status' => 'ativo',
        ]);
        $processoB = Processo::create([
            'numero_processo' => '000011-11.2026.8.26.0001',
            'vara_tribunal' => '2ª Vara Cível',
            'tipo_acao' => 'Cível',
            'data_abertura' => now()->toDateString(),
            'status' => 'ativo',
        ]);

        $processoA->clientes()->attach($cliente->id, ['papel_cliente' => 'principal']);
        $processoB->clientes()->attach($cliente->id, ['papel_cliente' => 'principal']);

        $this->actingAs($user)
            ->post('/incluir-financeiro', [
                'cliente_id' => $cliente->id,
                'valor_causa' => '1000.00',
                'honorarios' => '200.00',
                'reembolso' => '50.00',
                'processos' => [$processoA->id, $processoB->id],
            ])
            ->assertRedirect('/financeiro');

        $financeiro = Financeiro::query()->where('cliente_id', $cliente->id)->firstOrFail();

        $this->assertCount(2, $financeiro->processos);
        $this->assertSame(Financeiro::STATUS_NAO_PAGO, $financeiro->status_pagamento);
    }

    public function test_nao_permite_vincular_processo_de_outro_cliente(): void
    {
        $user = $this->actingUser();

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

        $processoDeB = Processo::create([
            'numero_processo' => '000020-11.2026.8.26.0001',
            'vara_tribunal' => '3ª Vara Cível',
            'tipo_acao' => 'Cível',
            'data_abertura' => now()->toDateString(),
            'status' => 'ativo',
        ]);
        $processoDeB->clientes()->attach($clienteB->id, ['papel_cliente' => 'principal']);

        $this->actingAs($user)
            ->post('/incluir-financeiro', [
                'cliente_id' => $clienteA->id,
                'valor_causa' => '500.00',
                'processos' => [$processoDeB->id],
            ])
            ->assertSessionHasErrors('processos');

        $this->assertDatabaseCount('financeiros', 0);
    }

    public function test_status_quitado_exige_data_de_pagamento(): void
    {
        $user = $this->actingUser();

        $cliente = Cliente::create([
            'nome' => 'Cliente Quitado',
            'email' => 'cliente.quitado@example.com',
            'status' => 'A',
        ]);

        $processo = Processo::create([
            'numero_processo' => '000030-11.2026.8.26.0001',
            'vara_tribunal' => '4ª Vara Cível',
            'tipo_acao' => 'Cível',
            'data_abertura' => now()->toDateString(),
            'status' => 'ativo',
        ]);
        $processo->clientes()->attach($cliente->id, ['papel_cliente' => 'principal']);

        $this->actingAs($user)
            ->post('/incluir-financeiro', [
                'cliente_id' => $cliente->id,
                'valor_causa' => '800.00',
                'processos' => [$processo->id],
            ])
            ->assertRedirect('/financeiro');

        $financeiro = Financeiro::query()->where('cliente_id', $cliente->id)->firstOrFail();
        $this->assertSame(Financeiro::STATUS_NAO_PAGO, $financeiro->status_pagamento);
    }

    public function test_lancamento_pode_ser_editado_e_excluido(): void
    {
        $user = $this->actingUser();

        $cliente = Cliente::create([
            'nome' => 'Cliente Edicao',
            'email' => 'cliente.edicao@example.com',
            'status' => 'A',
        ]);

        $processo = Processo::create([
            'numero_processo' => '000040-11.2026.8.26.0001',
            'vara_tribunal' => '5ª Vara Cível',
            'tipo_acao' => 'Cível',
            'data_abertura' => now()->toDateString(),
            'status' => 'ativo',
        ]);
        $processo->clientes()->attach($cliente->id, ['papel_cliente' => 'principal']);

        $financeiro = Financeiro::create([
            'cliente_id' => $cliente->id,
            'valor_causa' => '1200.00',
        ]);
        $financeiro->processos()->attach($processo->id);
        // marcar como pago (à vista) via endpoint específico
        $this->actingAs($user)
            ->post('/financeiro/pagar', [
                'financeiro_id' => $financeiro->id,
                'data_pagamento' => now()->toDateString(),
                'valor_pago' => '1200.00',
            ])->assertJson(['success' => true]);

        $financeiro->refresh();
        $this->assertSame(Financeiro::STATUS_PAGO, $financeiro->status_pagamento);

        $this->actingAs($user)
            ->post('/excluir-financeiro', ['id' => $financeiro->id])
            ->assertRedirect('/financeiro');

        $this->assertDatabaseMissing('financeiros', ['id' => $financeiro->id]);
    }
}
