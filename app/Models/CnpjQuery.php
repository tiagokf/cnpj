<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CnpjQuery extends Model
{
    public $timestamps = false;

    protected $table = 'cnpj_queries';

    protected $fillable = [
        'cnpj',
        'razao_social',
        'source',
        'success',
        'error_message',
        'response_time_ms',
        'ip_address',
        'user_id',
        'queried_at',
    ];

    protected function casts(): array
    {
        return [
            'success' => 'boolean',
            'response_time_ms' => 'integer',
            'queried_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
