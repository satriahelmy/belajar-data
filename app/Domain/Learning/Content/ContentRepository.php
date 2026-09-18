<?php

namespace App\Domain\Learning\Content;

use Illuminate\Support\Facades\File;
use JsonException;

class ContentRepository
{
    public function lesson(string $key): LessonSource
    {
        $this->assertSafeKey($key);

        $directory = base_path('content/data-analyst/'.$key);
        $lessonPath = $directory.'/lesson.md';
        $exercisePath = $directory.'/exercises.json';

        if (! File::exists($lessonPath)) {
            throw new ContentValidationException("Missing lesson file for [{$key}].");
        }

        $markdown = File::get($lessonPath);
        $exercises = [];

        if (File::exists($exercisePath)) {
            try {
                $decoded = json_decode(File::get($exercisePath), true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $exception) {
                throw new ContentValidationException("Invalid exercise JSON for [{$key}]: {$exception->getMessage()}", previous: $exception);
            }

            $exercises = $this->normalizeExercises($decoded, $key);
        }

        $sourceHash = hash('sha256', $markdown.'\n'.json_encode($exercises, JSON_THROW_ON_ERROR));

        return new LessonSource($key, $markdown, $exercises, $sourceHash);
    }

    /**
     * @return list<string>
     */
    public function lessonKeys(): array
    {
        $root = base_path('content/data-analyst');

        if (! File::isDirectory($root)) {
            throw new ContentValidationException('The content/data-analyst directory does not exist.');
        }

        return collect(File::allFiles($root))
            ->filter(fn ($file) => $file->getFilename() === 'lesson.md')
            ->map(function ($file) use ($root): string {
                $directory = str_replace('\\', '/', $file->getPath());
                $relative = ltrim(str_replace(str_replace('\\', '/', $root), '', $directory), '/');

                return $relative;
            })
            ->values()
            ->all();
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function normalizeExercises(mixed $decoded, string $lessonKey): array
    {
        if (! is_array($decoded) || ! isset($decoded['exercises']) || ! is_array($decoded['exercises'])) {
            throw new ContentValidationException("Exercise config for [{$lessonKey}] must contain an exercises array.");
        }

        $exercises = [];

        foreach ($decoded['exercises'] as $exercise) {
            if (! is_array($exercise) || ! is_string($exercise['id'] ?? null) || ! is_string($exercise['type'] ?? null)) {
                throw new ContentValidationException("Every exercise in [{$lessonKey}] needs a string id and type.");
            }

            $id = $exercise['id'];

            if (! preg_match('/^[a-z0-9][a-z0-9-]*$/', $id)) {
                throw new ContentValidationException("Exercise id [{$id}] in [{$lessonKey}] is invalid.");
            }

            if (isset($exercises[$id])) {
                throw new ContentValidationException("Duplicate exercise id [{$id}] in [{$lessonKey}].");
            }

            $exercises[$id] = $exercise;
        }

        return $exercises;
    }

    private function assertSafeKey(string $key): void
    {
        if (! preg_match('/^[a-z0-9][a-z0-9-]*(?:\/[a-z0-9][a-z0-9-]*)*$/', $key)) {
            throw new ContentValidationException("Unsafe or invalid lesson key [{$key}].");
        }
    }
}
