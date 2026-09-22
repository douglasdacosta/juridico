<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compromisso extends Model
{
    use HasFactory;

    protected $table = 'compromissos';

    public const STATUS_PENDENTE = 'pendente';
    public const STATUS_CONCLUIDO = 'concluido';
    public const STATUS_CANCELADO = 'cancelado';

    public const STATUS_OPTIONS = [
        self::STATUS_PENDENTE => 'Pendente',
        self::STATUS_CONCLUIDO => 'Concluído',
        self::STATUS_CANCELADO => 'Cancelado',
    ];

    public const TIPO_AUDIENCIA = 'audiencia';
    public const TIPO_PRAZO_FATAL = 'prazo_fatal';
    public const TIPO_REUNIAO = 'reuniao';
    public const TIPO_OUTRO = 'outro';

    public const TIPO_OPTIONS = [
        self::TIPO_AUDIENCIA => 'Audiência',
        self::TIPO_PRAZO_FATAL => 'Prazo Fatal',
        self::TIPO_REUNIAO => 'Reunião',
        self::TIPO_OUTRO => 'Outro',
    ];

    protected $fillable = [
        'titulo',
        'tipo',
        'data_hora',
        'processo_id',
        'responsavel_id',
        'created_by',
        'status',
        'observacoes',
    ];

    protected $casts = [
        'data_hora' => 'datetime',
    ];

    public function processo()
    {
        return $this->belongsTo(Processo::class, 'processo_id');
    }

    public function responsavel()
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }

    public function criador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTipoLabelAttribute(): string
    {
        return self::TIPO_OPTIONS[$this->tipo] ?? $this->tipo;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_OPTIONS[$this->status] ?? $this->status;
    }

    public function scopeProximos($query)
    {
        return $query->where('status', self::STATUS_PENDENTE)
            ->where('data_hora', '>=', now())
            ->orderBy('data_hora');
    }

    public function scopeVencidos($query)
    {
        return $query->where('status', self::STATUS_PENDENTE)
            ->where('data_hora', '<', now())
            ->orderBy('data_hora');
    }
}
