<?php

namespace Tests\Feature;

use App\Domain\Learning\Content\MarkdownLessonRenderer;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CommunicationBuilderTest extends TestCase
{
    use DatabaseTransactions;

    public function test_module_twelve_renders_a_structured_communication_lesson_and_challenge(): void
    {
        $this->get('/learn/data-analyst/12-communicating-insights/01-communication-builder')
            ->assertOk()
            ->assertSee('Write an Evidence-Led Finding')
            ->assertSee('data-learning-component="communication-builder"', false)
            ->assertSee('communication-builder-01')
            ->assertSee('Susun jawaban melalui lima bagian');

        $this->get('/learn/data-analyst/12-communicating-insights/challenge')
            ->assertOk()
            ->assertSee('Present the NusaMart Growth Story')
            ->assertSee('communication-builder-challenge-01');
    }

    public function test_communication_fields_and_checklist_are_persisted_without_ai_grading(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson('/attempts/check', [
            'exercise_key' => 'data-analyst/12-communicating-insights/01-communication-builder/communication-builder-01',
            'answer' => [
                'text' => json_encode([
                    'headline' => 'Revenue naik dari Agustus ke September.',
                    'evidence' => 'Revenue 450 menjadi 600.',
                    'known' => 'Ada perbedaan antarperiode.',
                    'unknown' => 'Penyebab belum terbukti.',
                    'next_step' => 'Bandingkan orders dan average order value.',
                ], JSON_THROW_ON_ERROR),
                'fields' => [
                    'headline' => 'Revenue naik dari Agustus ke September.',
                    'evidence' => 'Revenue 450 menjadi 600.',
                    'known' => 'Ada perbedaan antarperiode.',
                    'unknown' => 'Penyebab belum terbukti.',
                    'next_step' => 'Bandingkan orders dan average order value.',
                ],
                'checklist' => [true, true, true, true, true],
                'complete' => true,
            ],
        ])->assertOk()
            ->assertJsonPath('result.status', 'completed')
            ->assertJsonPath('attempt.status', 'completed')
            ->assertJsonPath('attempt.answer.fields.headline', 'Revenue naik dari Agustus ke September.');
    }

    public function test_communication_review_remains_incomplete_when_a_structured_field_is_empty(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson('/attempts/check', [
            'exercise_key' => 'data-analyst/12-communicating-insights/01-communication-builder/communication-builder-01',
            'answer' => [
                'text' => '{"headline":"Revenue naik"}',
                'fields' => [
                    'headline' => 'Revenue naik',
                    'evidence' => '',
                ],
                'checklist' => [true, true, true, true, true],
                'complete' => true,
            ],
        ])->assertOk()
            ->assertJsonPath('result.status', 'incomplete')
            ->assertJsonPath('result.completed', false);
    }

    public function test_communication_lesson_cache_uses_repository_source_hash(): void
    {
        $rendered = app(MarkdownLessonRenderer::class)->render('12-communicating-insights/01-communication-builder');

        $this->assertMatchesRegularExpression('/:12-communicating-insights\/01-communication-builder:[a-f0-9]{64}$/', $rendered->cacheKey);
    }

    public function test_ordinary_module_one_lesson_does_not_load_communication_component(): void
    {
        $this->get('/learn/data-analyst/01-thinking-with-data/01-analyst-role')
            ->assertOk()
            ->assertDontSee('data-learning-component="communication-builder"', false);
    }
}
