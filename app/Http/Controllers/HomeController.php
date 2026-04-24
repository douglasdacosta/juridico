<?php

namespace App\Http\Controllers;

use App\Models\Andamento;
use App\Models\Cliente;
use App\Models\Documento;
use App\Models\Processo;
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

        return view('home', [
            'kpis' => [
                'total_processos' => Processo::query()->count(),
                'processos_ativos' => Processo::query()->where('status', 'ativo')->count(),
                'processos_encerrados' => Processo::query()->where('status', 'encerrado')->count(),
                'total_clientes' => Cliente::query()->count(),
                'total_andamentos' => Andamento::query()->count(),
                'total_documentos' => Documento::query()->count(),
            ],
            'clientesOptions' => Cliente::query()->orderBy('nome')->pluck('nome', 'id'),
            'processos' => $query->orderByDesc('id')->limit(30)->get(),
            'request' => $request,
        ]);
    }
}
