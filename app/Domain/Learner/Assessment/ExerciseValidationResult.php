<?php

namespace App\Domain\Learner\Assessment;

final class ExerciseValidationResult
{
    public function __construct(
        public readonly bool $valid,
        public readonly bool $completed,
        public readonly string $status,
        public readonly ?float $score,
        public readonly string $feedback,
        public readonly array $answer,
    ) {}

    /** @return array{valid: bool, completed: bool, status: string, score: float|null, feedback: string, answer: array} */
    public function toArray(): array
    {
        return [
            'valid' => $this->valid,
            'completed' => $this->completed,
            'status' => $this->status,
            'score' => $this->score,
            'feedback' => $this->feedback,
            'answer' => $this->answer,
        ];
    }
}
