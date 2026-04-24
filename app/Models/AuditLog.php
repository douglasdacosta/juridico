<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id',
        'entity',
        'entity_id',
        'action',
        'before',
        'after',
        'ip',
        'user_agent',
        'method',
        'uri',
        'status_code',
    ];

    protected $casts = [
        'before' => 'array',
        'after' => 'array',
    ];
}
