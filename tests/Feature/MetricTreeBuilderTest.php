<?php

namespace Tests\Feature;

use App\Domain\Datasets\MetricTreeFixture;
use App\Domain\Learning\Content\MarkdownLessonRenderer;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MetricTreeBuilderTest extends TestCase
{
    use DatabaseTransactions;

    public function test_module_nine_renders_a_bounded_metric_tree_lesson_and_challenge(): void
    {
        $this->get('/learn/data-analyst/09-metrics-dashboards/01-metric-tree')
            ->assertOk()
            ->assertSee('Build a Metric Tree')
            ->assertSee('data-learning-component="metric-tree-builder"', false)
            ->assertSee('metric-tree-01')
            ->assertSee('Susunan metric awal')
            ->assertSee('Average order value');

        $this->get('/learn/data-analyst/09-metrics-dashboards/challenge')
            ->assertOk()
            ->assertSee('Build a Metric System')
            ->assertSee('metric-tree-challenge-01')
            ->assertSee('metric-tree-reflection-01');
    }

    public function test_metric_tree_relationships_are_validated_and_persisted_as_bounded_state(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson('/attempts/check', [
            'exercise_key' => 'data-analyst/09-metrics-dashboards/01-metric-tree/metric-tree-01',
            'answer' => [
                'columns' => ['node_id', 'parent_id'],
                'rows' => [
                    ['orders', 'revenue'],
                    ['average-order-value', 'revenue'],
                    ['units-per-order', 'average-order-value'],
                    ['average-price-per-unit', 'average-order-value'],
                ],
            ],
        ])->assertOk()
            ->assertJsonPath('result.status', 'completed')
            ->assertJsonPath('attempt.status', 'completed');
    }

    public function test_metric_tree_fixture_and_cache_are_versioned(): void
    {
        $fixture = app(MetricTreeFixture::class)->payload();

        $this->assertSame('nusamart', $fixture['dataset_key']);
        $this->assertSame('v1', $fixture['version']);
        $this->assertSame('revenue', $fixture['trees']['revenue-tree']['root_id']);
        $this->assertSame('average-order-value', $fixture['trees']['revenue-tree']['valid_parents']['units-per-order']);

        $rendered = app(MarkdownLessonRenderer::class)->render('09-metrics-dashboards/01-metric-tree');

        $this->assertMatchesRegularExpression('/:metric-tree-[a-f0-9]{64}$/', $rendered->cacheKey);
    }

    public function test_ordinary_module_one_lesson_does_not_load_metric_tree_component(): void
    {
        $this->get('/learn/data-analyst/01-thinking-with-data/01-analyst-role')
            ->assertOk()
            ->assertDontSee('data-learning-component="metric-tree-builder"', false);
    }
}
