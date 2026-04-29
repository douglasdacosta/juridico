<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAndamentosRequest;
use App\Http\Requests\UpdateAndamentosRequest;
use App\Models\Andamento;
use App\Models\Processo;
use Illuminate\Http\Request;

class AndamentosController extends Controller
{
    public function index(Request $request)
    {
        $query = Andamento::query()->with(['processo', 'usuario', 'criador']);

        if ($request->filled('numero_processo')) {
            $numeroProcesso = trim((string) $request->input('numero_processo'));
            $query->whereHas('processo', fn ($q) => $q->where('numero_processo', 'like', '%' . $numeroProcesso . '%'));
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->input('tipo'));
        }

        $andamentos = $query->orderByDesc('data_andamento')->orderByDesc('id')->get();

        return view('andamentos', [
            'tela' => 'pesquisa',
            'nome_tela' => 'Andamentos',
            'rotaAlterar' => 'alterar-andamentos',
            'rotaIncluir' => 'incluir-andamentos',
            'andamentos' => $andamentos,
            'processosOptions' => Processo::query()->orderBy('numero_processo')->pluck('numero_processo', 'id'),
            'request' => $request,
        ]);
    }

    public function incluir(StoreAndamentosRequest $request)
    {
        if ($request->isMethod('post')) {
            $authUser = auth()->user();

            $andamento = Andamento::create([
                'processo_id' => $request->input('processo_id'),
                'tipo' => $request->input('tipo'),
                'data_andamento' => $request->input('data_andamento'),
                'descricao' => $request->input('descricao'),
                'usuario_id' => $request->input('usuario_id') ?: $authUser->id,
                'created_by' => $authUser->id,
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'andamento' => $andamento,
                    '_token' => csrf_token()
                ], 201);
            }

            return redirect()->route('andamentos')->with('success', 'Andamento incluído com sucesso.');
        }

        return view('andamentos', $this->formData('incluir'));
    }

    public function alterar(UpdateAndamentosRequest $request)
    {
        // Suportar DELETE
        if ($request->isMethod('delete')) {
            $id = $request->input('id') ?: $request->query('id');
            $andamento = Andamento::query()->findOrFail($id);
            $andamento->delete();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Andamento excluído com sucesso.',
                    '_token' => csrf_token()
                ]);
            }

            return redirect()->route('andamentos')->with('success', 'Andamento excluído com sucesso.');
        }

        if ($request->isMethod('post')) {
            $andamento = Andamento::query()->findOrFail((int) $request->input('id'));
            $authUser = auth()->user();

            $andamento->update([
                'processo_id' => $request->input('processo_id'),
                'tipo' => $request->input('tipo'),
                'data_andamento' => $request->input('data_andamento'),
                'descricao' => $request->input('descricao'),
                'usuario_id' => $request->input('usuario_id') ?: ($authUser ? $authUser->id : null),
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'andamento' => $andamento,
                    '_token' => csrf_token()
                ]);
            }

            return redirect()->route('andamentos')->with('success', 'Andamento atualizado com sucesso.');
        }

        $id = $request->input('id') ?: $request->query('id');
        $andamento = Andamento::query()->findOrFail((int) $id);
        $authUser = auth()->user();

        if (! $authUser) {
            abort(401);
        }

        $isAdmin = method_exists($authUser, 'isAdmin') ? $authUser->isAdmin() : false;
        if (! $isAdmin && (int) $andamento->created_by !== (int) $authUser->id) {
            abort(403);
        }

        return view('andamentos', array_merge($this->formData('alterar'), [
            'andamento' => $andamento,
        ]));
    }

    /**
     * API endpoint para buscar andamentos de um processo específico
     */
    public function porProcesso(int $processoId)
    {
        $andamentos = Andamento::query()
            ->where('processo_id', $processoId)
            ->orderByDesc('data_andamento')
            ->orderByDesc('id')
            ->get()
            ->map(fn ($a) => [
                'id' => $a->id,
                'tipo' => ucfirst($a->tipo),
                'data_andamento' => $a->data_andamento->format('d/m/Y'),
                'descricao' => str()->limit($a->descricao, 60),
            ]);

        return response()->json($andamentos);
    }

    /**
     * API endpoint para buscar um andamento específico
     */
    public function show(int $id)
    {
        try {
            $andamento = Andamento::query()->findOrFail($id);

            // Simplesmente retornar o andamento sem verificação de permissão complexa
            // já que o middleware 'web' garante que o usuário está autenticado
            return response()->json([
                'id' => $andamento->id,
                'tipo' => $andamento->tipo,
                'data_andamento' => $andamento->data_andamento ? $andamento->data_andamento->format('Y-m-d') : null,
                'descricao' => $andamento->descricao,
                'processo_id' => $andamento->processo_id,
                'usuario_id' => $andamento->usuario_id,
                '_token' => csrf_token(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao buscar andamento',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function formData(string $tela): array
    {
        return [
            'tela' => $tela,
            'nome_tela' => 'Andamentos',
            'rotaAlterar' => 'alterar-andamentos',
            'rotaIncluir' => 'incluir-andamentos',
            'processosOptions' => Processo::query()->orderBy('numero_processo')->pluck('numero_processo', 'id'),
        ];
    }
}
