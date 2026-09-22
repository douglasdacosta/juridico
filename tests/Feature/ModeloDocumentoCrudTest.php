<?php

namespace Tests\Feature;

use App\Http\Middleware\AfterAuthMiddleware;
use App\Models\ModeloDocumento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ModeloDocumentoCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Mesmo padrão de ClientesCrudTest/PortalClienteTest: a permissão fina de
        // afterAuth:* não é seedada de forma confiável no ambiente de testes.
        $this->withoutMiddleware(AfterAuthMiddleware::class);
    }

    private function actingUser(): User
    {
        return User::create([
            'name' => 'Advogado Modelos',
            'email' => 'modelos@example.com',
            'password' => Hash::make('12345678'),
            'perfil_acesso' => 1,
        ]);
    }

    public function test_modelo_pode_ser_criado(): void
    {
        $user = $this->actingUser();

        $this->actingAs($user)
            ->post('/incluir-modelos-documento', [
                'nome' => 'Petição Inicial',
                'tipo' => ModeloDocumento::TIPO_PETICAO,
                'corpo' => '<p>Excelentíssimo Juiz, {{cliente.nome}} vem requerer...</p>',
            ])
            ->assertRedirect('/modelos-documento');

        $this->assertDatabaseHas('modelos_documento', [
            'nome' => 'Petição Inicial',
            'tipo' => ModeloDocumento::TIPO_PETICAO,
            'ativo' => true,
        ]);
    }

    public function test_modelo_pode_ser_atualizado(): void
    {
        $user = $this->actingUser();

        $modelo = ModeloDocumento::create([
            'nome' => 'Modelo Antigo',
            'tipo' => ModeloDocumento::TIPO_OUTRO,
            'corpo' => '<p>Conteúdo original</p>',
        ]);

        $this->actingAs($user)
            ->post('/alterar-modelos-documento', [
                'id' => $modelo->id,
                'nome' => 'Modelo Atualizado',
                'tipo' => ModeloDocumento::TIPO_CONTRATO,
                'corpo' => '<p>Conteúdo novo</p>',
            ])
            ->assertRedirect('/modelos-documento');

        $this->assertDatabaseHas('modelos_documento', [
            'id' => $modelo->id,
            'nome' => 'Modelo Atualizado',
            'tipo' => ModeloDocumento::TIPO_CONTRATO,
        ]);
    }

    public function test_modelo_pode_ser_desativado(): void
    {
        $user = $this->actingUser();

        $modelo = ModeloDocumento::create([
            'nome' => 'Modelo a Desativar',
            'tipo' => ModeloDocumento::TIPO_OUTRO,
            'corpo' => '<p>Conteúdo</p>',
        ]);

        $this->actingAs($user)
            ->post('/desativar-modelos-documento', ['id' => $modelo->id])
            ->assertRedirect('/modelos-documento');

        $this->assertDatabaseHas('modelos_documento', ['id' => $modelo->id, 'ativo' => false]);
    }

    public function test_tela_de_modelos_renderiza_sem_erro(): void
    {
        $user = $this->actingUser();

        ModeloDocumento::create([
            'nome' => 'Modelo Listagem',
            'tipo' => ModeloDocumento::TIPO_OUTRO,
            'corpo' => '<p>Conteúdo</p>',
        ]);

        $this->actingAs($user)->get('/modelos-documento')->assertOk()->assertSee('Modelos de Documento');
    }
}
