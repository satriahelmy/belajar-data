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
                '02-spreadsheet-for-analysis/02-filtering-and-sorting',
                '02-spreadsheet-for-analysis/03-calculating-business-metrics',
                '02-spreadsheet-for-analysis/04-adding-analysis-logic',
                '02-spreadsheet-for-analysis/05-connecting-with-lookups',
                '02-spreadsheet-for-analysis/06-summarizing-with-pivots',
                '02-spreadsheet-for-analysis/07-comparing-performance',
                '02-spreadsheet-for-analysis/08-building-a-simple-analysis',
                '03-sql-for-data-analysis/01-query-foundations',
                '03-sql-for-data-analysis/02-filtering-with-where',
                '03-sql-for-data-analysis/03-summarizing-with-aggregations',
                '03-sql-for-data-analysis/04-breaking-down-performance',
                '03-sql-for-data-analysis/05-combining-data-with-joins',
                '03-sql-for-data-analysis/06-adding-business-logic',
                '03-sql-for-data-analysis/07-analyzing-changes-over-time',
                '03-sql-for-data-analysis/08-structuring-an-analysis',
                '04-python-pandas-for-analysis/01-python-foundations',
                '04-python-pandas-for-analysis/02-loading-dataframes',
                '04-python-pandas-for-analysis/03-selecting-and-filtering',
                '04-python-pandas-for-analysis/04-creating-transformations',
                '04-python-pandas-for-analysis/05-grouping-and-aggregating',
                '04-python-pandas-for-analysis/06-combining-with-merge',
                '04-python-pandas-for-analysis/07-working-with-dates',
                '04-python-pandas-for-analysis/08-building-pandas-analysis',
                '05-data-cleaning/01-what-makes-data-dirty',
                '05-data-cleaning/02-missing-values',
                '05-data-cleaning/03-duplicate-data',
                '05-data-cleaning/04-invalid-inconsistent-values',
                '05-data-cleaning/05-data-types-formats',
                '05-data-cleaning/06-outliers-unusual-values',
                '05-data-cleaning/07-validating-cleaning',
                '05-data-cleaning/08-cleaning-workflow',
                '06-exploratory-data-analysis/01-join-grain',
                '06-exploratory-data-analysis/02-question-driven-exploration',
                '06-exploratory-data-analysis/03-profiling-the-dataset',
                '06-exploratory-data-analysis/04-exploring-distributions',
                '06-exploratory-data-analysis/05-comparing-categories',
                '06-exploratory-data-analysis/06-exploring-time',
                '06-exploratory-data-analysis/07-exploring-relationships',
                '06-exploratory-data-analysis/08-drilling-down-to-findings',
                '07-statistics-for-analysts/01-why-statistics',
                '07-statistics-for-analysts/02-summary-statistics',
                '07-statistics-for-analysts/03-variability',
                '07-statistics-for-analysts/04-percentiles',
                '07-statistics-for-analysts/05-samples-populations-bias',
                '07-statistics-for-analysts/01-sampling-uncertainty',
                '07-statistics-for-analysts/07-correlation-relationships',
                '07-statistics-for-analysts/08-hypothesis-business-significance',
                '08-data-visualization/01-choosing-a-visual',
                '08-data-visualization/02-why-visualize',
                '08-data-visualization/03-comparison',
                '08-data-visualization/04-trend',
                '08-data-visualization/05-composition',
                '08-data-visualization/06-distribution-relationship',
                '08-data-visualization/07-encoding-and-design',
                '08-data-visualization/08-avoiding-misleading',
                '09-metrics-dashboards/01-what-is-a-metric',
                '09-metrics-dashboards/02-metric-vs-kpi',
                '09-metrics-dashboards/03-good-metrics',
                '09-metrics-dashboards/04-vanity-metrics',
                '09-metrics-dashboards/05-leading-lagging',
                '09-metrics-dashboards/06-north-star',
                '09-metrics-dashboards/01-metric-tree',
                '09-metrics-dashboards/08-metrics-to-dashboard',
                '10-tableau-for-data-analysis/01-tableau-workflow',
                '10-tableau-for-data-analysis/02-tableau-model-and-metrics',
                '10-tableau-for-data-analysis/03-tableau-dashboard-validation',
                '11-business-analysis/01-understanding-business-problem',
                '11-business-analysis/02-analysis-plan',
                '11-business-analysis/03-break-down-problem',
                '11-business-analysis/04-performance-context',
                '11-business-analysis/05-finding-drivers',
                '11-business-analysis/06-segmentation',
                '11-business-analysis/07-evidence-to-recommendation',
                '11-business-analysis/08-knowing-what-you-dont-know',
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

    public function test_data_cleaning_and_business_analysis_modules_are_published_content(): void
    {
        $repository = app(ContentRepository::class);

        foreach (['05-data-cleaning', '11-business-analysis'] as $moduleKey) {
            $module = $repository->module($moduleKey);

            $this->assertSame('published', $module['status']);
            $this->assertCount(8, $module['topic_keys']);
            $this->assertSame('published', $module['challenge']['status']);
            $this->assertCount(3, $repository->challenge($moduleKey)->exercises);
        }
    }

    public function test_spreadsheet_module_challenge_loads_as_repository_content(): void
    {
        $repository = app(ContentRepository::class);
        $lesson = $repository->lesson('02-spreadsheet-for-analysis/01-spreadsheet-foundations');
        $challenge = $repository->challenge('02-spreadsheet-for-analysis');

        $this->assertCount(3, $lesson->exercises);
        $this->assertSame('spreadsheet_playground', $lesson->exercises['sheet-metrics-01']['interactive']['type']);
        $this->assertCount(5, $challenge->exercises);
        $this->assertSame('text_self_assessment', $challenge->exercises['sheet-finding-01']['type']);
    }

    public function test_python_module_challenge_loads_as_repository_content(): void
    {
        $repository = app(ContentRepository::class);
        $lesson = $repository->lesson('04-python-pandas-for-analysis/01-python-foundations');
        $challenge = $repository->challenge('04-python-pandas-for-analysis');

        $this->assertCount(8, $repository->module('04-python-pandas-for-analysis')['topic_keys']);
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
        $this->assertCount(5, $challenge->exercises);
        $this->assertSame('text_self_assessment', $challenge->exercises['visual-finding-01']['type']);
    }

    public function test_join_module_challenge_loads_as_repository_content(): void
    {
        $repository = app(ContentRepository::class);
        $lesson = $repository->lesson('06-exploratory-data-analysis/01-join-grain');
        $challenge = $repository->challenge('06-exploratory-data-analysis');

        $this->assertSame('join_row_multiplication', $lesson->exercises['join-grain-01']['interactive']['type']);
        $this->assertSame('join_row_multiplication', $challenge->exercises['join-diagnosis-01']['interactive']['type']);
        $this->assertCount(4, $challenge->exercises);
        $this->assertSame('text_self_assessment', $challenge->exercises['join-finding-01']['type']);
    }

    public function test_sampling_module_challenge_loads_as_repository_content(): void
    {
        $repository = app(ContentRepository::class);
        $lesson = $repository->lesson('07-statistics-for-analysts/01-sampling-uncertainty');
        $challenge = $repository->challenge('07-statistics-for-analysts');

        $this->assertCount(8, $repository->module('07-statistics-for-analysts')['topic_keys']);
        $this->assertSame('sampling_uncertainty', $lesson->exercises['sampling-uncertainty-01']['interactive']['type']);
        $this->assertSame('sampling_uncertainty', $challenge->exercises['sampling-uncertainty-challenge-01']['interactive']['type']);
        $this->assertSame('text_self_assessment', $challenge->exercises['sampling-uncertainty-reflection-01']['type']);
    }

    public function test_metric_tree_module_challenge_loads_as_repository_content(): void
    {
        $repository = app(ContentRepository::class);
        $lesson = $repository->lesson('09-metrics-dashboards/01-metric-tree');
        $challenge = $repository->challenge('09-metrics-dashboards');

        $this->assertCount(8, $repository->module('09-metrics-dashboards')['topic_keys']);
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

    public function test_tableau_module_challenge_loads_as_repository_content(): void
    {
        $repository = app(ContentRepository::class);
        $lesson = $repository->lesson('10-tableau-for-data-analysis/01-tableau-workflow');
        $challenge = $repository->challenge('10-tableau-for-data-analysis');

        $this->assertSame('multiple_choice', $lesson->exercises['tableau-download-01']['type']);
        $this->assertSame('numeric', $repository->lesson('10-tableau-for-data-analysis/03-tableau-dashboard-validation')->exercises['tableau-september-revenue-01']['type']);
        $this->assertSame('text_self_assessment', $challenge->exercises['tableau-challenge-reflection-01']['type']);
        $this->assertCount(4, $challenge->exercises);
    }

    public function test_invalid_lesson_key_is_rejected_before_file_access(): void
    {
        $this->expectException(ContentValidationException::class);

        app(ContentRepository::class)->lesson('../outside');
    }
}
