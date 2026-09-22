<?php

namespace Tests\Feature;

use App\Models\Compromisso;
use App\Models\Processo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AgendaCrudTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): User
    {
        return User::create([
            'name' => 'Advogado Agenda',
            'email' => 'agenda@example.com',
            'password' => Hash::make('12345678'),
            'perfil_acesso' => 1,
        ]);
    }

    public function test_compromisso_pode_ser_criado_vinculado_a_processo(): void
    {
        $user = $this->actingUser();

        $processo = Processo::create([
            'numero_processo' => '000040-11.2026.8.26.0001',
            'vara_tribunal' => '1ª Vara Cível',
            'tipo_acao' => 'Cível',
            'data_abertura' => now()->toDateString(),
            'status' => 'ativo',
        ]);

        $this->actingAs($user)
            ->post('/incluir-agenda', [
                'titulo' => 'Audiência de conciliação',
                'tipo' => Compromisso::TIPO_AUDIENCIA,
                'data_hora' => now()->addDays(5)->format('Y-m-d H:i:s'),
                'processo_id' => $processo->id,
                'responsavel_id' => $user->id,
            ])
            ->assertRedirect('/agenda');

        $compromisso = Compromisso::query()->where('processo_id', $processo->id)->firstOrFail();

        $this->assertSame('Audiência de conciliação', $compromisso->titulo);
        $this->assertSame(Compromisso::STATUS_PENDENTE, $compromisso->status);
    }

    public function test_titulo_e_data_hora_sao_obrigatorios(): void
    {
        $user = $this->actingUser();

        $this->actingAs($user)
            ->post('/incluir-agenda', [
                'tipo' => Compromisso::TIPO_OUTRO,
            ])
            ->assertSessionHasErrors(['titulo', 'data_hora']);

        $this->assertDatabaseCount('compromissos', 0);
    }

    public function test_compromisso_pode_ser_concluido(): void
    {
        $user = $this->actingUser();

        $compromisso = Compromisso::create([
            'titulo' => 'Reunião com cliente',
            'tipo' => Compromisso::TIPO_REUNIAO,
            'data_hora' => now()->addDay(),
            'responsavel_id' => $user->id,
            'created_by' => $user->id,
        ]);

        $this->actingAs($user)
            ->post('/concluir-agenda', ['id' => $compromisso->id])
            ->assertRedirect();

        $this->assertSame(Compromisso::STATUS_CONCLUIDO, $compromisso->refresh()->status);
    }

    public function test_scope_vencidos_retorna_apenas_pendentes_no_passado(): void
    {
        $user = $this->actingUser();

        $vencido = Compromisso::create([
            'titulo' => 'Prazo vencido',
            'tipo' => Compromisso::TIPO_PRAZO_FATAL,
            'data_hora' => now()->subDays(2),
            'responsavel_id' => $user->id,
            'created_by' => $user->id,
        ]);

        Compromisso::create([
            'titulo' => 'Prazo futuro',
            'tipo' => Compromisso::TIPO_PRAZO_FATAL,
            'data_hora' => now()->addDays(2),
            'responsavel_id' => $user->id,
            'created_by' => $user->id,
        ]);

        Compromisso::create([
            'titulo' => 'Prazo passado já concluído',
            'tipo' => Compromisso::TIPO_PRAZO_FATAL,
            'data_hora' => now()->subDays(3),
            'status' => Compromisso::STATUS_CONCLUIDO,
            'responsavel_id' => $user->id,
            'created_by' => $user->id,
        ]);

        $vencidos = Compromisso::vencidos()->get();

        $this->assertCount(1, $vencidos);
        $this->assertSame($vencido->id, $vencidos->first()->id);
    }

    public function test_tela_de_agenda_pesquisa_renderiza_sem_erro(): void
    {
        $user = $this->actingUser();

        Compromisso::create([
            'titulo' => 'Audiência de instrução',
            'tipo' => Compromisso::TIPO_AUDIENCIA,
            'data_hora' => now()->addDays(3),
            'responsavel_id' => $user->id,
            'created_by' => $user->id,
        ]);

        $this->actingAs($user)->get('/agenda')->assertOk()->assertSee('Agenda');
    }

    public function test_tela_de_processo_com_compromisso_renderiza_sem_erro(): void
    {
        $user = $this->actingUser();

        $processo = Processo::create([
            'numero_processo' => '000050-11.2026.8.26.0001',
            'vara_tribunal' => '1ª Vara Cível',
            'tipo_acao' => 'Cível',
            'data_abertura' => now()->toDateString(),
            'status' => 'ativo',
        ]);

        Compromisso::create([
            'titulo' => 'Audiência de instrução',
            'tipo' => Compromisso::TIPO_AUDIENCIA,
            'data_hora' => now()->addDays(3),
            'processo_id' => $processo->id,
            'responsavel_id' => $user->id,
            'created_by' => $user->id,
        ]);

        $this->actingAs($user)
            ->get('/alterar-processos?id=' . $processo->id)
            ->assertOk()
            ->assertSee('Agenda do Processo');
    }

    public function test_feed_da_api_retorna_json_com_eventos(): void
    {
        $user = $this->actingUser();

        Compromisso::create([
            'titulo' => 'Audiência de instrução',
            'tipo' => Compromisso::TIPO_AUDIENCIA,
            'data_hora' => now()->addDays(3),
            'responsavel_id' => $user->id,
            'created_by' => $user->id,
        ]);

        $this->actingAs($user)
            ->get('/api/compromissos/feed')
            ->assertOk()
            ->assertJsonStructure([
                ['id', 'title', 'start', 'color'],
            ]);
    }
}
