<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'nome',
        'tipo_pessoa',
        'cpf',
        'cnpj',
        'socios',
        'email',
        'endereco',
        'numero',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'telefone',
        'status',
        'ativo',
        'lgpd_consent_at',
        'lgpd_purpose',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'lgpd_consent_at' => 'datetime',
        'socios' => 'array',
    ];

    public function responsaveis()
    {
        return $this->belongsToMany(User::class, 'cliente_responsavel', 'cliente_id', 'user_id')
            ->withPivot('papel')
            ->withTimestamps();
    }

    public function processos()
    {
        return $this->belongsToMany(Processo::class, 'processo_cliente', 'cliente_id', 'processo_id')
            ->withPivot('papel_cliente')
            ->withTimestamps();
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class, 'cliente_id');
    }
}
