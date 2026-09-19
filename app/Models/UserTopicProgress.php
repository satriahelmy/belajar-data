<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTopicProgress extends Model
{
    protected $table = 'user_topic_progress';

    protected $fillable = [
        'user_id',
        'content_key',
        'status',
        'started_at',
        'completed_at',
        'last_activity_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'last_activity_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return array{content_key: string, status: string, started_at: string|null, completed_at: string|null, last_activity_at: string|null}
     */
    public function toProgressArray(): array
    {
        return [
            'content_key' => $this->content_key,
            'status' => $this->status,
            'started_at' => $this->started_at?->toISOString(),
            'completed_at' => $this->completed_at?->toISOString(),
            'last_activity_at' => $this->last_activity_at?->toISOString(),
        ];
    }
}
