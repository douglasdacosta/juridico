<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModeloDocumento extends Model
{
    use HasFactory;

    protected $table = 'modelos_documento';

    public const TIPO_CONTRATO = 'contrato';
    public const TIPO_PETICAO = 'peticao';
    public const TIPO_OUTRO = 'outro';

    public const TIPO_OPTIONS = [
        self::TIPO_CONTRATO => 'Contrato',
        self::TIPO_PETICAO => 'Petição',
        self::TIPO_OUTRO => 'Outro',
    ];

    protected $fillable = [
        'nome',
        'tipo',
        'corpo',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function documentosGerados()
    {
        return $this->hasMany(Documento::class, 'modelo_documento_id');
    }

    public function getTipoLabelAttribute(): string
    {
        return self::TIPO_OPTIONS[$this->tipo] ?? $this->tipo;
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true)->orderBy('nome');
    }
}
