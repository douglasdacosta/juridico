<?php

namespace App\Http\Controllers;

use App\Models\TipoAcao;
use Illuminate\Http\Request;

class TiposAcaoController extends Controller
{
    public function index(Request $request)
    {
        $query = TipoAcao::query();

        if ($request->filled('nome')) {
            $query->where('nome', 'like', '%' . trim((string) $request->input('nome')) . '%');
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', (bool) $request->input('ativo'));
        }

        $tipos = $query->orderBy('nome')->get();

        return view('tipos_acao', [
            'tela' => 'pesquisa',
            'nome_tela' => 'Tipos de Ação',
            'rotaIndex' => 'tipos-acao',
            'rotaAlterar' => 'alterar-tipos-acao',
            'rotaIncluir' => 'incluir-tipos-acao',
            'tipos' => $tipos,
            'request' => $request,
        ]);
    }

    public function incluir(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'nome' => 'required|string|max:100|unique:tipos_acao,nome',
                'descricao' => 'nullable|string|max:1000',
                'ativo' => 'required|boolean',
            ], [
                'nome.required' => 'O nome é obrigatório.',
                'nome.unique' => 'Já existe um tipo de ação com este nome.',
                'ativo.required' => 'O status é obrigatório.',
            ]);

            TipoAcao::create($validated);

            return redirect()->route('tipos-acao')->with('success', 'Tipo de ação criado com sucesso.');
        }

        return view('tipos_acao', [
            'tela' => 'incluir',
            'nome_tela' => 'Tipos de Ação',
            'rotaIndex' => 'tipos-acao',
            'rotaAlterar' => 'alterar-tipos-acao',
            'rotaIncluir' => 'incluir-tipos-acao',
        ]);
    }

    public function alterar(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'id' => 'required|integer',
                'nome' => 'required|string|max:100|unique:tipos_acao,nome,' . $request->input('id'),
                'descricao' => 'nullable|string|max:1000',
                'ativo' => 'required|boolean',
            ], [
                'nome.required' => 'O nome é obrigatório.',
                'nome.unique' => 'Já existe um tipo de ação com este nome.',
                'ativo.required' => 'O status é obrigatório.',
            ]);

            $tipo = TipoAcao::findOrFail((int) $validated['id']);
            $tipo->update($validated);

            return redirect()->route('tipos-acao')->with('success', 'Tipo de ação atualizado com sucesso.');
        }

        $tipo = TipoAcao::findOrFail((int) $request->input('id'));

        return view('tipos_acao', [
            'tela' => 'alterar',
            'nome_tela' => 'Tipos de Ação',
            'rotaIndex' => 'tipos-acao',
            'rotaAlterar' => 'alterar-tipos-acao',
            'rotaIncluir' => 'incluir-tipos-acao',
            'tipos' => [$tipo],
        ]);
    }

    public function desativar(Request $request)
    {
        $validated = $request->validate(['id' => 'required|integer']);

        $tipo = TipoAcao::findOrFail((int) $validated['id']);

        // Verificar se há andamentos usando este tipo
        if ($tipo->andamentos()->count() > 0) {
            return redirect()->route('tipos-acao')
                ->with('error', 'Não é possível desativar este tipo pois existem andamentos vinculados. Altere o status para Inativo ao invés de excluir.');
        }

        $tipo->delete();

        return redirect()->route('tipos-acao')->with('success', 'Tipo de ação excluído com sucesso.');
    }
}
