<?php

namespace App\Domain\Learning\Content;

use Illuminate\Support\Facades\File;
use JsonException;

class ContentRepository
{
    /**
     * @return array<string, mixed>
     */
    public function path(string $pathKey = 'data-analyst'): array
    {
        MachineKey::assert($pathKey, 'path key');

        $path = $this->readJson($this->contentRoot($pathKey).'/path.json', "path [{$pathKey}]");

        if (($path['schema_version'] ?? null) !== 1
            || ($path['key'] ?? null) !== $pathKey
            || ($path['type'] ?? null) !== 'path'
            || ! is_string($path['title'] ?? null)
            || ! is_array($path['module_keys'] ?? null)
        ) {
            throw new ContentValidationException("Invalid path metadata [{$pathKey}].");
        }

        foreach ($path['module_keys'] as $moduleKey) {
            MachineKey::assert((string) $moduleKey, 'module key');
        }

        return $path;
    }

    /**
     * @return array<string, mixed>
     */
    public function module(string $moduleKey, string $pathKey = 'data-analyst'): array
    {
        MachineKey::assert($moduleKey, 'module key');
        $path = $this->path($pathKey);

        if (! in_array($moduleKey, $path['module_keys'], true)) {
            throw new ContentValidationException("Module [{$moduleKey}] is not registered in path [{$pathKey}].");
        }

        $module = $this->readJson(
            $this->contentRoot($pathKey).'/'.$moduleKey.'/module.json',
            "module [{$moduleKey}]",
        );

        if (($module['schema_version'] ?? null) !== 1
            || ($module['key'] ?? null) !== $moduleKey
            || ($module['type'] ?? null) !== 'module'
            || ! is_string($module['title'] ?? null)
            || ! is_int($module['order'] ?? null)
            || ! is_array($module['topic_keys'] ?? null)
            || ! is_array($module['topics'] ?? null)
        ) {
            throw new ContentValidationException("Invalid module metadata [{$moduleKey}].");
        }

        foreach ($module['topic_keys'] as $topicKey) {
            MachineKey::assert((string) $topicKey, 'topic key');

            if (! isset($module['topics'][$topicKey]) || ! is_array($module['topics'][$topicKey])) {
                throw new ContentValidationException("Topic [{$topicKey}] is missing from module [{$moduleKey}].");
            }

            $topic = $module['topics'][$topicKey];

            if (($topic['title'] ?? null) === null
                || ! is_string($topic['title'])
                || ! is_int($topic['order'] ?? null)
                || ! is_string($topic['lesson_file'] ?? null)
                || ! is_string($topic['exercise_file'] ?? null)
                || str_contains($topic['lesson_file'], '..')
                || str_contains($topic['exercise_file'], '..')
                || str_starts_with($topic['lesson_file'], '/')
                || str_starts_with($topic['exercise_file'], '/')
            ) {
                throw new ContentValidationException("Invalid topic metadata [{$topicKey}] in module [{$moduleKey}].");
            }

            $moduleRoot = $this->contentRoot($pathKey).'/'.$moduleKey;

            foreach ([$topic['lesson_file'], $topic['exercise_file']] as $relativeFile) {
                if (! File::exists($moduleRoot.'/'.$relativeFile)) {
                    throw new ContentValidationException("Missing content file [{$relativeFile}] for [{$moduleKey}/{$topicKey}].");
                }
            }
        }

        return $module;
    }

