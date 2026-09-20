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
                '02-spreadsheet-for-analysis/01-spreadsheet-foundations',
                '03-sql-for-data-analysis/01-query-foundations',
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

    public function test_spreadsheet_module_challenge_loads_as_repository_content(): void
    {
        $repository = app(ContentRepository::class);
        $lesson = $repository->lesson('02-spreadsheet-for-analysis/01-spreadsheet-foundations');
        $challenge = $repository->challenge('02-spreadsheet-for-analysis');

        $this->assertCount(3, $lesson->exercises);
        $this->assertSame('spreadsheet_playground', $lesson->exercises['sheet-metrics-01']['interactive']['type']);
        $this->assertCount(4, $challenge->exercises);
        $this->assertSame('text_self_assessment', $challenge->exercises['sheet-finding-01']['type']);
    }

    public function test_invalid_lesson_key_is_rejected_before_file_access(): void
    {
        $this->expectException(ContentValidationException::class);

        app(ContentRepository::class)->lesson('../outside');
    }
}
