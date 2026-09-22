<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Além de ser o cadastro interno de clientes, este model também autentica o
 * Portal do Cliente (guard "cliente", ver config/auth.php) via CPF + senha.
 * "password" é deliberadamente omitido do $fillable: só é gravado através de
 * ClientesController::definirAcessoPortal, nunca pelo formulário de cadastro.
 */
class Cliente extends Authenticatable
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

    protected $hidden = [
        'password',
        'remember_token',
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

    public function financeiros()
    {
        return $this->hasMany(Financeiro::class, 'cliente_id');
    }
}
