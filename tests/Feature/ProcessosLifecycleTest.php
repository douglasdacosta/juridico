<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Filial;
use App\Models\Processo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProcessosLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_process_can_be_closed_and_reopened_with_multiple_filiais(): void
    {
        $user = User::create([
            'name' => 'Advogado',
            'email' => 'processo.lifecycle@example.com',
            'password' => Hash::make('12345678'),
            'perfil_acesso' => 1,
        ]);

        $cliente = Cliente::create([
            'nome' => 'Cliente Vinculado',
            'email' => 'cliente.lifecycle@example.com',
            'status' => 'A',
        ]);

        $filialA = Filial::create([
            'nome' => 'Filial A',
            'ativo' => true,
        ]);

        $filialB = Filial::create([
            'nome' => 'Filial B',
            'ativo' => true,
        ]);

        $this->actingAs($user)
            ->post('/incluir-processos', [
                'numero_processo' => '000099-11.2026.8.26.0001',
                'vara_tribunal' => '9ª Vara Cível',
                'tipo_acao' => 'Cível',
                'data_abertura' => now()->toDateString(),
                'status' => 'encerrado',
                'responsavel_id' => $user->id,
                'clientes' => [$cliente->id],
                'filiais' => [$filialA->id, $filialB->id],
            ])
            ->assertRedirect('/processos');

        $processo = Processo::query()->where('numero_processo', '000099-11.2026.8.26.0001')->firstOrFail();

        $this->assertSame('encerrado', $processo->status);
        $this->assertNotNull($processo->data_encerramento);
        $this->assertCount(2, $processo->filiais);

        $this->actingAs($user)
            ->post('/alterar-processos', [
                'id' => $processo->id,
                'numero_processo' => $processo->numero_processo,
                'vara_tribunal' => $processo->vara_tribunal,
                'tipo_acao' => $processo->tipo_acao,
                'data_abertura' => $processo->data_abertura->format('Y-m-d'),
                'status' => 'ativo',
                'responsavel_id' => $user->id,
                'clientes' => [$cliente->id],
                'filiais' => [$filialA->id, $filialB->id],
            ])
            ->assertRedirect('/processos');

        $processo->refresh();
        $this->assertSame('ativo', $processo->status);
        $this->assertNull($processo->data_encerramento);
        $this->assertCount(2, $processo->filiais);
    }
}
