<?php

namespace App\Http\Controllers;

use App\Models\Filial;
use Illuminate\Http\Request;

class FiliaisController extends Controller
{
    public function index(Request $request)
    {
        $query = Filial::query();

        if ($request->filled('nome')) {
            $query->where('nome', 'like', '%' . trim((string) $request->input('nome')) . '%');
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', (bool) $request->input('ativo'));
        }

        return view('filiais', [
            'tela' => 'pesquisa',
            'nome_tela' => 'Filiais',
            'rotaAlterar' => 'alterar-filiais',
            'rotaIncluir' => 'incluir-filiais',
            'filiais' => $query->orderBy('id', 'desc')->get(),
            'request' => $request,
        ]);
    }

    public function alterar(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'id' => 'required|integer|exists:filiais,id',
                'nome' => 'required|string|max:255',
                'cnpj' => 'nullable|string|max:20|unique:filiais,cnpj,' . $request->input('id'),
                'endereco' => 'nullable|string|max:255',
                'ativo' => 'required|in:0,1',
            ], [
                'nome.required' => 'O nome da filial é obrigatório.',
                'cnpj.unique' => 'Já existe uma filial com esse CNPJ.',
            ]);

            Filial::query()->whereKey((int) $validated['id'])->update([
                'nome' => $validated['nome'],
                'cnpj' => $validated['cnpj'] ?: null,
                'endereco' => $validated['endereco'] ?: null,
                'ativo' => (bool) $validated['ativo'],
            ]);

            return redirect()->route('filiais')->with('success', 'Filial atualizada com sucesso.');
        }

        $filial = Filial::query()->findOrFail((int) $request->input('id'));

        return view('filiais', [
            'tela' => 'alterar',
            'nome_tela' => 'Filiais',
            'rotaAlterar' => 'alterar-filiais',
            'rotaIncluir' => 'incluir-filiais',
            'filial' => $filial,
        ]);
    }

    public function incluir(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'nome' => 'required|string|max:255',
                'cnpj' => 'nullable|string|max:20|unique:filiais,cnpj',
                'endereco' => 'nullable|string|max:255',
                'ativo' => 'required|in:0,1',
            ], [
                'nome.required' => 'O nome da filial é obrigatório.',
                'cnpj.unique' => 'Já existe uma filial com esse CNPJ.',
            ]);

            Filial::create([
                'nome' => $validated['nome'],
                'cnpj' => $validated['cnpj'] ?: null,
                'endereco' => $validated['endereco'] ?: null,
                'ativo' => (bool) $validated['ativo'],
            ]);

            return redirect()->route('filiais')->with('success', 'Filial incluída com sucesso.');
        }

        return view('filiais', [
            'tela' => 'incluir',
            'nome_tela' => 'Filiais',
            'rotaAlterar' => 'alterar-filiais',
            'rotaIncluir' => 'incluir-filiais',
        ]);
    }

    /**
     * API endpoint para Select2 - busca filiais com paginação
     */
    public function apiSearch(Request $request)
    {
        $search = trim((string) $request->input('q', ''));
        $page = max(1, (int) $request->input('page', 1));
        $perPage = 10;

        $query = Filial::query()
            ->where('ativo', true)
            ->orderBy('nome');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('cnpj', 'like', '%' . preg_replace('/[^0-9]/', '', $search) . '%');
            });
        }

        $total = $query->count();
        $filiais = $query
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get(['id', 'nome', 'cnpj']);

        // Formatar para Select2
        $results = $filiais->map(function ($filial) {
            $text = $filial->nome;
            if ($filial->cnpj) {
                $text .= " (CNPJ: {$filial->cnpj})";
            }
            return [
                'id' => $filial->id,
                'text' => $text,
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
