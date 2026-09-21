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
                '04-python-pandas-for-analysis/01-python-foundations',
                '06-exploratory-data-analysis/01-join-grain',
                '07-statistics-for-analysts/01-sampling-uncertainty',
                '08-data-visualization/01-choosing-a-visual',
                '09-metrics-dashboards/01-metric-tree',
                '12-communicating-insights/01-communication-builder',
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

    public function test_python_module_challenge_loads_as_repository_content(): void
    {
        $repository = app(ContentRepository::class);
        $lesson = $repository->lesson('04-python-pandas-for-analysis/01-python-foundations');
        $challenge = $repository->challenge('04-python-pandas-for-analysis');

        $this->assertCount(4, $lesson->exercises);
        $this->assertSame('python_practice', $lesson->exercises['python-merge-aggregate-01']['interactive']['type']);
        $this->assertCount(2, $challenge->exercises);
        $this->assertSame('text_self_assessment', $challenge->exercises['python-finding-01']['type']);
    }

    public function test_visualization_module_challenge_loads_as_repository_content(): void
    {
        $repository = app(ContentRepository::class);
        $lesson = $repository->lesson('08-data-visualization/01-choosing-a-visual');
        $challenge = $repository->challenge('08-data-visualization');

        $this->assertSame('visualization_playground', $lesson->exercises['visual-question-01']['interactive']['type']);
        $this->assertSame('visualization_playground', $challenge->exercises['visual-composition-01']['interactive']['type']);
        $this->assertSame('text_self_assessment', $challenge->exercises['visual-finding-01']['type']);
    }

    public function test_join_module_challenge_loads_as_repository_content(): void
    {
        $repository = app(ContentRepository::class);
        $lesson = $repository->lesson('06-exploratory-data-analysis/01-join-grain');
        $challenge = $repository->challenge('06-exploratory-data-analysis');

        $this->assertSame('join_row_multiplication', $lesson->exercises['join-grain-01']['interactive']['type']);
        $this->assertSame('join_row_multiplication', $challenge->exercises['join-diagnosis-01']['interactive']['type']);
        $this->assertSame('text_self_assessment', $challenge->exercises['join-finding-01']['type']);
    }

    public function test_sampling_module_challenge_loads_as_repository_content(): void
    {
        $repository = app(ContentRepository::class);
        $lesson = $repository->lesson('07-statistics-for-analysts/01-sampling-uncertainty');
        $challenge = $repository->challenge('07-statistics-for-analysts');

        $this->assertSame('sampling_uncertainty', $lesson->exercises['sampling-uncertainty-01']['interactive']['type']);
        $this->assertSame('sampling_uncertainty', $challenge->exercises['sampling-uncertainty-challenge-01']['interactive']['type']);
        $this->assertSame('text_self_assessment', $challenge->exercises['sampling-uncertainty-reflection-01']['type']);
    }

    public function test_metric_tree_module_challenge_loads_as_repository_content(): void
    {
        $repository = app(ContentRepository::class);
        $lesson = $repository->lesson('09-metrics-dashboards/01-metric-tree');
        $challenge = $repository->challenge('09-metrics-dashboards');

        $this->assertSame('metric_tree_builder', $lesson->exercises['metric-tree-01']['interactive']['type']);
        $this->assertSame('metric_tree_builder', $challenge->exercises['metric-tree-challenge-01']['interactive']['type']);
        $this->assertSame('text_self_assessment', $challenge->exercises['metric-tree-reflection-01']['type']);
    }

    public function test_communication_module_challenge_loads_as_repository_content(): void
    {
        $repository = app(ContentRepository::class);
        $lesson = $repository->lesson('12-communicating-insights/01-communication-builder');
        $challenge = $repository->challenge('12-communicating-insights');

        $this->assertSame('text_self_assessment', $lesson->exercises['communication-builder-01']['type']);
        $this->assertSame('communication_builder', $lesson->exercises['communication-builder-01']['interactive']['type']);
        $this->assertSame('communication_builder', $challenge->exercises['communication-builder-challenge-01']['interactive']['type']);
    }

    public function test_invalid_lesson_key_is_rejected_before_file_access(): void
    {
        $this->expectException(ContentValidationException::class);

        app(ContentRepository::class)->lesson('../outside');
    }
}
