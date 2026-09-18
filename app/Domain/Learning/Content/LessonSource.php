<?php

namespace App\Domain\Learning\Content;

final class LessonSource
{
    public function __construct(
        public readonly string $key,
        public readonly string $markdown,
        public readonly array $exercises,
        public readonly string $sourceHash,
    ) {
    }
}
