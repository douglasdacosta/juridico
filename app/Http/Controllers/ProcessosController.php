<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProcessosRequest;
use App\Http\Requests\UpdateProcessosRequest;
use App\Models\Filial;
use App\Models\Cliente;
use App\Models\Processo;
use App\Models\User;
use Illuminate\Http\Request;

class ProcessosController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->baseQuery($request);

        return view('processos', [
            'tela' => 'pesquisa',
            'nome_tela' => 'Processos',
            'rotaAlterar' => 'alterar-processos',
            'rotaIncluir' => 'incluir-processos',
            'processos' => $query->orderByDesc('id')->get(),
            'clientesOptions' => Cliente::query()->orderBy('nome')->pluck('nome', 'id'),
            'request' => $request,
        ]);
    }

    public function exportCsv(Request $request)
    {
        $processos = $this->baseQuery($request)->orderByDesc('id')->get();
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, ['ID', 'Número', 'Vara/Tribunal', 'Tipo', 'Status', 'Responsável', 'Clientes', 'Filiais']);

        foreach ($processos as $processo) {
            fputcsv($handle, [
                $processo->id,
                $processo->numero_processo,
                $processo->vara_tribunal,
                $processo->tipoAcao?->nome ?? ($processo->tipo_acao ?? ''),
                $processo->status,
                $processo->responsavel->name ?? '',
                $processo->clientes->pluck('nome')->implode(' | '),
                $processo->filiais->pluck('nome')->implode(' | '),
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle) ?: '';
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="processos.csv"',
        ]);
    }

    public function exportPrint(Request $request)
    {
        return view('exports.processos-print', [
            'processos' => $this->baseQuery($request)->orderByDesc('id')->get(),
        ]);
    }

    public function incluir(StoreProcessosRequest $request)
    {
        if ($request->isMethod('post')) {
            $processo = Processo::create([
                'numero_processo' => $request->input('numero_processo'),
                'vara_tribunal' => $request->input('vara_tribunal'),
                'tipo_acao' => $request->input('tipo_acao'),
                'data_abertura' => $request->input('data_abertura'),
                'data_encerramento' => $request->input('status') === 'encerrado' ? now()->toDateString() : null,
                'status' => $request->input('status'),
                'responsavel_id' => $request->input('responsavel_id'),
                'observacoes' => $request->input('observacoes'),
            ]);

            $processo->clientes()->sync($this->mapClientes($request->input('clientes', [])));
            $processo->filiais()->sync($request->input('filiais', []));

            return redirect()->route('processos')->with('success', 'Processo incluído com sucesso.');
        }

        return view('processos', $this->formData('incluir'));
    }

    public function alterar(UpdateProcessosRequest $request)
    {
        if ($request->isMethod('post')) {
            $processo = Processo::query()->findOrFail((int) $request->input('id'));
            $novoStatus = $request->input('status');

            $processo->update([
                'numero_processo' => $request->input('numero_processo'),
                'vara_tribunal' => $request->input('vara_tribunal'),
                'tipo_acao' => $request->input('tipo_acao'),
                'data_abertura' => $request->input('data_abertura'),
                'data_encerramento' => $novoStatus === 'encerrado'
                    ? ($processo->data_encerramento?->toDateString() ?? now()->toDateString())
                    : null,
                'status' => $novoStatus,
                'responsavel_id' => $request->input('responsavel_id'),
                'observacoes' => $request->input('observacoes'),
            ]);

            $processo->clientes()->sync($this->mapClientes($request->input('clientes', [])));
            $processo->filiais()->sync($request->input('filiais', []));

            return redirect()->route('processos')->with('success', 'Processo atualizado com sucesso.');
        }

        $processo = Processo::query()->with(['clientes', 'filiais', 'documentos.andamento', 'andamentos.usuario', 'andamentos.criador'])->findOrFail((int) $request->input('id'));

        return view('processos', array_merge($this->formData('alterar'), [
            'processo' => $processo,
        ]));
    }

    private function formData(string $tela): array
    {
        return [
            'tela' => $tela,
            'nome_tela' => 'Processos',
            'rotaAlterar' => 'alterar-processos',
            'rotaIncluir' => 'incluir-processos',
            'clientesOptions' => Cliente::query()->orderBy('nome')->pluck('nome', 'id'),
            'filiaisOptions' => Filial::query()->orderBy('nome')->pluck('nome', 'id'),
            'responsaveisOptions' => User::query()->orderBy('name')->pluck('name', 'id'),
        ];
    }

    private function mapClientes(array $clientes): array
    {
        $mapped = [];

        foreach ($clientes as $clienteId) {
            $mapped[$clienteId] = ['papel_cliente' => 'principal'];
        }

        return $mapped;
    }

    private function baseQuery(Request $request)
    {
        $query = Processo::query()->with(['responsavel', 'clientes', 'filiais', 'tipoAcao']);

        if ($request->filled('numero_processo')) {
            $query->where('numero_processo', 'like', '%' . trim((string) $request->input('numero_processo')) . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('cliente_id')) {
            $clienteId = (int) $request->input('cliente_id');
            $query->whereHas('clientes', fn ($q) => $q->where('clientes.id', $clienteId));
        }

        if ($request->filled('data_de')) {
            try {
                $dataDe = \Carbon\Carbon::createFromFormat('d/m/Y', $request->input('data_de'))->startOfDay();
                $query->where('created_at', '>=', $dataDe);
            } catch (\Exception $e) {
                // Data inválida, ignora o filtro
            }
        }

        if ($request->filled('data_ate')) {
            try {
                $dataAte = \Carbon\Carbon::createFromFormat('d/m/Y', $request->input('data_ate'))->endOfDay();
                $query->where('created_at', '<=', $dataAte);
            } catch (\Exception $e) {
                // Data inválida, ignora o filtro
            }
        }

        return $query;
    }
}
