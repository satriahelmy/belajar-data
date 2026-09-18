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

        if (count($path['module_keys']) !== count(array_unique($path['module_keys']))) {
            throw new ContentValidationException("Duplicate module keys in path [{$pathKey}].");
        }

        if (isset($path['phases'])) {
            $this->validatePhases($path['phases'], $path['module_keys'], $pathKey);
        }

        if (isset($path['skills'])) {
            $this->validateSkills($path['skills'], $path['module_keys'], $pathKey);
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
            || ! in_array($module['status'] ?? null, ['published', 'roadmap'], true)
            || ! is_string($module['purpose'] ?? null)
            || ! is_string($module['outcome'] ?? null)
            || ! is_int($module['estimated_minutes'] ?? null)
            || ! is_array($module['recommended_knowledge'] ?? null)
            || ! is_array($module['skill_tags'] ?? null)
            || ! is_array($module['topic_keys'] ?? null)
            || ! is_array($module['topics'] ?? null)
        ) {
            throw new ContentValidationException("Invalid module metadata [{$moduleKey}].");
        }

        $lastTopicOrder = 0;

        foreach ($module['topic_keys'] as $topicKey) {
            MachineKey::assert((string) $topicKey, 'topic key');

            if (! isset($module['topics'][$topicKey]) || ! is_array($module['topics'][$topicKey])) {
                throw new ContentValidationException("Topic [{$topicKey}] is missing from module [{$moduleKey}].");
            }

            $topic = $module['topics'][$topicKey];

            if ($topic['order'] <= $lastTopicOrder) {
                throw new ContentValidationException("Topic order is not ascending in module [{$moduleKey}].");
            }

            $lastTopicOrder = $topic['order'];

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

            if (isset($topic['skill_tags']) && ! is_array($topic['skill_tags'])) {
                throw new ContentValidationException("Invalid skill tags for topic [{$topicKey}] in module [{$moduleKey}].");
            }

            if (isset($topic['further_reading']) && ! is_array($topic['further_reading'])) {
                throw new ContentValidationException("Invalid Further Reading for topic [{$topicKey}] in module [{$moduleKey}].");
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
     * @return list<array<string, mixed>>
     */
    public function modules(string $pathKey = 'data-analyst'): array
    {
        $path = $this->path($pathKey);
        $modules = [];

        foreach ($path['module_keys'] as $moduleKey) {
            $modules[] = $this->module($moduleKey, $pathKey);
        }

        return $modules;
    }

    /**
     * Validate all canonical metadata, lesson sources, and exercise references.
     *
     * @return list<string>
     */
    public function validate(string $pathKey = 'data-analyst'): array
    {
        $path = $this->path($pathKey);
        $modules = $this->modules($pathKey);
        $moduleOrders = array_column($modules, 'order');
        $sortedModuleOrders = $moduleOrders;
        sort($sortedModuleOrders);

        if ($moduleOrders !== $sortedModuleOrders) {
            throw new ContentValidationException("Module order is invalid in path [{$pathKey}].");
        }

        $moduleByKey = [];
        foreach ($modules as $module) {
            $moduleByKey[$module['key']] = $module;
        }

        foreach ($path['skills'] ?? [] as $skill) {
            if (isset($skill['topic_key']) && ! in_array($skill['topic_key'], $moduleByKey[$skill['module_key']]['topic_keys'], true)) {
                throw new ContentValidationException("Skill [{$skill['key']}] points to an unknown topic.");
            }
        }

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

    /**
     * @param mixed $phases
     * @param list<mixed> $moduleKeys
     */
    private function validatePhases(mixed $phases, array $moduleKeys, string $pathKey): void
    {
        if (! is_array($phases) || $phases === []) {
            throw new ContentValidationException("Path [{$pathKey}] must define at least one phase.");
        }

        $seenModules = [];
        $lastPhaseOrder = 0;

        foreach ($phases as $phase) {
            if (! is_array($phase)
                || ! is_string($phase['key'] ?? null)
                || ! is_string($phase['title'] ?? null)
                || ! is_int($phase['order'] ?? null)
                || ! is_array($phase['module_keys'] ?? null)
                || $phase['module_keys'] === []
            ) {
                throw new ContentValidationException("Invalid phase metadata in path [{$pathKey}].");
            }

            MachineKey::assert($phase['key'], 'phase key');

            if ($phase['order'] <= $lastPhaseOrder) {
                throw new ContentValidationException("Phase order is not ascending in path [{$pathKey}].");
            }

            $lastPhaseOrder = $phase['order'];

            foreach ($phase['module_keys'] as $moduleKey) {
                MachineKey::assert((string) $moduleKey, 'phase module key');

                if (! in_array($moduleKey, $moduleKeys, true) || isset($seenModules[$moduleKey])) {
                    throw new ContentValidationException("Phase module [{$moduleKey}] is not unique and canonical in path [{$pathKey}].");
                }

                $seenModules[$moduleKey] = true;
            }
        }

        if ($seenModules !== array_fill_keys($moduleKeys, true)) {
            throw new ContentValidationException("Phase modules do not match path order in [{$pathKey}].");
        }
    }

    /**
     * @param mixed $skills
     * @param list<mixed> $moduleKeys
     */
    private function validateSkills(mixed $skills, array $moduleKeys, string $pathKey): void
    {
        if (! is_array($skills)) {
            throw new ContentValidationException("Skills in path [{$pathKey}] must be an array.");
        }

        foreach ($skills as $skill) {
            if (! is_array($skill)
                || ! is_string($skill['key'] ?? null)
                || ! is_string($skill['title'] ?? null)
                || ! is_string($skill['module_key'] ?? null)
                || ! in_array($skill['module_key'], $moduleKeys, true)
            ) {
                throw new ContentValidationException("Invalid skill reference in path [{$pathKey}].");
            }

            MachineKey::assert($skill['key'], 'skill key');
            MachineKey::assert($skill['module_key'], 'skill module key');

            if (isset($skill['topic_key']) && ! is_string($skill['topic_key'])) {
                throw new ContentValidationException("Invalid skill topic reference in path [{$pathKey}].");
            }
        }
    }
}
