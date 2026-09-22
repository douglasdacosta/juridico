<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Compromisso;
use App\Models\Documento;
use App\Models\FinanceiroParcela;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $cliente = Auth::guard('cliente')->user();
        $processoIds = $cliente->processos()->pluck('processos.id');

        return view('portal.dashboard', [
            'cliente' => $cliente,
            'totalProcessos' => $processoIds->count(),
            'totalDocumentos' => Documento::query()
                ->whereIn('processo_id', $processoIds)
                ->where('shared_with_client', true)
                ->where('ativo', true)
                ->count(),
            'parcelasPendentes' => FinanceiroParcela::query()
                ->whereHas('financeiro', fn ($q) => $q->where('cliente_id', $cliente->id))
                ->whereNull('data_pagamento')
                ->count(),
            'proximosCompromissos' => Compromisso::query()
                ->whereIn('processo_id', $processoIds)
                ->where('status', Compromisso::STATUS_PENDENTE)
                ->where('data_hora', '>=', now())
                ->orderBy('data_hora')
                ->limit(3)
                ->get(),
        ]);
    }
}
