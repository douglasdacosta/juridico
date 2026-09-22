<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Compromisso;
use Illuminate\Support\Facades\Auth;

class AgendaController extends Controller
{
    public function index()
    {
        $cliente = Auth::guard('cliente')->user();
        $processoIds = $cliente->processos()->pluck('processos.id');

        $audiencias = Compromisso::query()
            ->with('processo')
            ->whereIn('processo_id', $processoIds)
            ->where('tipo', Compromisso::TIPO_AUDIENCIA)
            ->orderBy('data_hora')
            ->get();

        return view('portal.agenda', [
            'audiencias' => $audiencias,
        ]);
    }
}
