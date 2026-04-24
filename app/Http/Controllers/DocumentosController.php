<?php

namespace App\Http\Controllers;

use App\Models\Andamento;
use App\Models\Documento;
use App\Models\Cliente;
use App\Models\Processo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentosController extends Controller
{
    public function index(Request $request)
    {
        $query = Documento::query()->with(['cliente', 'processo', 'andamento', 'usuario']);

        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', (int) $request->input('cliente_id'));
        }

        if ($request->filled('numero_processo')) {
            $numeroProcesso = trim((string) $request->input('numero_processo'));
            $query->whereHas('processo', fn ($q) => $q->where('numero_processo', 'like', '%' . $numeroProcesso . '%'));
        }

        if ($request->filled('andamento_id')) {
            $query->where('andamento_id', (int) $request->input('andamento_id'));
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', (bool) $request->input('ativo'));
        }

        return view('documentos', [
            'tela' => 'pesquisa',
            'nome_tela' => 'Documentos',
            'rotaIncluir' => 'incluir-documentos',
            'rotaAlterar' => 'alterar-documentos',
            'documentos' => $query->orderByDesc('id')->get(),
            'clientesOptions' => Cliente::query()->orderBy('nome')->pluck('nome', 'id'),
            'processosOptions' => Processo::query()->orderBy('numero_processo')->pluck('numero_processo', 'id'),
            'andamentosOptions' => Andamento::query()->orderByDesc('id')->pluck('id', 'id'),
            'request' => $request,
        ]);
    }

    public function incluir(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'arquivo' => 'required|file|max:51200|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif',
                'cliente_id' => 'nullable|exists:clientes,id',
                'processo_id' => 'nullable|exists:processos,id',
                'andamento_id' => 'nullable|exists:andamentos,id',
                'documento_base_id' => 'nullable|exists:documentos,id',
                'shared_with_client' => 'nullable|boolean',
            ], [
                'arquivo.required' => 'Selecione um arquivo para upload.',
                'arquivo.max' => 'O arquivo deve ter no máximo 50 MB.',
                'arquivo.mimes' => 'Formato de arquivo não permitido.',
            ]);

            $arquivo = $request->file('arquivo');
            $baseDocumento = !empty($validated['documento_base_id'])
                ? Documento::query()->find($validated['documento_base_id'])
                : null;

            $versionGroupId = $baseDocumento?->version_group_id ?: (string) Str::uuid();
            $versao = $baseDocumento
                ? (Documento::query()->where('version_group_id', $versionGroupId)->max('versao') + 1)
                : 1;

            $contexto = $this->resolveContextoPath(
                $validated['cliente_id'] ?? null,
                $validated['processo_id'] ?? null,
                $validated['andamento_id'] ?? null
            );

            $nomeArmazenado = now()->format('YmdHis') . '_' . Str::random(12) . '.' . $arquivo->getClientOriginalExtension();
            $caminho = $arquivo->storeAs($contexto, $nomeArmazenado, 'local');

            $documento = Documento::create([
                'nome_original' => $arquivo->getClientOriginalName(),
                'nome_armazenado' => $nomeArmazenado,
                'tipo_midia' => $arquivo->getClientMimeType(),
                'tamanho' => $arquivo->getSize(),
                'caminho' => $caminho,
                'cliente_id' => $validated['cliente_id'] ?? null,
                'processo_id' => $validated['processo_id'] ?? null,
                'andamento_id' => $validated['andamento_id'] ?? null,
                'version_group_id' => $versionGroupId,
                'versao' => $versao,
                'shared_with_client' => $request->boolean('shared_with_client', true),
                'usuario_id' => auth()->id(),
                'ativo' => true,
            ]);

            // Resposta AJAX
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Documento enviado com sucesso.',
                    'documento' => $documento,
                    '_token' => csrf_token()
                ], 201);
            }

            if ($request->filled('processo_retorno_id')) {
                return redirect()->route('alterar-processos', ['id' => (int) $request->input('processo_retorno_id')])
                    ->with('success', 'Documento enviado com sucesso.');
            }

            return redirect()->route('documentos')->with('success', 'Documento enviado com sucesso.');
        }

        return view('documentos', [
            'tela' => 'incluir',
            'nome_tela' => 'Documentos',
            'rotaIncluir' => 'incluir-documentos',
            'clientesOptions' => Cliente::query()->orderBy('nome')->pluck('nome', 'id'),
            'processosOptions' => Processo::query()->orderBy('numero_processo')->pluck('numero_processo', 'id'),
            'andamentosOptions' => Andamento::query()->orderByDesc('id')->pluck('id', 'id'),
            'documentosBaseOptions' => Documento::query()->where('ativo', true)->orderByDesc('id')->get(),
            'processoSelecionado' => $request->input('processo_id'),
            'processoRetornoId' => $request->input('processo_retorno_id', $request->input('processo_id')),
        ]);
    }

    public function alterar(Request $request)
    {
        if ($request->isMethod('post')) {
            $documento = Documento::query()->findOrFail((int) $request->input('id'));

            $validated = $request->validate([
                'arquivo' => 'nullable|file|max:51200|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif',
                'cliente_id' => 'nullable|exists:clientes,id',
                'processo_id' => 'nullable|exists:processos,id',
                'andamento_id' => 'nullable|exists:andamentos,id',
                'shared_with_client' => 'nullable|boolean',
            ], [
                'arquivo.max' => 'O arquivo deve ter no máximo 50 MB.',
                'arquivo.mimes' => 'Formato de arquivo não permitido.',
            ]);

            $dadosAtualizacao = [
                'cliente_id' => $validated['cliente_id'] ?? null,
                'processo_id' => $validated['processo_id'] ?? null,
                'andamento_id' => $validated['andamento_id'] ?? null,
                'shared_with_client' => true, // Sempre compartilhado
            ];

            if ($request->hasFile('arquivo')) {
                $arquivo = $request->file('arquivo');
                $contexto = $this->resolveContextoPath(
                    $validated['cliente_id'] ?? null,
                    $validated['processo_id'] ?? null,
                    $validated['andamento_id'] ?? null
                );

                $nomeArmazenado = now()->format('YmdHis') . '_' . Str::random(12) . '.' . $arquivo->getClientOriginalExtension();
                $caminho = $arquivo->storeAs($contexto, $nomeArmazenado, 'local');

                $dadosAtualizacao = array_merge($dadosAtualizacao, [
                    'nome_original' => $arquivo->getClientOriginalName(),
                    'nome_armazenado' => $nomeArmazenado,
                    'tipo_midia' => $arquivo->getClientMimeType(),
                    'tamanho' => $arquivo->getSize(),
                    'caminho' => $caminho,
                ]);
            }

            $documento->update($dadosAtualizacao);

            if ($request->filled('processo_retorno_id')) {
                return redirect()->route('alterar-processos', ['id' => (int) $request->input('processo_retorno_id')])
                    ->with('success', 'Documento atualizado com sucesso.');
            }

            return redirect()->route('documentos')->with('success', 'Documento atualizado com sucesso.');
        }

        $documento = Documento::query()->findOrFail((int) $request->input('id'));

        return view('documentos', [
            'tela' => 'alterar',
            'nome_tela' => 'Documentos',
            'rotaAlterar' => 'alterar-documentos',
            'documento' => $documento,
            'clientesOptions' => Cliente::query()->orderBy('nome')->pluck('nome', 'id'),
            'processosOptions' => Processo::query()->orderBy('numero_processo')->pluck('numero_processo', 'id'),
            'andamentosOptions' => Andamento::query()->orderByDesc('id')->pluck('id', 'id'),
            'documentosBaseOptions' => Documento::query()->where('ativo', true)->orderByDesc('id')->get(),
            'processoRetornoId' => $request->input('processo_retorno_id', $documento->processo_id),
        ]);
    }

    public function desativar(Request $request)
    {
        $documento = Documento::query()->findOrFail((int) $request->input('id'));
        $documento->update(['ativo' => false]);

        return redirect()->route('documentos')->with('success', 'Documento desativado com sucesso.');
    }

    public function excluir(Request $request)
    {
        $documento = Documento::query()->findOrFail((int) $request->input('id'));
        $documento->update(['ativo' => false]);

        if ($request->filled('processo_retorno_id')) {
            return redirect()->route('alterar-processos', ['id' => (int) $request->input('processo_retorno_id')])
                ->with('success', 'Documento excluído com sucesso.');
        }

        return redirect()->route('documentos')->with('success', 'Documento excluído com sucesso.');
    }

    public function preview(int $id)
    {
        $documento = Documento::query()->findOrFail($id);

        if (! Storage::disk('local')->exists($documento->caminho)) {
            return redirect()->back()->with('error', 'Arquivo não encontrado no armazenamento.');
        }

        $mime = $documento->tipo_midia ?: (Storage::disk('local')->mimeType($documento->caminho) ?: 'application/octet-stream');

        return response()->file(
            Storage::disk('local')->path($documento->caminho),
            [
                'Content-Type' => $mime,
                'Content-Disposition' => 'inline; filename="' . addslashes($documento->nome_original) . '"',
            ]
        );
    }

    private function resolveContextoPath($clienteId, $processoId, $andamentoId): string
    {
        if ($andamentoId) {
            return 'documentos/andamentos/' . $andamentoId;
        }

        if ($processoId) {
            return 'documentos/processos/' . $processoId;
        }

        if ($clienteId) {
            return 'documentos/clientes/' . $clienteId;
        }

        return 'documentos/geral';
    }
}
