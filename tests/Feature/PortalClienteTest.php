<?php

namespace Tests\Feature;

use App\Http\Middleware\AfterAuthMiddleware;
use App\Models\Cliente;
use App\Models\Documento;
use App\Models\Processo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PortalClienteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Mesmo padrão de ClientesCrudTest: a permissão fina de afterAuth:* não é
        // seedada de forma confiável no ambiente de testes (SQLite), então os testes
        // desta suíte focam na lógica de autenticação/isolamento do Portal, não nela.
        $this->withoutMiddleware(AfterAuthMiddleware::class);
    }

    private function staffUser(): User
    {
        return User::create([
            'name' => 'Advogado Portal',
            'email' => 'portal.staff@example.com',
            'password' => Hash::make('12345678'),
            'perfil_acesso' => 1,
        ]);
    }

    private function clienteComAcesso(string $cpf, string $senha, array $overrides = []): Cliente
    {
        $cliente = Cliente::create(array_merge([
            'nome' => 'Cliente Portal',
            'cpf' => $cpf,
            'email' => 'cliente.portal@example.com',
            'status' => 'A',
            'ativo' => true,
        ], $overrides));

        // "password" não é mass-assignable (ver Cliente::$fillable), então é
        // atribuído diretamente — mesmo padrão usado em ClientesController::definirAcessoPortal.
        $cliente->password = Hash::make($senha);
        $cliente->save();

        return $cliente;
    }

    public function test_staff_pode_definir_senha_de_acesso_ao_portal(): void
    {
        $staff = $this->staffUser();

        $cliente = Cliente::create([
            'nome' => 'Cliente Sem Acesso',
            'cpf' => '111.222.333-44',
            'email' => 'cliente.sem.acesso@example.com',
            'status' => 'A',
        ]);

        $this->actingAs($staff)
            ->post('/clientes/definir-acesso-portal', [
                'id' => $cliente->id,
                'password' => 'senha123',
                'password_confirmation' => 'senha123',
            ])
            ->assertRedirect();

        $cliente->refresh();
        $this->assertNotNull($cliente->password);
        $this->assertTrue(Hash::check('senha123', $cliente->password));
    }

    public function test_nao_permite_definir_senha_sem_cpf_cadastrado(): void
    {
        $staff = $this->staffUser();

        $cliente = Cliente::create([
            'nome' => 'Cliente Sem CPF',
            'email' => 'cliente.sem.cpf@example.com',
            'status' => 'A',
        ]);

        $this->actingAs($staff)
            ->post('/clientes/definir-acesso-portal', [
                'id' => $cliente->id,
                'password' => 'senha123',
                'password_confirmation' => 'senha123',
            ])
            ->assertRedirect();

        $this->assertNull($cliente->refresh()->password);
    }

    public function test_cliente_pode_logar_no_portal_com_cpf_e_senha(): void
    {
        $this->clienteComAcesso('222.333.444-55', 'minhaSenha1');

        $response = $this->post('/portal/login', [
            'cpf' => '222.333.444-55',
            'password' => 'minhaSenha1',
        ]);

        $response->assertRedirect(route('portal.dashboard'));
        $this->assertAuthenticated('cliente');
    }

    public function test_login_falha_com_senha_invalida(): void
    {
        $this->clienteComAcesso('333.444.555-66', 'senhaCorreta');

        $response = $this->post('/portal/login', [
            'cpf' => '333.444.555-66',
            'password' => 'senhaErrada',
        ]);

        $response->assertSessionHasErrors('cpf');
        $this->assertGuest('cliente');
    }

    public function test_login_falha_com_cpf_inexistente(): void
    {
        $response = $this->post('/portal/login', [
            'cpf' => '000.000.000-00',
            'password' => 'qualquer',
        ]);

        $response->assertSessionHasErrors('cpf');
        $this->assertGuest('cliente');
    }

    public function test_visitante_nao_autenticado_e_redirecionado_ao_login_do_portal(): void
    {
        $this->get('/portal')->assertRedirect(route('portal.login'));
    }

    public function test_cliente_so_ve_seus_proprios_processos(): void
    {
        $clienteA = $this->clienteComAcesso('444.555.666-77', 'senhaA', [
            'nome' => 'Cliente A', 'email' => 'cliente.a.portal@example.com',
        ]);
        $clienteB = Cliente::create([
            'nome' => 'Cliente B',
            'cpf' => '555.666.777-88',
            'email' => 'cliente.b.portal@example.com',
            'status' => 'A',
        ]);

        $processoA = Processo::create([
            'numero_processo' => '000801-11.2026.8.26.0001',
            'vara_tribunal' => '1ª Vara',
            'tipo_acao' => 'Cível',
            'data_abertura' => now()->toDateString(),
            'status' => 'ativo',
        ]);
        $processoB = Processo::create([
            'numero_processo' => '000802-11.2026.8.26.0001',
            'vara_tribunal' => '2ª Vara',
            'tipo_acao' => 'Cível',
            'data_abertura' => now()->toDateString(),
            'status' => 'ativo',
        ]);

        $processoA->clientes()->attach($clienteA->id, ['papel_cliente' => 'principal']);
        $processoB->clientes()->attach($clienteB->id, ['papel_cliente' => 'principal']);

        $this->post('/portal/login', ['cpf' => '444.555.666-77', 'password' => 'senhaA']);

        $this->get('/portal/processos')
            ->assertOk()
            ->assertSee('000801-11.2026.8.26.0001')
            ->assertDontSee('000802-11.2026.8.26.0001');

        // Tentativa de acessar diretamente o processo de outro cliente deve falhar (404)
        $this->get('/portal/processos/' . $processoB->id)->assertNotFound();
    }

    public function test_documento_nao_compartilhado_nao_aparece_para_o_cliente(): void
    {
        $staff = $this->staffUser();
        $cliente = $this->clienteComAcesso('666.777.888-99', 'senhaDoc');

        $processo = Processo::create([
            'numero_processo' => '000803-11.2026.8.26.0001',
            'vara_tribunal' => '3ª Vara',
            'tipo_acao' => 'Cível',
            'data_abertura' => now()->toDateString(),
            'status' => 'ativo',
        ]);
        $processo->clientes()->attach($cliente->id, ['papel_cliente' => 'principal']);

        Documento::create([
            'nome_original' => 'documento-publico.pdf',
            'nome_armazenado' => 'doc1.pdf',
            'tipo_midia' => 'application/pdf',
            'tamanho' => 100,
            'caminho' => 'documentos/doc1.pdf',
            'processo_id' => $processo->id,
            'usuario_id' => $staff->id,
            'versao' => 1,
            'shared_with_client' => true,
            'ativo' => true,
        ]);

        Documento::create([
            'nome_original' => 'documento-interno.pdf',
            'nome_armazenado' => 'doc2.pdf',
            'tipo_midia' => 'application/pdf',
            'tamanho' => 100,
            'caminho' => 'documentos/doc2.pdf',
            'usuario_id' => $staff->id,
            'processo_id' => $processo->id,
            'versao' => 1,
            'shared_with_client' => false,
            'ativo' => true,
        ]);

        $this->post('/portal/login', ['cpf' => '666.777.888-99', 'password' => 'senhaDoc']);

        $this->get('/portal/documentos')
            ->assertOk()
            ->assertSee('documento-publico.pdf')
            ->assertDontSee('documento-interno.pdf');
    }
}
