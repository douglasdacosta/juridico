<?php

namespace Tests\Feature;

use App\Models\Documento;
use App\Models\Processo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentosVersioningTest extends TestCase
{
    use RefreshDatabase;

    public function test_document_upload_respects_50mb_limit_and_versions_documents(): void
    {
        Storage::fake('local');

        $user = User::create([
            'name' => 'Docs User',
            'email' => 'docs@example.com',
            'password' => Hash::make('12345678'),
            'perfil_acesso' => 1,
        ]);

        $processo = Processo::create([
            'numero_processo' => '000123-11.2026.8.26.0001',
            'vara_tribunal' => '1ª Vara',
            'tipo_acao' => 'Cível',
            'data_abertura' => now()->toDateString(),
            'status' => 'ativo',
            'responsavel_id' => $user->id,
        ]);

        $arquivoGrande = UploadedFile::fake()->create('grande.pdf', 51201, 'application/pdf');

        $this->actingAs($user)
            ->post('/incluir-documentos', [
                'arquivo' => $arquivoGrande,
                'processo_id' => $processo->id,
                'shared_with_client' => 1,
            ])
            ->assertSessionHasErrors('arquivo');

        $arquivoV1 = UploadedFile::fake()->create('peticao-v1.pdf', 1024, 'application/pdf');

        $this->actingAs($user)
            ->post('/incluir-documentos', [
                'arquivo' => $arquivoV1,
                'processo_id' => $processo->id,
                'shared_with_client' => 1,
            ])
            ->assertRedirect('/documentos');

        $documentoV1 = Documento::query()->latest('id')->firstOrFail();

        $this->assertSame(1, (int) $documentoV1->versao);
        $this->assertTrue((bool) $documentoV1->shared_with_client);
        $this->assertTrue(Storage::disk('local')->exists($documentoV1->caminho));

        $arquivoV2 = UploadedFile::fake()->create('peticao-v2.pdf', 1024, 'application/pdf');

        $this->actingAs($user)
            ->post('/incluir-documentos', [
                'arquivo' => $arquivoV2,
                'processo_id' => $processo->id,
                'documento_base_id' => $documentoV1->id,
                'shared_with_client' => 0,
            ])
            ->assertRedirect('/documentos');

        $documentoV2 = Documento::query()->latest('id')->firstOrFail();

        $this->assertSame(2, (int) $documentoV2->versao);
        $this->assertSame($documentoV1->version_group_id, $documentoV2->version_group_id);
        $this->assertFalse((bool) $documentoV2->shared_with_client);
    }
}
