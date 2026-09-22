<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Processo;
use Illuminate\Support\Facades\Auth;

class ProcessosController extends Controller
{
    public function index()
    {
        $cliente = Auth::guard('cliente')->user();

        $processos = $cliente->processos()
            ->with('andamentos')
            ->orderByDesc('processos.id')
            ->get();

        return view('portal.processos', [
            'processos' => $processos,
        ]);
    }

    public function show(int $id)
    {
        $cliente = Auth::guard('cliente')->user();

        $processo = $cliente->processos()
            ->with(['andamentos' => fn ($q) => $q->orderByDesc('data_andamento')])
            ->where('processos.id', $id)
            ->firstOrFail();

        return view('portal.processo-detalhe', [
            'processo' => $processo,
        ]);
    }
}
