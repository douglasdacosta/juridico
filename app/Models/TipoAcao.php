<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoAcao extends Model
{
    use HasFactory;

    protected $table = 'tipos_acao';

    protected $fillable = [
        'nome',
        'descricao',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    /**
     * Andamentos que usam este tipo
     */
    public function andamentos()
    {
        return $this->hasMany(Andamento::class, 'tipo_acao_id');
    }

    /**
     * Scope para tipos ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }
}
