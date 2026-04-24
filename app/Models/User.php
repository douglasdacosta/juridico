<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const PERFIL_ADMIN = 1;
    public const PERFIL_CLIENTE = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'chave_pix',
        'password',
        'perfil_acesso',
        'status',
        'failed_attempts',
        'locked_until',
        'two_factor_enabled',
        'two_factor_secret',
        'lgpd_consent_at',
        'lgpd_purpose',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'locked_until' => 'datetime',
        'two_factor_enabled' => 'boolean',
        'lgpd_consent_at' => 'datetime',
    ];

    public function isAdmin(): bool
    {
        return (int) $this->perfil_acesso === self::PERFIL_ADMIN;
    }

    public function isCliente(): bool
    {
        return (int) $this->perfil_acesso === self::PERFIL_CLIENTE;
    }

    public function perfil()
    {
        return $this->belongsTo(Perfis::class, 'perfil_acesso', 'id');
    }

    public function processosResponsavel()
    {
        return $this->hasMany(Processo::class, 'responsavel_id');
    }

    public function clientesResponsaveis()
    {
        return $this->belongsToMany(Cliente::class, 'cliente_responsavel', 'user_id', 'cliente_id')
            ->withPivot('papel')
            ->withTimestamps();
    }

    public function andamentosRegistrados()
    {
        return $this->hasMany(Andamento::class, 'usuario_id');
    }

    public function andamentosCriados()
    {
        return $this->hasMany(Andamento::class, 'created_by');
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class, 'usuario_id');
    }
}
