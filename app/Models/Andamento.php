<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Andamento extends Model
{
    use HasFactory;

    protected $table = 'andamentos';

    protected $fillable = [
        'processo_id',
        'tipo',
        'tipo_acao_id',
        'data_andamento',
        'descricao',
        'usuario_id',
        'created_by',
    ];

    protected $casts = [
        'data_andamento' => 'date',
    ];

    public function processo()
    {
        return $this->belongsTo(Processo::class, 'processo_id');
    }

    public function tipoAcao()
    {
        return $this->belongsTo(TipoAcao::class, 'tipo_acao_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function criador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class, 'andamento_id');
    }
}
