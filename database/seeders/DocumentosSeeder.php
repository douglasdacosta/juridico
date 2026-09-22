<?php

namespace Database\Seeders;

use App\Models\Documento;
use App\Models\Processo;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentosSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = User::query()->where('perfil_acesso', 2)->pluck('id');

        if ($usuarios->isEmpty()) {
            return;
        }

        $tiposDocumento = [
            ['nome' => 'Procuracao.pdf', 'mime' => 'application/pdf'],
            ['nome' => 'Contrato_Honorarios.pdf', 'mime' => 'application/pdf'],
            ['nome' => 'Peticao_Inicial.pdf', 'mime' => 'application/pdf'],
            ['nome' => 'Documentos_Pessoais.jpg', 'mime' => 'image/jpeg'],
        ];

        Processo::with('clientes')->doesntHave('documentos')->get()->each(function (Processo $processo) use ($usuarios, $tiposDocumento) {
            $cliente = $processo->clientes->first();
            $tipo = $tiposDocumento[array_rand($tiposDocumento)];

            $contexto = 'documentos/processos/' . $processo->id;
            $nomeArmazenado = now()->format('YmdHis') . '_' . Str::random(12) . '.' . pathinfo($tipo['nome'], PATHINFO_EXTENSION);
            $conteudo = "Documento gerado pelo seeder para o processo {$processo->numero_processo}.";
            $caminho = $contexto . '/' . $nomeArmazenado;

            Storage::disk('local')->put($caminho, $conteudo);

            Documento::create([
                'nome_original' => $tipo['nome'],
                'nome_armazenado' => $nomeArmazenado,
                'tipo_midia' => $tipo['mime'],
                'tamanho' => strlen($conteudo),
                'caminho' => $caminho,
                'cliente_id' => $cliente?->id,
                'processo_id' => $processo->id,
                'versao' => 1,
                'shared_with_client' => true,
                'usuario_id' => $usuarios->random(),
                'ativo' => true,
            ]);
        });
    }
}
