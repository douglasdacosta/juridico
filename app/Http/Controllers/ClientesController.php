<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Processo;
use App\Models\User;
use Illuminate\Http\Request;

class ClientesController extends Controller
{
    public function index(Request $request)
    {
        $clientes = $this->baseQuery($request)->with('responsaveis')->orderBy('id', 'desc')->get();

        return view('clientes', [
            'tela' => 'pesquisa',
            'nome_tela' => 'Clientes',
            'rotaIndex' => 'clientes',
            'rotaAlterar' => 'alterar-clientes',
            'rotaIncluir' => 'incluir-clientes',
            'rotaExportarCsv' => 'exportar-clientes-csv',
            'rotaExportarPdf' => 'exportar-clientes-pdf',
            'clientes' => $clientes,
            'responsaveisOptions' => User::query()->orderBy('name')->pluck('name', 'id'),
            'processosOptions' => Processo::query()->orderBy('numero_processo')->pluck('numero_processo', 'id'),
            'request' => $request,
            'perfil' => auth()->check() ? (int) auth()->user()->perfil_acesso : 2,
        ]);
    }

    public function exportCsv(Request $request)
    {
        $clientes = $this->baseQuery($request)->orderBy('id', 'desc')->get();

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, ['ID', 'Nome', 'E-mail', 'Telefone', 'Status', 'Cidade', 'Estado']);

