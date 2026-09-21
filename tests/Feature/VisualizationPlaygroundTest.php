<?php

namespace Tests\Feature;

use App\Domain\Datasets\VisualizationDatasetFixture;
use App\Domain\Learning\Content\MarkdownLessonRenderer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use Tests\TestCase;

class VisualizationPlaygroundTest extends TestCase
{
    use DatabaseTransactions;

    public function test_module_eight_renders_a_bounded_visualization_lesson_and_challenge(): void
    {
        $this->get('/learn/data-analyst/08-data-visualization/01-choosing-a-visual')
            ->assertOk()
            ->assertSee('Choose the Question Before the Chart')
            ->assertSee('data-learning-component="visualization-playground"', false)
            ->assertSee('visual-question-01')
            ->assertSee('Tabel angka yang mendasari visual')
            ->assertSee('Electronics');

        $this->get('/learn/data-analyst/08-data-visualization/challenge')
            ->assertOk()
            ->assertSee('Visual Explanation')
            ->assertSee('visual-composition-01')
            ->assertSee('visual-finding-01');
    }

    public function test_visualization_choice_is_validated_and_persisted_as_bounded_state(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson('/attempts/check', [
            'exercise_key' => 'data-analyst/08-data-visualization/01-choosing-a-visual/visual-question-01',
            'answer' => [
                'columns' => ['chart', 'metric', 'dimension', 'sort', 'highlight', 'scale_mode'],
                'rows' => [['bar', 'revenue', 'category', 'descending', 'top', 'honest']],
            ],
        ])->assertOk()
            ->assertJsonPath('result.status', 'completed')
            ->assertJsonPath('attempt.status', 'completed');
    }

    public function test_visualization_fixture_contains_joined_rows_and_cache_includes_source_hash(): void
    {
        $fixture = app(VisualizationDatasetFixture::class)->payload();

        $this->assertSame('nusamart', $fixture['dataset_key']);
        $this->assertSame('v1', $fixture['version']);
        $this->assertSame(['order_id', 'order_date', 'month', 'region', 'channel', 'category', 'quantity', 'revenue'], array_keys($fixture['rows'][0]));
        $this->assertCount(8, $fixture['rows']);
        $this->assertSame(560, $fixture['views']['category']['rows'][0]['revenue']);

        $rendered = app(MarkdownLessonRenderer::class)->render('08-data-visualization/01-choosing-a-visual');

        $this->assertMatchesRegularExpression('/:visualization-[a-f0-9]{64}$/', $rendered->cacheKey);
    }

    public function test_ordinary_module_one_lesson_does_not_load_visualization_component(): void
    {
        $this->get('/learn/data-analyst/01-thinking-with-data/01-analyst-role')
            ->assertOk()
            ->assertDontSee('data-learning-component="visualization-playground"', false);
    }
}
