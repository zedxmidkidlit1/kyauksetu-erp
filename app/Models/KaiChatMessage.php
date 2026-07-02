<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'kai_chat_session_id',
    'user_id',
    'role',
    'content',
    'context_keys',
    'driver',
    'provider',
    'model',
    'status',
    'error_code',
    'metadata',
])]
class KaiChatMessage extends Model
{
    protected $attributes = [
        'status' => 'completed',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(KaiChatSession::class, 'kai_chat_session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'context_keys' => 'array',
            'metadata' => 'array',
        ];
    }
}
