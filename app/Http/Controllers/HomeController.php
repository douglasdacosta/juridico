<?php

namespace App\Http\Controllers;

use App\Models\Andamento;
use App\Models\Cliente;
use App\Models\Compromisso;
use App\Models\Despesa;
use App\Models\Documento;
use App\Models\Financeiro;
use App\Models\FinanceiroParcela;
use App\Models\Processo;
use App\Services\FluxoCaixaCalculator;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $query = Processo::query()->with(['clientes', 'responsavel']);

        if ($request->filled('cliente_id')) {
            $clienteId = (int) $request->input('cliente_id');
            $query->whereHas('clientes', fn ($q) => $q->where('clientes.id', $clienteId));
        }

        if ($request->filled('numero_processo')) {
            $query->where('numero_processo', 'like', '%' . trim((string) $request->input('numero_processo')) . '%');
        }

        $fluxoMensal = FluxoCaixaCalculator::calcular(6);

        return view('home', [
            'kpis' => [
                'total_processos' => Processo::query()->count(),
                'processos_ativos' => Processo::query()->where('status', 'ativo')->count(),
                'processos_encerrados' => Processo::query()->where('status', 'encerrado')->count(),
                'total_clientes' => Cliente::query()->count(),
                'total_andamentos' => Andamento::query()->count(),
                'total_documentos' => Documento::query()->count(),
            ],
            'kpisFinanceiros' => [
                'a_receber_pendente' => $this->aReceberPendente(),
                'recebido_mes' => $this->recebidoNoMes(),
                'total_receber_atraso' => Financeiro::totalEmAtraso(),
                'total_pagar_atraso' => Despesa::totalEmAtraso(),
            ],
            'processosPorStatus' => Processo::query()
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
            'proximosCompromissos' => Compromisso::proximos()->with('processo')->limit(5)->get(),
            'fluxoMensal' => $fluxoMensal,
            'clientesOptions' => Cliente::query()->orderBy('nome')->pluck('nome', 'id'),
            'processos' => $query->orderByDesc('id')->limit(30)->get(),
            'request' => $request,
        ]);
    }

    private function recebidoNoMes(): float
    {
        $inicio = now()->startOfMonth();
        $fim = now()->endOfMonth();

        $avista = Financeiro::query()
            ->where('parcelado', false)
            ->whereNotNull('valor_pago')
            ->whereBetween('data_pagamento', [$inicio, $fim])
            ->sum('valor_pago');

        $parcelas = FinanceiroParcela::query()
            ->whereNotNull('data_pagamento')
            ->whereBetween('data_pagamento', [$inicio, $fim])
            ->sum('valor_pago');

        return (float) $avista + (float) $parcelas;
    }

    private function aReceberPendente(): float
    {
        $avista = Financeiro::query()
            ->where('parcelado', false)
            ->whereNull('valor_pago')
            ->sum('valor_causa');

        $parcelas = FinanceiroParcela::query()
            ->whereNull('data_pagamento')
            ->sum('valor');

        return (float) $avista + (float) $parcelas;
    }
}
