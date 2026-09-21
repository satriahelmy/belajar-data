<?php

namespace Tests\Feature;

use App\Domain\Datasets\SamplingDatasetFixture;
use App\Domain\Learning\Content\MarkdownLessonRenderer;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SamplingUncertaintyTest extends TestCase
{
    use DatabaseTransactions;

    public function test_module_seven_renders_a_bounded_sampling_lesson_and_challenge(): void
    {
        $this->get('/learn/data-analyst/07-statistics-for-analysts/01-sampling-uncertainty')
            ->assertOk()
            ->assertSee('Sampling Changes the Estimate')
            ->assertSee('data-learning-component="sampling-uncertainty-playground"', false)
            ->assertSee('sampling-uncertainty-01')
            ->assertSee('Population mean:')
            ->assertSee('Estimasi sample awal');

        $this->get('/learn/data-analyst/07-statistics-for-analysts/challenge')
            ->assertOk()
            ->assertSee('Evidence &amp; Uncertainty', false)
            ->assertSee('sampling-uncertainty-challenge-01')
            ->assertSee('sampling-uncertainty-reflection-01');
    }

    public function test_sampling_selection_is_validated_and_persisted_as_bounded_state(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson('/attempts/check', [
            'exercise_key' => 'data-analyst/07-statistics-for-analysts/01-sampling-uncertainty/sampling-uncertainty-01',
            'answer' => [
                'columns' => ['metric', 'sample_size', 'repeats', 'seed'],
                'rows' => [['revenue', 3, 5, 42]],
            ],
        ])->assertOk()
            ->assertJsonPath('result.status', 'completed')
            ->assertJsonPath('attempt.status', 'completed');
    }

    public function test_sampling_fixture_is_deterministic_and_cache_includes_source_hash(): void
    {
        $fixture = app(SamplingDatasetFixture::class);
        $first = $fixture->summary('revenue', 3, 5, 42);
        $second = $fixture->summary('revenue', 3, 5, 42);

        $this->assertSame($first, $second);
        $this->assertSame(131.25, $first['population_mean']);
        $this->assertCount(5, $first['estimates']);
        $this->assertNotSame($first['estimate_min'], $first['estimate_max']);

        $rendered = app(MarkdownLessonRenderer::class)->render('07-statistics-for-analysts/01-sampling-uncertainty');

        $this->assertMatchesRegularExpression('/:sampling-[a-f0-9]{64}$/', $rendered->cacheKey);
    }

    public function test_ordinary_module_one_lesson_does_not_load_sampling_component(): void
    {
        $this->get('/learn/data-analyst/01-thinking-with-data/01-analyst-role')
            ->assertOk()
            ->assertDontSee('data-learning-component="sampling-uncertainty-playground"', false);
    }
}
