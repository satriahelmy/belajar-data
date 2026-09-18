<?php

namespace Tests\Feature;

use App\Domain\Learning\Content\ContentRepository;
use App\Domain\Learning\Content\ContentValidationException;
use Tests\TestCase;

class ContentContractTest extends TestCase
{
    public function test_canonical_content_metadata_and_stable_keys_load(): void
    {
        $repository = app(ContentRepository::class);

        $this->assertSame('data-analyst', $repository->path()['key']);
        $this->assertSame('01-thinking-with-data', $repository->module('01-thinking-with-data')['key']);
        $this->assertSame(
            ['01-thinking-with-data/01-analyst-role', '01-thinking-with-data/02-data-tables'],
            $repository->validate(),
        );
    }

    public function test_invalid_lesson_key_is_rejected_before_file_access(): void
    {
        $this->expectException(ContentValidationException::class);

        app(ContentRepository::class)->lesson('../outside');
    }
}