    public function lesson(string $key, string $pathKey = 'data-analyst'): LessonSource
    {
        $parts = $this->assertLessonKey($key);
        [$moduleKey, $topicKey] = $parts;
        $module = $this->module($moduleKey, $pathKey);
        $topic = $module['topics'][$topicKey] ?? null;

        if (! is_array($topic)) {
            throw new ContentValidationException("Topic [{$topicKey}] is not registered in module [{$moduleKey}].");
        }

        $lessonPath = $this->contentRoot($pathKey).'/'.$moduleKey.'/'.$topic['lesson_file'];
        $exercisePath = $this->contentRoot($pathKey).'/'.$moduleKey.'/'.$topic['exercise_file'];

        if (! File::exists($lessonPath)) {
            throw new ContentValidationException("Missing lesson file for [{$key}].");
        }

        $markdown = File::get($lessonPath);
        $exercises = [];

        try {
            $decoded = json_decode(File::get($exercisePath), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new ContentValidationException("Invalid exercise JSON for [{$key}]: {$exception->getMessage()}", previous: $exception);
        }

        $exercises = $this->normalizeExercises($decoded, $key);

        $sourceHash = hash('sha256', $markdown.'\n'.json_encode($exercises, JSON_THROW_ON_ERROR).'\n'.json_encode($topic, JSON_THROW_ON_ERROR));

        return new LessonSource($key, $markdown, $exercises, $sourceHash, [
            'path_key' => $pathKey,
            'module_key' => $moduleKey,
            'topic_key' => $topicKey,
            'title' => $topic['title'],
            'order' => $topic['order'],
        ]);
    }

    /**
     * @return list<string>
     */
    public function lessonKeys(string $pathKey = 'data-analyst'): array
    {
        $path = $this->path($pathKey);
        $keys = [];

        foreach ($path['module_keys'] as $moduleKey) {
            $module = $this->module($moduleKey, $pathKey);
            foreach ($module['topic_keys'] as $topicKey) {
                $this->lesson($moduleKey.'/'.$topicKey, $pathKey);
                $keys[] = $moduleKey.'/'.$topicKey;
            }
        }

        return $keys;
    }

    /**
     * Validate all canonical metadata, lesson sources, and exercise references.
     *
     * @return list<string>
     */
    public function validate(string $pathKey = 'data-analyst'): array
    {
        $this->path($pathKey);
        $keys = $this->lessonKeys($pathKey);

        if ($keys === []) {
            throw new ContentValidationException("No lessons were registered in path [{$pathKey}].");
        }

        return $keys;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function normalizeExercises(mixed $decoded, string $lessonKey): array
    {
        if (! is_array($decoded)
            || ($decoded['schema_version'] ?? null) !== 1
            || ! isset($decoded['exercises'])
            || ! is_array($decoded['exercises'])
        ) {
            throw new ContentValidationException("Exercise config for [{$lessonKey}] must contain an exercises array.");
        }

        $exercises = [];

        foreach ($decoded['exercises'] as $exercise) {
            if (! is_array($exercise)
                || ! is_string($exercise['id'] ?? null)
                || ! is_string($exercise['type'] ?? null)
                || ! is_string($exercise['prompt'] ?? null)
                || ! is_array($exercise['options'] ?? null)
                || $exercise['options'] === []
            ) {
                throw new ContentValidationException("Every exercise in [{$lessonKey}] needs a string id and type.");
            }

            if (! in_array($exercise['type'], ['multiple_choice'], true)
                || trim($exercise['prompt']) === ''
                || count(array_filter($exercise['options'], 'is_string')) !== count($exercise['options'])
                || (isset($exercise['correct_option']) && ! is_int($exercise['correct_option']))
                || (isset($exercise['correct_option'])
                    && ($exercise['correct_option'] < 0 || $exercise['correct_option'] >= count($exercise['options'])))
            ) {
                throw new ContentValidationException("Invalid exercise configuration for [{$lessonKey}].");
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

    /** @return array{0: string, 1: string} */
    private function assertLessonKey(string $key): array
    {
        $parts = explode('/', $key);

        if (count($parts) !== 2) {
            throw new ContentValidationException("Unsafe or invalid lesson key [{$key}].");
        }

        foreach ($parts as $part) {
            MachineKey::assert($part, 'lesson key segment');
        }

        return [$parts[0], $parts[1]];
    }

    private function contentRoot(string $pathKey): string
    {
        MachineKey::assert($pathKey, 'path key');

        $root = rtrim((string) config('belajardata.content_path'), '/\\').'/'.$pathKey;

        if (! File::isDirectory($root)) {
            throw new ContentValidationException("The content path [{$pathKey}] does not exist.");
        }

        return $root;
    }

    /** @return array<string, mixed> */
    private function readJson(string $path, string $label): array
    {
        if (! File::exists($path)) {
            throw new ContentValidationException("Missing {$label} metadata at [{$path}].");
        }

        try {
            $decoded = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new ContentValidationException("Invalid {$label} JSON: {$exception->getMessage()}", previous: $exception);
        }

        if (! is_array($decoded)) {
            throw new ContentValidationException("The {$label} must decode to an object.");
        }

        return $decoded;
    }
}
