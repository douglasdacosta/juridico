<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Processo extends Model
{
    use HasFactory;

    protected $table = 'processos';

    protected $fillable = [
        'numero_processo',
        'vara_tribunal',
        'tipo_acao',
        'data_abertura',
        'data_encerramento',
        'status',
        'responsavel_id',
        'observacoes',
    ];

    protected $casts = [
        'data_abertura' => 'date',
        'data_encerramento' => 'date',
    ];

    public function responsavel()
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }

    public function filiais()
    {
        return $this->belongsToMany(Filial::class, 'processo_filial')
            ->withTimestamps();
    }

    public function clientes()
    {
        return $this->belongsToMany(Cliente::class, 'processo_cliente', 'processo_id', 'cliente_id')
            ->withPivot('papel_cliente')
            ->withTimestamps();
    }

    public function andamentos()
    {
        return $this->hasMany(Andamento::class, 'processo_id');
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class, 'processo_id');
    }

    public function tipoAcao()
    {
        return $this->belongsTo(TipoAcao::class, 'tipo_acao');
    }
}
