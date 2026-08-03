<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceiroParcela extends Model
{
    use HasFactory;

    protected $table = 'financeiro_parcelas';

    protected $fillable = [
        'financeiro_id',
        'numero',
        'valor',
        'valor_pago',
        'data_vencimento',
        'data_pagamento',
        'status',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'valor_pago' => 'decimal:2',
        'data_vencimento' => 'date',
        'data_pagamento' => 'date',
    ];

    public function financeiro()
    {
        return $this->belongsTo(Financeiro::class, 'financeiro_id');
    }
}
