<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Despesa extends Model
{
    use HasFactory;

    protected $table = 'despesas';

    public const STATUS_PENDENTE = 'pendente';
    public const STATUS_PAGO = 'pago';
    public const STATUS_ATRASADO = 'atrasado';

    public const STATUS_OPTIONS = [
        self::STATUS_PENDENTE => 'Pendente',
        self::STATUS_PAGO => 'Pago',
        self::STATUS_ATRASADO => 'Atrasado',
    ];

    protected $fillable = [
        'descricao',
        'categoria',
        'valor',
        'valor_pago',
        'data_vencimento',
        'data_pagamento',
        'status',
        'filial_id',
        'observacoes',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'valor_pago' => 'decimal:2',
        'data_vencimento' => 'date',
        'data_pagamento' => 'date',
    ];

    public function filial()
    {
        return $this->belongsTo(Filial::class, 'filial_id');
    }

    public function computeStatus(): string
    {
        if (! empty($this->valor_pago) || $this->data_pagamento) {
            return self::STATUS_PAGO;
        }

        if ($this->data_vencimento && $this->data_vencimento->startOfDay()->lt(now()->startOfDay())) {
            return self::STATUS_ATRASADO;
        }

        return self::STATUS_PENDENTE;
    }

    public function getComputedStatusAttribute(): string
    {
        return $this->computeStatus();
    }

    public function getComputedStatusLabelAttribute(): string
    {
        return self::STATUS_OPTIONS[$this->computed_status] ?? $this->computed_status;
    }

    public function scopeAtrasadas($query)
    {
        return $query->whereNull('data_pagamento')
            ->where('data_vencimento', '<', now()->startOfDay());
    }

    /**
     * Soma das despesas em atraso (não pagas e com vencimento no passado).
     * Reaproveitado pelo Dashboard (indicador financeiro) e pelo relatório de inadimplência.
     */
    public static function totalEmAtraso(): float
    {
        return (float) self::atrasadas()->sum('valor');
    }
}
