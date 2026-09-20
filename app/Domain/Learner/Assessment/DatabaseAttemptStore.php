<?php

namespace App\Domain\Learner\Assessment;

use App\Models\LearningAttempt;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class DatabaseAttemptStore implements AttemptStore
{
    public function record(User $user, string $exerciseKey, ExerciseValidationResult $result): LearningAttempt
    {
        return DB::transaction(function () use ($user, $exerciseKey, $result): LearningAttempt {
            $attempt = LearningAttempt::query()->firstOrNew([
                'user_id' => $user->id,
                'exercise_key' => $exerciseKey,
            ]);
            $now = now();

            $attempt->answer_json = $result->answer;
            $attempt->score = $result->score;
            $attempt->status = $attempt->status === 'completed' ? 'completed' : $result->status;
            $attempt->attempts_count = (int) $attempt->attempts_count + 1;
            $attempt->last_attempt_at = $now;
            $attempt->completed_at ??= $result->completed ? $now : null;
            $attempt->save();

            return $attempt;
        });
    }

    public function find(User $user, string $exerciseKey): ?LearningAttempt
    {
        return $user->attempts()->where('exercise_key', $exerciseKey)->first();
    }

    public function reset(User $user, string $exerciseKey): void
    {
        $user->attempts()->where('exercise_key', $exerciseKey)->delete();
    }
}
