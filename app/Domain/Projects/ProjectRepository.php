<?php

namespace App\Domain\Projects;

use App\Domain\Learning\Content\ContentValidationException;
use App\Domain\Learning\Content\ExerciseConfigValidator;
use App\Domain\Learning\Content\MachineKey;
use Illuminate\Support\Facades\File;
use JsonException;

final class ProjectRepository
{
    /** @return list<array<string, mixed>> */
    public function projects(): array
    {
        $root = $this->root();
        $projects = [];

        foreach (File::directories($root) as $directory) {
            $key = basename($directory);
            $projects[] = $this->project($key);
        }

        usort($projects, static fn (array $left, array $right): int => $left['position'] <=> $right['position']);

        return $projects;
    }

    /** @return list<array<string, mixed>> */
    public function publishedProjects(): array
    {
        return array_values(array_filter($this->projects(), static fn (array $project): bool => $project['status'] === 'published'));
    }

    /** @return array<string, mixed> */
    public function publishedProject(string $key): array
    {
        $project = $this->project($key);

        if ($project['status'] !== 'published') {
            throw new ContentValidationException("Project [{$key}] is not published.");
        }

        return $project;
    }

    /** @return array<string, mixed> */
    public function project(string $key): array
    {
        MachineKey::assert($key, 'project key');
        $path = $this->root().'/'.$key.'/project.json';

        if (! File::exists($path)) {
            throw new ContentValidationException("Project [{$key}] does not exist.");
        }

        try {
            $project = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new ContentValidationException("Invalid project JSON [{$key}]: {$exception->getMessage()}", previous: $exception);
        }

        $this->validateProjectMetadata($project, $key);

        return $project;
    }

    /** @return array{project: array<string, mixed>, stage: array<string, mixed>, markdown: string, exercises: array<string, array<string, mixed>>, source_hash: string} */
    public function stage(string $projectKey, string $stageKey): array
    {
        $project = $this->publishedProject($projectKey);
        MachineKey::assert($stageKey, 'project stage key');
        $stage = $project['stages'][$stageKey] ?? null;

        if (! is_array($stage)) {
            throw new ContentValidationException("Stage [{$stageKey}] is not registered in project [{$projectKey}].");
        }

        $root = $this->root().'/'.$projectKey;
        $markdownPath = $this->safePath($root, $stage['content_file'], "stage [{$projectKey}/{$stageKey}]");
        $exercisePath = $this->safePath($root, $stage['exercise_file'], "stage exercises [{$projectKey}/{$stageKey}]");

        if (! File::exists($markdownPath) || ! File::exists($exercisePath)) {
            throw new ContentValidationException("Missing content for project stage [{$projectKey}/{$stageKey}].");
        }

        $markdown = File::get($markdownPath);

        try {
            $decoded = json_decode(File::get($exercisePath), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new ContentValidationException("Invalid project exercise JSON [{$projectKey}/{$stageKey}]: {$exception->getMessage()}", previous: $exception);
        }

        $exercises = (new ExerciseConfigValidator)->normalize($decoded, "project/{$projectKey}/{$stageKey}");

        if (! isset($exercises[$stage['exercise_id']])) {
            throw new ContentValidationException("Stage exercise [{$stage['exercise_id']}] is not registered in [{$projectKey}/{$stageKey}].");
        }

        $sourceHash = hash('sha256', $markdown.'\n'.json_encode($exercises, JSON_THROW_ON_ERROR).'\n'.json_encode($stage, JSON_THROW_ON_ERROR));

        return [
            'project' => $project,
            'stage' => $stage,
            'markdown' => $markdown,
            'exercises' => $exercises,
            'source_hash' => $sourceHash,
        ];
    }

    /** @return array{project: array<string, mixed>, markdown: string, source_hash: string} */
    public function reference(string $projectKey): array
    {
        $project = $this->publishedProject($projectKey);
        $path = $this->safePath($this->root().'/'.$projectKey, $project['reference_file'], "reference [{$projectKey}]");

        if (! File::exists($path)) {
            throw new ContentValidationException("Missing project reference [{$projectKey}].");
        }

        $markdown = File::get($path);

        return [
            'project' => $project,
            'markdown' => $markdown,
            'source_hash' => hash('sha256', $markdown.'\n'.json_encode($project, JSON_THROW_ON_ERROR)),
        ];
    }

    /** @return list<string> */
    public function validate(): array
    {
        $keys = [];

        foreach ($this->projects() as $project) {
            $keys[] = $project['key'];

            if ($project['status'] !== 'published') {
                continue;
            }

            foreach ($project['stage_keys'] as $stageKey) {
                $this->stage($project['key'], $stageKey);
            }

            $this->reference($project['key']);
        }

        return $keys;
    }

    /** @param array<string, mixed> $project */
    private function validateProjectMetadata(array $project, string $key): void
    {
        if (($project['schema_version'] ?? null) !== 1
            || ($project['type'] ?? null) !== 'project'
            || ($project['key'] ?? null) !== $key
            || ! is_int($project['number'] ?? null)
            || ! is_string($project['title'] ?? null)
            || ! is_string($project['focus'] ?? null)
            || ! is_string($project['difficulty'] ?? null)
            || ! is_string($project['description'] ?? null)
            || ! is_int($project['estimated_minutes'] ?? null)
            || ! in_array($project['status'] ?? null, ['published', 'roadmap'], true)
            || ! is_int($project['position'] ?? null)
            || ! is_array($project['dataset_refs'] ?? null)
            || ! is_array($project['tool_guidance'] ?? null)
            || ! is_array($project['stage_keys'] ?? null)
            || ! is_array($project['stages'] ?? null)) {
            throw new ContentValidationException("Invalid project metadata [{$key}].");
        }

        if ($project['status'] === 'roadmap') {
            return;
        }

        if ($project['stage_keys'] !== ['brief', 'understand', 'plan', 'investigate', 'validate', 'communicate']
            || ! is_string($project['reference_file'] ?? null)) {
            throw new ContentValidationException("Published project [{$key}] must use the canonical six-stage model.");
        }

        foreach ($project['stage_keys'] as $stageKey) {
            $stage = $project['stages'][$stageKey] ?? null;

            if (! is_array($stage)
                || ($stage['key'] ?? null) !== $stageKey
                || ! is_int($stage['number'] ?? null)
                || ! is_string($stage['title'] ?? null)
                || ! is_string($stage['content_file'] ?? null)
                || ! is_string($stage['exercise_file'] ?? null)
                || ! is_string($stage['exercise_id'] ?? null)) {
                throw new ContentValidationException("Invalid stage metadata [{$key}/{$stageKey}].");
            }
        }
    }

    private function root(): string
    {
        $root = rtrim((string) config('belajardata.projects_path'), '/\\');

        if (! File::isDirectory($root)) {
            throw new ContentValidationException('The projects content path does not exist.');
        }

        return $root;
    }

    private function safePath(string $root, string $relative, string $label): string
    {
        if ($relative === '' || str_contains($relative, '..') || str_starts_with($relative, '/') || str_starts_with($relative, '\\')) {
            throw new ContentValidationException("Unsafe path for {$label}.");
        }

        return $root.'/'.$relative;
    }
}
