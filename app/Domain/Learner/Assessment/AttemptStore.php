<?php

namespace App\Domain\Learner\Assessment;

use App\Models\LearningAttempt;
use App\Models\User;

interface AttemptStore
{
    public function record(User $user, string $exerciseKey, ExerciseValidationResult $result): LearningAttempt;

    public function find(User $user, string $exerciseKey): ?LearningAttempt;

    public function reset(User $user, string $exerciseKey): void;
}
