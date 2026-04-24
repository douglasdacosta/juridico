<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use HasFactory;

    protected $table = 'documentos';

    protected $fillable = [
        'nome_original',
        'nome_armazenado',
        'tipo_midia',
        'tamanho',
        'caminho',
        'cliente_id',
        'processo_id',
        'andamento_id',
        'version_group_id',
        'versao',
        'shared_with_client',
        'usuario_id',
        'ativo',
    ];

    protected $casts = [
        'shared_with_client' => 'boolean',
        'ativo' => 'boolean',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function processo()
    {
        return $this->belongsTo(Processo::class, 'processo_id');
    }

    public function andamento()
    {
        return $this->belongsTo(Andamento::class, 'andamento_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
