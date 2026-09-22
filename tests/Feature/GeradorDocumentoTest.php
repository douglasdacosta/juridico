<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Documento;
use App\Models\ModeloDocumento;
use App\Models\Processo;
use App\Models\User;
use App\Services\GeradorDocumentoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GeradorDocumentoTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): User
    {
        return User::create([
            'name' => 'Advogado Documentos',
            'email' => 'documentos@example.com',
            'password' => Hash::make('12345678'),
            'perfil_acesso' => 1,
        ]);
    }

    public function test_servico_substitui_todos_os_placeholders_conhecidos(): void
    {
        $cliente = Cliente::create([
            'nome' => 'João da Silva',
            'cpf' => '123.456.789-00',
            'email' => 'joao@example.com',
            'status' => 'A',
            'endereco' => 'Rua das Flores',
            'numero' => '100',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
        ]);

        $processo = Processo::create([
            'numero_processo' => '000900-11.2026.8.26.0001',
            'vara_tribunal' => '1ª Vara Cível',
            'tipo_acao' => 'Cível',
            'data_abertura' => now()->toDateString(),
            'status' => 'ativo',
        ]);
        $processo->clientes()->attach($cliente->id, ['papel_cliente' => 'principal']);

        $modelo = ModeloDocumento::create([
            'nome' => 'Procuração',
            'tipo' => ModeloDocumento::TIPO_CONTRATO,
            'corpo' => '<p>Eu, {{cliente.nome}}, CPF {{cliente.cpf}}, residente em {{cliente.endereco}}, '
                . 'outorgo poderes referentes ao processo {{processo.numero}} da {{processo.vara}}.</p>',
        ]);

        $resultado = app(GeradorDocumentoService::class)->gerar($modelo, $processo);

        $this->assertStringContainsString('João da Silva', $resultado);
        $this->assertStringContainsString('123.456.789-00', $resultado);
        $this->assertStringContainsString('Rua das Flores, 100', $resultado);
        $this->assertStringContainsString('000900-11.2026.8.26.0001', $resultado);
        $this->assertStringContainsString('1ª Vara Cível', $resultado);
        $this->assertStringNotContainsString('{{', $resultado);
    }

    public function test_gerar_documento_salva_pdf_vinculado_ao_processo_correto(): void
    {
        Storage::fake('local');

        $user = $this->actingUser();

        $cliente = Cliente::create([
            'nome' => 'Maria Souza',
            'email' => 'maria@example.com',
            'status' => 'A',
        ]);

        $processo = Processo::create([
            'numero_processo' => '000901-11.2026.8.26.0001',
            'vara_tribunal' => '2ª Vara Cível',
            'tipo_acao' => 'Cível',
            'data_abertura' => now()->toDateString(),
            'status' => 'ativo',
        ]);
        $processo->clientes()->attach($cliente->id, ['papel_cliente' => 'principal']);

        $outroProcesso = Processo::create([
            'numero_processo' => '000902-11.2026.8.26.0001',
            'vara_tribunal' => '3ª Vara Cível',
            'tipo_acao' => 'Cível',
            'data_abertura' => now()->toDateString(),
            'status' => 'ativo',
        ]);

        $modelo = ModeloDocumento::create([
            'nome' => 'Contrato de Honorários',
            'tipo' => ModeloDocumento::TIPO_CONTRATO,
            'corpo' => '<p>Contrato entre o escritório e {{cliente.nome}}.</p>',
        ]);

        $response = $this->actingAs($user)->post('/gerar-documento', [
            'processo_id' => $processo->id,
            'modelo_documento_id' => $modelo->id,
            'cliente_id' => $cliente->id,
        ]);

        $response->assertRedirect(route('alterar-processos', ['id' => $processo->id]));

        $documento = Documento::query()->where('modelo_documento_id', $modelo->id)->firstOrFail();

        $this->assertSame($processo->id, $documento->processo_id);
        $this->assertNotSame($outroProcesso->id, $documento->processo_id);
        $this->assertSame('modelo', $documento->origem);
        $this->assertSame('application/pdf', $documento->tipo_midia);
        $this->assertTrue(Storage::disk('local')->exists($documento->caminho));
        $this->assertStringStartsWith('%PDF-', Storage::disk('local')->get($documento->caminho));
    }
}
