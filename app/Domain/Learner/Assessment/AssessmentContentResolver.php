<?php

namespace App\Domain\Learner\Assessment;

use App\Domain\Learning\Content\ContentRepository;
use App\Domain\Learning\Content\ContentValidationException;
use App\Domain\Learning\Content\LessonSource;

final class AssessmentContentResolver
{
    public function __construct(private readonly ContentRepository $content) {}

    /** @return array{source: LessonSource, exercise: array<string, mixed>} */
    public function resolve(string $exerciseKey): array
    {
        $parts = explode('/', $exerciseKey);

        if (count($parts) !== 4 || ! preg_match('/^[a-z0-9][a-z0-9-]*$/', $parts[0]) || ! preg_match('/^[a-z0-9][a-z0-9-]*$/', $parts[1]) || ! preg_match('/^[a-z0-9][a-z0-9-]*$/', $parts[2]) || ! preg_match('/^[a-z0-9][a-z0-9-]*$/', $parts[3])) {
            throw new ContentValidationException('Exercise key tidak valid.');
        }

        [$pathKey, $moduleKey, $topicKey, $exerciseId] = $parts;
        $source = $topicKey === 'challenge'
            ? $this->content->challenge($moduleKey, $pathKey)
            : $this->content->lesson($moduleKey.'/'.$topicKey, $pathKey);
        $exercise = $source->exercises[$exerciseId] ?? null;

        if (! is_array($exercise)) {
            throw new ContentValidationException('Exercise tidak terdaftar.');
        }

        return ['source' => $source, 'exercise' => $exercise];
    }
}
