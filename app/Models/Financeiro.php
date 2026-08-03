<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Financeiro extends Model
{
    use HasFactory;

    protected $table = 'financeiros';

    public const STATUS_PAGO = 'pago';
    public const STATUS_NAO_PAGO = 'nao_pago';
    public const STATUS_EM_DIA = 'em_dia';
    public const STATUS_VENCIDO = 'vencido';

    public const STATUS_OPTIONS = [
        self::STATUS_PAGO => 'Pago',
        self::STATUS_NAO_PAGO => 'Não Pago',
        self::STATUS_EM_DIA => 'Em Dia',
        self::STATUS_VENCIDO => 'Vencido',
    ];

    protected $fillable = [
        'cliente_id',
        'valor_causa',
        'honorarios',
        'reembolso',
        'data_pagamento',
        'status_pagamento',
        'parcelado',
        'numero_parcelas',
        'valor_parcela',
            'valor_pago',
        'data_primeira_parcela',
        'observacoes',
    ];

    protected $casts = [
        'valor_causa' => 'decimal:2',
        'honorarios' => 'decimal:2',
        'reembolso' => 'decimal:2',
        'data_pagamento' => 'date',
        'parcelado' => 'boolean',
        'numero_parcelas' => 'integer',
        'valor_parcela' => 'decimal:2',
            'valor_pago' => 'decimal:2',
        'data_primeira_parcela' => 'date',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function processos()
    {
        return $this->belongsToMany(Processo::class, 'financeiro_processo', 'financeiro_id', 'processo_id')
            ->withTimestamps();
    }

    public function parcelas()
    {
        return $this->hasMany(FinanceiroParcela::class, 'financeiro_id')->orderBy('numero');
    }

    public function getStatusPagamentoLabelAttribute(): string
    {
        return self::STATUS_OPTIONS[$this->status_pagamento] ?? $this->status_pagamento;
    }

    public function computeStatus(): string
    {
        // If parcelado, consider parcelas
        if ($this->parcelado) {
            $parcelas = $this->parcelas()->get();

            if ($parcelas->isEmpty()) {
                return self::STATUS_NAO_PAGO;
            }

            $allPaid = $parcelas->every(fn($p) => ! empty($p->valor_pago));
            if ($allPaid) {
                return self::STATUS_PAGO;
            }

            // Overall for parcelado: if some parcela not paid -> Não Pago
            return self::STATUS_NAO_PAGO;
        }

        // Not parcelado: if there's a data_pagamento or valor_pago, consider quitado
        if (! empty($this->valor_pago)) {
            return self::STATUS_PAGO;
        }

        // For non-parcelado unpaid, consider data_pagamento as due date
        if ($this->data_pagamento) {
            $today = now()->startOfDay();
            $due = $this->data_pagamento->startOfDay();
            if ($due->lt($today)) {
                return self::STATUS_VENCIDO;
            }

            return self::STATUS_EM_DIA;
        }

        return self::STATUS_NAO_PAGO;
    }

    public function getComputedStatusAttribute(): string
    {
        return $this->computeStatus();
    }

    public function getComputedStatusLabelAttribute(): string
    {
        return self::STATUS_OPTIONS[$this->computed_status] ?? $this->computed_status;
    }
}
