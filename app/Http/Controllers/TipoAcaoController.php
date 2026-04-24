<?php

namespace App\Http\Controllers;

use App\Models\TipoAcao;
use Illuminate\Http\Request;

class TipoAcaoController extends Controller
{
    public function index(Request $request)
    {
        $query = TipoAcao::query();

        if ($request->filled('nome')) {
            $query->where('nome', 'like', '%' . trim($request->input('nome')) . '%');
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', (bool) $request->input('ativo'));
        }

        $tiposAcao = $query->orderBy('nome')->get();

        return view('tipos-acao', [
            'tela' => 'pesquisa',
            'nome_tela' => 'Tipos de Ação',
            'rotaAlterar' => 'alterar-tipos-acao',
            'rotaIncluir' => 'incluir-tipos-acao',
            'tiposAcao' => $tiposAcao,
            'request' => $request,
        ]);
    }

    public function incluir(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'nome' => 'required|string|max:100|unique:tipos_acao,nome',
                'descricao' => 'nullable|string|max:500',
            ], [
                'nome.required' => 'Nome is required.',
                'nome.unique' => 'This type of action already exists.',
            ]);

            TipoAcao::create($validated);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tipo de ação criado com sucesso.',
                    '_token' => csrf_token()
                ], 201);
            }

            return redirect()->route('tipos-acao')->with('success', 'Tipo de ação criado com sucesso.');
        }

        return view('tipos-acao', [
            'tela' => 'incluir',
            'nome_tela' => 'Tipos de Ação',
            'rotaAlterar' => 'alterar-tipos-acao',
            'rotaIncluir' => 'incluir-tipos-acao',
        ]);
    }

    public function alterar(Request $request)
    {
        if ($request->isMethod('post')) {
            $tipoAcao = TipoAcao::findOrFail((int) $request->input('id'));

            $validated = $request->validate([
                'nome' => 'required|string|max:100|unique:tipos_acao,nome,' . $tipoAcao->id,
                'descricao' => 'nullable|string|max:500',
            ]);

            $tipoAcao->update($validated);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tipo de ação atualizado com sucesso.',
                    '_token' => csrf_token()
                ]);
            }

            return redirect()->route('tipos-acao')->with('success', 'Tipo de ação atualizado com sucesso.');
        }

        $tipoAcao = TipoAcao::findOrFail((int) $request->input('id'));

        return view('tipos-acao', [
            'tela' => 'alterar',
            'nome_tela' => 'Tipos de Ação',
            'rotaAlterar' => 'alterar-tipos-acao',
            'rotaIncluir' => 'incluir-tipos-acao',
            'tipoAcao' => $tipoAcao,
        ]);
    }

    public function desativar(Request $request)
    {
        $tipoAcao = TipoAcao::findOrFail((int) $request->input('id'));
        $tipoAcao->update(['ativo' => false]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Tipo de ação desativado com sucesso.',
            ]);
        }

        return redirect()->back()->with('success', 'Tipo de ação desativado com sucesso.');
    }

    public function excluir(Request $request)
    {
        $tipoAcao = TipoAcao::findOrFail((int) $request->input('id'));
        $tipoAcao->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Tipo de ação excluído com sucesso.',
            ]);
        }

        return redirect()->back()->with('success', 'Tipo de ação excluído com sucesso.');
    }

    /**
     * API endpoint para buscar tipos de ação para Select2
     */
    public function apiSearch(Request $request)
    {
        $search = trim((string) $request->input('q', ''));
        $page = max(1, (int) $request->input('page', 1));
        $perPage = 10;

        $query = TipoAcao::query()
            ->where('ativo', true)
            ->orderBy('nome');

        if ($search !== '') {
            $query->where('nome', 'like', "%{$search}%");
        }

        $total = $query->count();
        $tiposAcao = $query
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get(['id', 'nome']);

        // Formatar para Select2
        $results = $tiposAcao->map(function ($tipo) {
            return [
                'id' => $tipo->id,
                'text' => $tipo->nome,
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => ($page * $perPage) < $total
            ]
        ]);
    }
}
