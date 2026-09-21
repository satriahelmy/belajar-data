<?php

namespace Tests\Feature;

use App\Domain\Datasets\JoinDatasetFixture;
use App\Domain\Learning\Content\MarkdownLessonRenderer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use Tests\TestCase;

class JoinGrainPlaygroundTest extends TestCase
{
    use DatabaseTransactions;

    public function test_module_six_renders_a_bounded_join_grain_lesson_and_challenge(): void
    {
        $this->get('/learn/data-analyst/06-exploratory-data-analysis/01-join-grain')
            ->assertOk()
            ->assertSee('A JOIN Can Change the Grain')
            ->assertSee('data-learning-component="join-grain-playground"', false)
            ->assertSee('join-grain-01')
            ->assertSee('Contoh hasil JOIN')
            ->assertSee('Satu order dapat memiliki beberapa item');

        $this->get('/learn/data-analyst/06-exploratory-data-analysis/challenge')
            ->assertOk()
            ->assertSee('NusaMart EDA')
            ->assertSee('join-diagnosis-01')
            ->assertSee('join-finding-01');
    }

    public function test_join_row_count_is_validated_and_persisted_as_bounded_state(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson('/attempts/check', [
            'exercise_key' => 'data-analyst/06-exploratory-data-analysis/01-join-grain/join-grain-01',
            'answer' => [
                'columns' => ['scenario', 'join_key', 'left_rows', 'right_rows', 'result_rows'],
                'rows' => [['order-to-items', 'order_id', 6, 9, 9]],
            ],
        ])->assertOk()
            ->assertJsonPath('result.status', 'completed')
            ->assertJsonPath('attempt.status', 'completed');
    }

    public function test_join_fixture_exposes_predefined_tables_and_cache_includes_source_hash(): void
    {
        $fixture = app(JoinDatasetFixture::class)->payload();

        $this->assertSame(['orders', 'order_items', 'products'], array_keys($fixture['tables']));
        $this->assertCount(6, $fixture['tables']['orders']['rows']);
        $this->assertCount(9, $fixture['tables']['order_items']['rows']);
        $this->assertSame('order_id', $fixture['scenarios']['order-to-items']['join_key']);

        $rendered = app(MarkdownLessonRenderer::class)->render('06-exploratory-data-analysis/01-join-grain');

        $this->assertMatchesRegularExpression('/:join-[a-f0-9]{64}$/', $rendered->cacheKey);
    }

    public function test_ordinary_module_one_lesson_does_not_load_join_component(): void
    {
        $this->get('/learn/data-analyst/01-thinking-with-data/01-analyst-role')
            ->assertOk()
            ->assertDontSee('data-learning-component="join-grain-playground"', false);
    }
}
