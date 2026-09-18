<?php

namespace App\Domain\Learning\Content;

final class RenderedLesson
{
    public function __construct(
        public readonly string $html,
        public readonly array $headings,
        public readonly string $cacheKey,
    ) {
    }
}
