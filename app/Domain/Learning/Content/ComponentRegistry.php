<?php

namespace App\Domain\Learning\Content;

final class ComponentRegistry
{
    /** @var list<string> */
    private const REGISTERED = ['practice', 'sql-playground', 'spreadsheet-playground', 'python-practice', 'visualization-playground', 'join-grain-playground', 'sampling-uncertainty-playground', 'metric-tree-builder', 'communication-builder'];

    /** @var list<string> */
    private const PLANNED = [];

    public function assertRegistered(string $type): void
    {
        if (! in_array($type, self::REGISTERED, true)) {
            throw new InvalidLearningComponentException("Learning component [{$type}] is not registered.");
        }
    }

    /** @return list<string> */
    public function registered(): array
    {
        return self::REGISTERED;
    }

    /** @return list<string> */
    public function planned(): array
    {
        return self::PLANNED;
    }
}