        foreach ($clientes as $cliente) {
            fputcsv($handle, [
                $cliente->id,
                $cliente->nome,
                $cliente->email,
                $cliente->telefone,
                $cliente->status,
                $cliente->cidade,
                $cliente->estado,
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle) ?: '';
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="clientes.csv"',
        ]);
    }

    public function exportPrint(Request $request)
    {
        return view('exports.clientes-print', [
            'clientes' => $this->baseQuery($request)->orderBy('id', 'desc')->get(),
        ]);
    }

    public function alterar(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'id' => 'required|integer',
                'nome' => 'required|string|max:255',
                'cpf' => 'nullable|string|max:14',
                'email' => 'nullable|email|max:255',
                'status' => 'required|in:A,I',
                'estado' => 'nullable|string|size:2',
                'responsaveis' => 'nullable|array',
                'responsaveis.*' => 'integer|exists:users,id',
                'lgpd_consent' => 'nullable|boolean',
                'lgpd_purpose' => 'nullable|string|max:1000',
            ], [
                'nome.required' => 'O nome é obrigatório.',
                'email.email' => 'Informe um e-mail válido.',
                'status.required' => 'O status é obrigatório.',
                'status.in' => 'Status inválido.',
                'estado.size' => 'Informe a UF com 2 letras (ex: SP).',
                'lgpd_purpose.max' => 'A finalidade LGPD deve ter no máximo 1000 caracteres.',
            ]);

            if ($request->boolean('lgpd_consent') && trim((string) $request->input('lgpd_purpose')) === '') {
                return redirect()->back()
                    ->withErrors(['lgpd_purpose' => 'Informe a finalidade do tratamento de dados ao conceder consentimento LGPD.'])
                    ->withInput();
            }

            $cliente = Cliente::query()->findOrFail((int) $validated['id']);
            $payload = [
                'nome' => $validated['nome'],
                'cpf' => $request->input('cpf'),
                'email' => $validated['email'],
                'endereco' => $request->input('endereco'),
                'numero' => $request->input('numero'),
                'bairro' => $request->input('bairro'),
                'cidade' => $request->input('cidade'),
                'estado' => $request->filled('estado') ? strtoupper(trim((string) $request->input('estado'))) : null,
                'cep' => $request->input('cep'),
                'telefone' => $request->input('telefone'),
                'status' => $validated['status'],
                'ativo' => $validated['status'] === 'A',
                'lgpd_consent_at' => $request->boolean('lgpd_consent') ? now() : null,
                'lgpd_purpose' => $request->boolean('lgpd_consent') ? trim((string) $request->input('lgpd_purpose')) : null,
            ];

            $cliente->update($payload);
            $cliente->responsaveis()->sync($request->input('responsaveis', []));

            return redirect()->route('clientes')->with('success', 'Cliente atualizado com sucesso.');
        }

        $cliente = Cliente::query()->with('responsaveis')->findOrFail((int) $request->input('id'));

        return view('clientes', [
            'tela' => 'alterar',
            'nome_tela' => 'Clientes',
            'rotaIndex' => 'clientes',
            'rotaAlterar' => 'alterar-clientes',
            'rotaIncluir' => 'incluir-clientes',
            'rotaExportarCsv' => 'exportar-clientes-csv',
            'rotaExportarPdf' => 'exportar-clientes-pdf',
            'clientes' => [$cliente],
            'responsaveisOptions' => User::query()->orderBy('name')->pluck('name', 'id'),
            'processosOptions' => Processo::query()->orderBy('numero_processo')->pluck('numero_processo', 'id'),
            'request' => $request,
            'perfil' => auth()->check() ? (int) auth()->user()->perfil_acesso : 2,
        ]);
    }

    public function incluir(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'nome' => 'required|string|max:255',
                'cpf' => 'nullable|string|max:14',
                'email' => 'nullable|email|max:255',
                'status' => 'required|in:A,I',
                'estado' => 'nullable|string|size:2',
                'responsaveis' => 'nullable|array',
                'responsaveis.*' => 'integer|exists:users,id',
                'lgpd_consent' => 'nullable|boolean',
                'lgpd_purpose' => 'nullable|string|max:1000',
            ], [
                'nome.required' => 'O nome é obrigatório.',
                'email.email' => 'Informe um e-mail válido.',
                'status.required' => 'O status é obrigatório.',
                'status.in' => 'Status inválido.',
                'estado.size' => 'Informe a UF com 2 letras (ex: SP).',
                'lgpd_purpose.max' => 'A finalidade LGPD deve ter no máximo 1000 caracteres.',
            ]);

            if ($request->boolean('lgpd_consent') && trim((string) $request->input('lgpd_purpose')) === '') {
                return redirect()->back()
                    ->withErrors(['lgpd_purpose' => 'Informe a finalidade do tratamento de dados ao conceder consentimento LGPD.'])
                    ->withInput();
            }

            $cliente = Cliente::query()->create([
                'nome' => $validated['nome'],
                'cpf' => $request->input('cpf'),
                'email' => $validated['email'],
                'endereco' => $request->input('endereco'),
                'numero' => $request->input('numero'),
                'bairro' => $request->input('bairro'),
                'cidade' => $request->input('cidade'),
                'estado' => $request->filled('estado') ? strtoupper(trim((string) $request->input('estado'))) : null,
                'cep' => $request->input('cep'),
                'telefone' => $request->input('telefone'),
                'status' => $validated['status'],
                'ativo' => $validated['status'] === 'A',
                'lgpd_consent_at' => $request->boolean('lgpd_consent') ? now() : null,
                'lgpd_purpose' => $request->boolean('lgpd_consent') ? trim((string) $request->input('lgpd_purpose')) : null,
            ]);

            $cliente->responsaveis()->sync($request->input('responsaveis', []));

            return redirect()->route('clientes')->with('success', 'Cliente incluído com sucesso.');
        }

        return view('clientes', [
            'tela' => 'incluir',
            'nome_tela' => 'Clientes',
            'rotaIndex' => 'clientes',
            'rotaAlterar' => 'alterar-clientes',
            'rotaIncluir' => 'incluir-clientes',
            'rotaExportarCsv' => 'exportar-clientes-csv',
            'rotaExportarPdf' => 'exportar-clientes-pdf',
            'responsaveisOptions' => User::query()->orderBy('name')->pluck('name', 'id'),
            'processosOptions' => Processo::query()->orderBy('numero_processo')->pluck('numero_processo', 'id'),
            'request' => $request,
            'perfil' => auth()->check() ? (int) auth()->user()->perfil_acesso : 2,
        ]);
    }

    public function desativar(Request $request)
    {
        $cliente = Cliente::query()->findOrFail((int) $request->input('id'));

        $cliente->update([
            'ativo' => false,
            'status' => 'I',
        ]);

        return redirect()->route('clientes')->with('success', 'Cliente desativado com sucesso.');
    }

    private function baseQuery(Request $request)
    {
        $query = Cliente::query();

        if ($request->filled('nome')) {
            $query->where('nome', 'like', '%' . trim((string) $request->input('nome')) . '%');
        }

        if ($request->filled('cpf')) {
            // Remove formatação para buscar apenas números
            $cpf = preg_replace('/[^0-9]/', '', trim((string) $request->input('cpf')));
            $query->where('cpf', 'like', '%' . $cpf . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', (bool) $request->input('ativo'));
        }

        if ($request->filled('numero_processo')) {
            $numeroProcesso = trim((string) $request->input('numero_processo'));
            $query->whereHas('processos', fn ($q) => $q->where('processos.numero_processo', 'like', '%' . $numeroProcesso . '%'));
        }

        return $query;
    }

    /**
     * API endpoint para Select2 - busca clientes com paginação
     */
    public function apiSearch(Request $request)
    {
        $search = trim((string) $request->input('q', ''));
        $page = max(1, (int) $request->input('page', 1));
        $perPage = 10;

        $query = Cliente::query()
            ->where('ativo', true)
            ->orderBy('nome');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('cpf', 'like', '%' . preg_replace('/[^0-9]/', '', $search) . '%')
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $total = $query->count();
        $clientes = $query
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get(['id', 'nome', 'cpf', 'email']);

        // Formatar para Select2
        $results = $clientes->map(function ($cliente) {
            $text = $cliente->nome;
            if ($cliente->cpf) {
                $text .= " (CPF: {$cliente->cpf})";
            }
            return [
                'id' => $cliente->id,
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
