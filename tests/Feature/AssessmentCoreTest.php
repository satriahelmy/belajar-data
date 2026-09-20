<?php

namespace Tests\Feature;

use App\Domain\Learner\Assessment\ExerciseValidator;
use App\Domain\Learning\Content\ContentValidationException;
use App\Domain\Learning\Content\ExerciseConfigValidator;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AssessmentCoreTest extends TestCase
{
    use DatabaseTransactions;

    public function test_multiple_choice_and_multi_select_validators_check_exact_answers(): void
    {
        $validator = app(ExerciseValidator::class);
        $multipleChoice = [
            'type' => 'multiple_choice',
            'correct_option' => 1,
            'success_feedback' => 'Benar.',
        ];
        $multiSelect = [
            'type' => 'multi_select',
            'correct_options' => [0, 2],
        ];

        $this->assertTrue($validator->validate($multipleChoice, ['option' => 1])->completed);
        $this->assertSame('incorrect', $validator->validate($multipleChoice, ['option' => 0])->status);
        $this->assertTrue($validator->validate($multiSelect, ['options' => [2, 0]])->completed);
        $this->assertSame('incorrect', $validator->validate($multiSelect, ['options' => [0]])->status);
    }

    public function test_numeric_validator_uses_tolerance(): void
    {
        $result = app(ExerciseValidator::class)->validate([
            'type' => 'numeric',
            'validator' => ['type' => 'numeric', 'expected' => 120, 'tolerance' => 0.5],
        ], ['value' => 120.4]);

        $this->assertTrue($result->completed);
        $this->assertSame(1.0, $result->score);
    }

    public function test_result_validator_supports_unordered_rows_numeric_tolerance_and_nulls(): void
    {
        $result = app(ExerciseValidator::class)->validate([
            'type' => 'result_based',
            'validator' => [
                'type' => 'table',
                'expected_columns' => ['region', 'revenue'],
                'required_columns' => ['region'],
                'expected_rows' => [['South', 120.0], ['North', null]],
                'row_order' => 'unordered',
                'numeric_tolerance' => 0.5,
                'null_behavior' => 'strict',
            ],
        ], [
            'columns' => ['region', 'revenue'],
            'rows' => [['North', null], ['South', 120.4]],
        ]);

        $this->assertTrue($result->completed);
    }

    public function test_self_assessment_requires_reference_review_and_checklist_completion(): void
    {
        $exercise = [
            'type' => 'text_self_assessment',
            'checklist' => ['Metric jelas', 'Batas evidence disebutkan'],
        ];
        $validator = app(ExerciseValidator::class);

        $reviewed = $validator->validate($exercise, [
            'text' => 'Revenue September lebih tinggi.',
            'checklist' => [true, false],
            'complete' => false,
        ]);
        $completed = $validator->validate($exercise, [
            'text' => 'Revenue September lebih tinggi dan penyebab belum dapat ditentukan.',
            'checklist' => [true, true],
            'complete' => true,
        ]);

        $this->assertSame('reviewed', $reviewed->status);
        $this->assertFalse($reviewed->completed);
        $this->assertTrue($completed->completed);
    }

    public function test_content_config_validator_rejects_unknown_fields_and_malformed_numeric_config(): void
    {
        $validator = app(ExerciseConfigValidator::class);

        $this->expectException(ContentValidationException::class);
        $validator->normalize([
            'schema_version' => 1,
            'exercises' => [[
                'id' => 'numeric-01',
                'type' => 'numeric',
                'prompt' => 'Hitung.',
                'validator' => ['type' => 'numeric', 'expected' => 1, 'tolerance' => -1],
                'script' => 'alert(1)',
            ]],
        ], 'test/lesson');
    }

    public function test_authenticated_attempts_persist_retry_completion_and_reset(): void
    {
        $user = User::factory()->create();
        $exerciseKey = 'data-analyst/01-thinking-with-data/01-analyst-role/thinking-analyst-01';

        $this->actingAs($user)->postJson('/attempts/check', [
            'exercise_key' => $exerciseKey,
            'answer' => ['option' => 3],
        ])->assertOk()
            ->assertJsonPath('result.status', 'incorrect')
            ->assertJsonPath('attempt.status', 'incorrect');

        $this->actingAs($user)->postJson('/attempts/check', [
            'exercise_key' => $exerciseKey,
            'answer' => ['option' => 0],
        ])->assertOk()
            ->assertJsonPath('result.status', 'completed')
            ->assertJsonPath('attempt.status', 'completed');

        $this->actingAs($user)->postJson('/attempts/check', [
            'exercise_key' => $exerciseKey,
            'answer' => ['option' => 2],
        ])->assertOk()
            ->assertJsonPath('attempt.status', 'completed')
            ->assertJsonPath('attempt.attempts_count', 3);

        $this->actingAs($user)->get('/attempts?exercise_key='.urlencode($exerciseKey))
            ->assertOk()
            ->assertJsonPath('attempt.status', 'completed');

        $this->actingAs($user)->deleteJson('/attempts', ['exercise_key' => $exerciseKey])
            ->assertOk()
            ->assertJsonPath('reset', true);
        $this->assertDatabaseMissing('learning_attempts', [
            'user_id' => $user->id,
            'exercise_key' => $exerciseKey,
        ]);
    }

    public function test_attempts_are_scoped_to_the_authenticated_learner(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $exerciseKey = 'data-analyst/01-thinking-with-data/01-analyst-role/thinking-analyst-01';

        $this->actingAs($owner)->postJson('/attempts/check', [
            'exercise_key' => $exerciseKey,
            'answer' => ['option' => 0],
        ])->assertOk();

        $this->actingAs($other)->get('/attempts?exercise_key='.urlencode($exerciseKey))
            ->assertOk()
            ->assertJsonPath('attempt', null);

        $this->actingAs($other)->deleteJson('/attempts', ['exercise_key' => $exerciseKey])
            ->assertOk();

        $this->assertDatabaseHas('learning_attempts', [
            'user_id' => $owner->id,
            'exercise_key' => $exerciseKey,
            'status' => 'completed',
        ]);
    }

    public function test_attempt_payloads_are_bounded_before_persistence(): void
    {
        $user = User::factory()->create();
        $exerciseKey = 'data-analyst/01-thinking-with-data/01-analyst-role/thinking-analyst-01';

        $this->actingAs($user)->postJson('/attempts/check', [
            'exercise_key' => $exerciseKey,
            'answer' => ['text' => str_repeat('x', 70000)],
        ])->assertStatus(422)
            ->assertJsonValidationErrors('answer');
    }

    public function test_challenge_progress_aggregates_completed_registered_exercises(): void
    {
        $user = User::factory()->create();
        $exerciseKey = 'data-analyst/01-thinking-with-data/challenge/challenge-01-grain';

        $this->actingAs($user)->postJson('/attempts/check', [
            'exercise_key' => $exerciseKey,
            'answer' => ['option' => 0],
        ])->assertOk()
            ->assertJsonPath('attempt.status', 'completed');

        $this->actingAs($user)->get('/learn/data-analyst/01-thinking-with-data/challenge')
            ->assertOk()
            ->assertSee('1 / 7 latihan selesai', false)
            ->assertDontSee('Semua latihan challenge selesai', false);
    }
}
