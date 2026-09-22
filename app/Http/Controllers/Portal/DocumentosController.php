<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Documento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentosController extends Controller
{
    public function index()
    {
        $cliente = Auth::guard('cliente')->user();
        $processoIds = $cliente->processos()->pluck('processos.id');

        $documentos = Documento::query()
            ->with('processo')
            ->whereIn('processo_id', $processoIds)
            ->where('shared_with_client', true)
            ->where('ativo', true)
            ->orderByDesc('id')
            ->get();

        return view('portal.documentos', [
            'documentos' => $documentos,
        ]);
    }

    /**
     * Serve o arquivo apenas se pertencer a um processo do cliente autenticado
     * E estiver marcado como compartilhado (shared_with_client) — nunca reaproveita
     * o preview administrativo, que não tem essa restrição de dono.
     */
    public function preview(int $id)
    {
        $cliente = Auth::guard('cliente')->user();
        $processoIds = $cliente->processos()->pluck('processos.id');

        $documento = Documento::query()
            ->whereIn('processo_id', $processoIds)
            ->where('shared_with_client', true)
            ->where('ativo', true)
            ->findOrFail($id);

        if (! Storage::disk('local')->exists($documento->caminho)) {
            abort(404, 'Arquivo não encontrado.');
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
}
