<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'exercise_key',
        'answer_json',
        'score',
        'status',
        'attempts_count',
        'last_attempt_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'answer_json' => 'array',
            'score' => 'float',
            'last_attempt_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return array{exercise_key: string, answer: array|null, score: float|null, status: string, attempts_count: int, last_attempt_at: string|null, completed_at: string|null}
     */
    public function toAttemptArray(): array
    {
        return [
            'exercise_key' => $this->exercise_key,
            'answer' => $this->answer_json,
            'score' => $this->score,
            'status' => $this->status,
            'attempts_count' => $this->attempts_count,
            'last_attempt_at' => $this->last_attempt_at?->toISOString(),
            'completed_at' => $this->completed_at?->toISOString(),
        ];
    }
}
