<?php

namespace App\Http\Controllers;

use App\Models\ModeloDocumento;
use App\Services\GeradorDocumentoService;
use Illuminate\Http\Request;

class ModeloDocumentoController extends Controller
{
    public function index(Request $request)
    {
        $query = ModeloDocumento::query();

        if ($request->filled('nome')) {
            $query->where('nome', 'like', '%' . trim((string) $request->input('nome')) . '%');
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->input('tipo'));
        }

        return view('modelos-documento', $this->formData('pesquisa', [
            'modelos' => $query->orderBy('nome')->get(),
            'request' => $request,
        ]));
    }

    public function incluir(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $this->validarDados($request);

            ModeloDocumento::create($validated);

            return redirect()->route('modelos-documento')->with('success', 'Modelo de documento criado com sucesso.');
        }

        return view('modelos-documento', $this->formData('incluir'));
    }

    public function alterar(Request $request)
    {
        if ($request->isMethod('post')) {
            $modelo = ModeloDocumento::query()->findOrFail((int) $request->input('id'));
            $modelo->update($this->validarDados($request));

            return redirect()->route('modelos-documento')->with('success', 'Modelo de documento atualizado com sucesso.');
        }

        $modelo = ModeloDocumento::query()->findOrFail((int) $request->input('id'));

        return view('modelos-documento', $this->formData('alterar', [
            'modelo' => $modelo,
        ]));
    }

    public function desativar(Request $request)
    {
        $modelo = ModeloDocumento::query()->findOrFail((int) $request->input('id'));
        $modelo->update(['ativo' => false]);

        return redirect()->route('modelos-documento')->with('success', 'Modelo desativado com sucesso.');
    }

    private function validarDados(Request $request): array
    {
        return $request->validate([
            'nome' => 'required|string|max:255',
            'tipo' => 'required|in:' . implode(',', array_keys(ModeloDocumento::TIPO_OPTIONS)),
            'corpo' => 'required|string',
            'ativo' => 'nullable|boolean',
        ], [
            'nome.required' => 'O nome do modelo é obrigatório.',
            'tipo.required' => 'O tipo é obrigatório.',
            'corpo.required' => 'O conteúdo do modelo é obrigatório.',
        ]) + ['ativo' => $request->boolean('ativo', true)];
    }

    private function formData(string $tela, array $extra = []): array
    {
        return array_merge([
            'tela' => $tela,
            'nome_tela' => 'Modelos de Documento',
            'rotaAlterar' => 'alterar-modelos-documento',
            'rotaIncluir' => 'incluir-modelos-documento',
            'tipoOptions' => ModeloDocumento::TIPO_OPTIONS,
            'placeholders' => app(GeradorDocumentoService::class)->placeholdersDisponiveis(),
        ], $extra);
    }
}
