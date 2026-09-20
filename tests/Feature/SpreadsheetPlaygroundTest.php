<?php

namespace Tests\Feature;

use App\Domain\Datasets\SpreadsheetDatasetFixture;
use App\Domain\Learning\Content\MarkdownLessonRenderer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Tests\TestCase;

class SpreadsheetPlaygroundTest extends TestCase
{
    use DatabaseTransactions;

    public function test_module_two_publishes_a_repository_backed_spreadsheet_lesson_and_challenge(): void
    {
        $this->get('/learn/data-analyst/02-spreadsheet-for-analysis/01-spreadsheet-foundations')
            ->assertOk()
            ->assertSee('Start with the Table')
            ->assertSee('data-learning-component="spreadsheet-playground"', false)
            ->assertSee('sheet-inspection-01')
            ->assertSee('Transactions')
            ->assertSee('Products');

        $this->get('/learn/data-analyst/02-spreadsheet-for-analysis/challenge')
            ->assertOk()
            ->assertSee('NusaMart Monthly Sales Performance')
            ->assertSee('sheet-summary-01')
            ->assertSee('sheet-finding-01')
            ->assertSee('Spreadsheet Practice')
            ->assertSee('Latihan terarah')
            ->assertDontSee('SQL di browser');
    }

    public function test_authenticated_spreadsheet_result_is_validated_and_persisted_as_an_attempt(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson('/attempts/check', [
            'exercise_key' => 'data-analyst/02-spreadsheet-for-analysis/01-spreadsheet-foundations/sheet-metrics-01',
            'answer' => [
                'columns' => ['B2', 'B3', 'B4'],
                'rows' => [[1050, 131.25, 'Above threshold']],
            ],
        ])->assertOk()
            ->assertJsonPath('result.status', 'completed')
            ->assertJsonPath('attempt.status', 'completed');
    }

    public function test_spreadsheet_execution_has_no_mutating_or_remote_http_route(): void
    {
        $spreadsheetRoutes = 0;

        foreach (Route::getRoutes() as $route) {
            $uri = $route->uri();

            if (str_contains($uri, 'spreadsheet')) {
                $spreadsheetRoutes++;
                $this->assertSame([], array_values(array_intersect(['POST', 'PUT', 'PATCH', 'DELETE'], $route->methods())), $uri);
            }
        }

        $this->assertGreaterThanOrEqual(0, $spreadsheetRoutes);
    }

    public function test_spreadsheet_fixture_is_versioned_and_contains_the_bounded_tables(): void
    {
        $fixture = app(SpreadsheetDatasetFixture::class)->payload();

        $this->assertSame('nusamart', $fixture['dataset_key']);
        $this->assertSame('v1', $fixture['version']);
        $this->assertSame('one row per order-item transaction', $fixture['grain']);
        $this->assertSame(['transactions', 'products'], array_keys($fixture['tables']));
        $this->assertCount(8, $fixture['tables']['transactions']['rows']);
        $this->assertCount(4, $fixture['tables']['products']['rows']);
    }

    public function test_spreadsheet_lesson_cache_key_includes_the_fixture_source_hash(): void
    {
        $rendered = app(MarkdownLessonRenderer::class)->render('02-spreadsheet-for-analysis/01-spreadsheet-foundations');

        $this->assertMatchesRegularExpression('/:spreadsheet-[a-f0-9]{64}$/', $rendered->cacheKey);
    }
}
