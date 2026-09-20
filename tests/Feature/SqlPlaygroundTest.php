<?php

namespace Tests\Feature;

use App\Domain\Datasets\SqlSpikeFixture;
use App\Domain\Learning\Content\MarkdownLessonRenderer;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use Tests\TestCase;

class SqlPlaygroundTest extends TestCase
{
    use DatabaseTransactions;

    public function test_module_three_publishes_a_repository_backed_sql_lesson_and_challenge(): void
    {
        $lesson = $this->get('/learn/data-analyst/03-sql-for-data-analysis/01-query-foundations');

        $lesson->assertOk()
            ->assertSee('Start with the Table')
            ->assertSee('data-learning-component="sql-playground"', false)
            ->assertSee('sql-foundation-01')
            ->assertSee('orders')
            ->assertSee('order_items');

        $this->get('/learn/data-analyst/03-sql-for-data-analysis/challenge')
            ->assertOk()
            ->assertSee('NusaMart Sales Investigation')
            ->assertSee('challenge-03-grain')
            ->assertSee('challenge-03-diagnostic')
            ->assertSee('Latihan terarah');
    }

    public function test_authenticated_sql_result_is_validated_and_persisted_as_an_attempt(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson('/attempts/check', [
            'exercise_key' => 'data-analyst/03-sql-for-data-analysis/01-query-foundations/sql-foundation-01',
            'answer' => [
                'columns' => ['order_id', 'customer_id'],
                'rows' => [
                    ['ORD001', 'C001'],
                    ['ORD003', 'C001'],
                    ['ORD004', 'C003'],
                    ['ORD006', 'C002'],
                ],
            ],
        ])->assertOk()
            ->assertJsonPath('result.status', 'completed')
            ->assertJsonPath('attempt.status', 'completed');
    }

    public function test_sql_execution_has_no_mutating_or_remote_http_route(): void
    {
        foreach (Route::getRoutes() as $route) {
            $uri = $route->uri();
            $methods = $route->methods();

            if (str_contains($uri, 'sql')) {
                $this->assertSame([], array_values(array_intersect(['POST', 'PUT', 'PATCH', 'DELETE'], $methods)), $uri);
            }
        }

        $this->assertNotNull(Route::getRoutes()->getByName('spike.sql'));
        $this->assertSame(['GET', 'HEAD'], Route::getRoutes()->getByName('spike.sql')->methods());
    }

    public function test_sql_fixture_is_versioned_and_contains_the_required_grain(): void
    {
        $fixture = app(SqlSpikeFixture::class)->payload();

        $this->assertSame('nusamart', $fixture['dataset_key']);
        $this->assertSame('v1', $fixture['version']);
        $this->assertSame(6, count($fixture['tables']['orders']['rows']));
        $this->assertSame(9, count($fixture['tables']['order_items']['rows']));
        $this->assertCount(3, $fixture['relationships']);
    }

    public function test_sql_lesson_cache_key_includes_the_fixture_source_hash(): void
    {
        $rendered = app(MarkdownLessonRenderer::class)->render('03-sql-for-data-analysis/01-query-foundations');

        $this->assertMatchesRegularExpression('/:sql-[a-f0-9]{64}$/', $rendered->cacheKey);
    }
}
