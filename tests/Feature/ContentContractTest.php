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
            [
                '01-thinking-with-data/01-analyst-role',
                '01-thinking-with-data/02-business-to-data-question',
                '01-thinking-with-data/03-data-tables',
                '01-thinking-with-data/04-metrics-dimensions',
                '01-thinking-with-data/05-granularity',
                '01-thinking-with-data/06-aggregation-comparison',
                '01-thinking-with-data/07-data-to-insight',
            ],
            $repository->validate(),
        );
    }

    public function test_published_module_challenge_loads_as_repository_content(): void
    {
        $challenge = app(ContentRepository::class)->challenge('01-thinking-with-data');

        $this->assertSame('01-thinking-with-data/challenge', $challenge->key);
        $this->assertCount(7, $challenge->exercises);
        $this->assertSame('text_self_assessment', $challenge->exercises['challenge-01-finding']['type']);
    }

    public function test_invalid_lesson_key_is_rejected_before_file_access(): void
    {
        $this->expectException(ContentValidationException::class);

        app(ContentRepository::class)->lesson('../outside');
    }
}
