<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Financeiro;
use Illuminate\Support\Facades\Auth;

class FinanceiroController extends Controller
{
    public function index()
    {
        $cliente = Auth::guard('cliente')->user();

        $lancamentos = Financeiro::query()
            ->where('cliente_id', $cliente->id)
            ->with('parcelas')
            ->orderByDesc('id')
            ->get();

        return view('portal.financeiro', [
            'lancamentos' => $lancamentos,
        ]);
    }
}
