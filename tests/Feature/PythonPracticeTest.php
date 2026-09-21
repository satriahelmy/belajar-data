<?php

namespace Tests\Feature;

use App\Domain\Datasets\PythonDatasetFixture;
use App\Domain\Learning\Content\MarkdownLessonRenderer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Tests\TestCase;

class PythonPracticeTest extends TestCase
{
    use DatabaseTransactions;

    public function test_module_four_publishes_a_repository_backed_bounded_python_lesson_and_challenge(): void
    {
        $this->get('/learn/data-analyst/04-python-pandas-for-analysis/01-python-foundations')
            ->assertOk()
            ->assertSee('From DataFrame to Finding')
            ->assertSee('data-learning-component="python-practice"', false)
            ->assertSee('python-inspect-01')
            ->assertSee('transactions.csv')
            ->assertDontSee('Pyodide');

        $this->get('/learn/data-analyst/04-python-pandas-for-analysis/challenge')
            ->assertOk()
            ->assertSee('NusaMart Analysis Notebook')
            ->assertSee('python-september-01')
            ->assertSee('python-finding-01')
            ->assertDontSee('Pyodide');
    }

    public function test_authenticated_python_output_is_validated_and_persisted_without_sending_code(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson('/attempts/check', [
            'exercise_key' => 'data-analyst/04-python-pandas-for-analysis/challenge/python-september-01',
            'answer' => [
                'columns' => ['category', 'revenue'],
                'rows' => [['Electronics', 320], ['Home', 180], ['Grocery', 100]],
            ],
        ])->assertOk()
            ->assertJsonPath('result.status', 'completed')
            ->assertJsonPath('attempt.status', 'completed');
    }

    public function test_python_fixture_reads_the_canonical_csv_subset(): void
    {
        $fixture = app(PythonDatasetFixture::class)->payload();

        $this->assertSame('nusamart', $fixture['dataset_key']);
        $this->assertSame('v1', $fixture['version']);
        $this->assertSame(['transactions', 'products'], array_keys($fixture['tables']));
        $this->assertSame(['order_id', 'order_date', 'region', 'channel', 'product_id', 'quantity', 'unit_price'], array_keys($fixture['tables']['transactions']['columns']));
        $this->assertCount(8, $fixture['tables']['transactions']['rows']);
        $this->assertCount(4, $fixture['tables']['products']['rows']);
    }

    public function test_python_lesson_cache_key_includes_the_fixture_source_hash(): void
    {
        $rendered = app(MarkdownLessonRenderer::class)->render('04-python-pandas-for-analysis/01-python-foundations');

        $this->assertMatchesRegularExpression('/:python-[a-f0-9]{64}$/', $rendered->cacheKey);
    }

    public function test_downloadable_fallback_notebook_is_available_and_python_has_no_mutating_route(): void
    {
        $this->get('/downloads/nusamart-module-04-fallback.ipynb')
            ->assertOk()
            ->assertHeader('content-disposition', 'attachment; filename=nusamart-module-04-fallback.ipynb');

        foreach (Route::getRoutes() as $route) {
            if (str_contains($route->uri(), 'python')) {
                $this->assertSame([], array_values(array_intersect(['POST', 'PUT', 'PATCH', 'DELETE'], $route->methods())), $route->uri());
            }
        }
    }

    public function test_ordinary_module_one_lesson_does_not_load_python_practice(): void
    {
        $this->get('/learn/data-analyst/01-thinking-with-data/01-analyst-role')
            ->assertOk()
            ->assertDontSee('data-learning-component="python-practice"', false);
    }
}
