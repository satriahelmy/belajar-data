<?php

namespace App\Domain\Learning;

use App\Domain\Learning\Content\ContentRepository;
use App\Domain\Learning\Content\ContentValidationException;

final class CurriculumRepository
{
    public function __construct(private readonly ContentRepository $content)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function path(string $pathKey = 'data-analyst'): array
    {
        return $this->content->path($pathKey);
    }

    /**
     * Return the canonical phase/module hierarchy in repository order.
     *
     * @return list<array<string, mixed>>
     */
    public function phases(string $pathKey = 'data-analyst'): array
    {
        $path = $this->path($pathKey);
        $modules = [];

        foreach ($this->content->modules($pathKey) as $module) {
            $modules[$module['key']] = $this->decorateModule($module);
        }

        return array_map(function (array $phase) use ($modules): array {
            $phase['modules'] = array_map(
                static fn (string $moduleKey): array => $modules[$moduleKey],
                $phase['module_keys'],
            );

            return $phase;
        }, $path['phases'] ?? []);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function modules(string $pathKey = 'data-analyst'): array
    {
        return array_map(
            fn (array $module): array => $this->decorateModule($module),
            $this->content->modules($pathKey),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function publishedModule(string $moduleKey, string $pathKey = 'data-analyst'): array
    {
        $module = $this->content->module($moduleKey, $pathKey);

        if (($module['status'] ?? null) !== 'published') {
            throw new ContentValidationException("Module [{$moduleKey}] is not published.");
        }

        return $module;
    }

    /**
     * @return array<string, mixed>
     */
    public function module(string $moduleKey, string $pathKey = 'data-analyst'): array
    {
        return $this->decorateModule($this->content->module($moduleKey, $pathKey));
    }

    /**
     * @return array<string, mixed>
     */
    public function topic(string $moduleKey, string $topicKey, string $pathKey = 'data-analyst'): array
    {
        $module = $this->publishedModule($moduleKey, $pathKey);

        if (! in_array($topicKey, $module['topic_keys'], true)) {
            throw new ContentValidationException("Topic [{$topicKey}] is not registered in module [{$moduleKey}].");
        }

        return array_merge($module['topics'][$topicKey], ['key' => $topicKey]);
    }

    /**
     * @return array{previous: array<string, mixed>|null, current: array<string, mixed>, next: array<string, mixed>|null, total: int}
     */
    public function lessonNavigation(string $moduleKey, string $topicKey, string $pathKey = 'data-analyst'): array
    {
        $module = $this->publishedModule($moduleKey, $pathKey);
        $topics = $this->topics($module);
        $index = array_search($topicKey, array_column($topics, 'key'), true);

        if ($index === false) {
            throw new ContentValidationException("Topic [{$topicKey}] is not registered in module [{$moduleKey}].");
        }

        return [
            'previous' => $topics[$index - 1] ?? null,
            'current' => $topics[$index],
            'next' => $topics[$index + 1] ?? null,
            'total' => count($topics),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function skills(string $pathKey = 'data-analyst'): array
    {
        $path = $this->path($pathKey);
        $modules = [];

        foreach ($this->content->modules($pathKey) as $module) {
            $modules[$module['key']] = $module;
        }

        return array_map(function (array $skill) use ($modules): array {
            $module = $modules[$skill['module_key']];
            $topic = null;

            if (isset($skill['topic_key']) && in_array($skill['topic_key'], $module['topic_keys'], true)) {
                $topic = array_merge($module['topics'][$skill['topic_key']], ['key' => $skill['topic_key']]);
            }

            return [
                ...$skill,
                'module_title' => $module['title'],
                'module_status' => $module['status'],
                'topic' => $topic,
            ];
        }, $path['skills'] ?? []);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function topics(array $module): array
    {
        return array_map(
            static fn (string $topicKey): array => array_merge($module['topics'][$topicKey], ['key' => $topicKey]),
            $module['topic_keys'],
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function decorateModule(array $module): array
    {
        $module['topics'] = $this->topics($module);
        $module['published'] = ($module['status'] ?? null) === 'published';

        return $module;
    }
}
