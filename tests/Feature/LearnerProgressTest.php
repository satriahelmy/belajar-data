<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LearnerProgressTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_cannot_write_account_progress(): void
    {
        $this->postJson('/progress/topics/start', [
            'content_key' => 'data-analyst/01-thinking-with-data/01-analyst-role',
        ])->assertUnauthorized();
    }

    public function test_topic_progress_is_idempotent_and_completion_wins(): void
    {
        $user = User::factory()->create();
        $payload = ['content_key' => 'data-analyst/01-thinking-with-data/01-analyst-role'];

        $this->actingAs($user)->postJson('/progress/topics/start', $payload)
            ->assertOk()
            ->assertJsonPath('topic.status', 'started');
        $this->actingAs($user)->postJson('/progress/topics/start', $payload)->assertOk();
        $this->actingAs($user)->postJson('/progress/topics/complete', $payload)
            ->assertOk()
            ->assertJsonPath('topic.status', 'completed');
        $this->actingAs($user)->postJson('/progress/topics/start', $payload)
            ->assertOk()
            ->assertJsonPath('topic.status', 'completed');

        $this->assertDatabaseCount('user_topic_progress', 1);
        $this->assertDatabaseHas('user_topic_progress', [
            'user_id' => $user->id,
            'content_key' => $payload['content_key'],
            'status' => 'completed',
        ]);
    }

    public function test_guest_progress_merge_does_not_downgrade_completed_topics(): void
    {
        $user = User::factory()->create();
        $completedKey = 'data-analyst/01-thinking-with-data/02-business-to-data-question';

        $this->actingAs($user)->postJson('/progress/topics/complete', ['content_key' => $completedKey])
            ->assertOk();

        $this->actingAs($user)->postJson('/progress/merge-guest', [
            'topics' => [
                [
                    'content_key' => 'data-analyst/01-thinking-with-data/01-analyst-role',
                    'status' => 'started',
                ],
                [
                    'content_key' => $completedKey,
                    'status' => 'started',
                ],
                [
                    'content_key' => 'data-analyst/01-thinking-with-data/03-data-tables',
                    'status' => 'completed',
                ],
            ],
        ])->assertOk()
            ->assertJsonPath('topics.'.$completedKey.'.status', 'completed')
            ->assertJsonPath('topics.data-analyst/01-thinking-with-data/03-data-tables.status', 'completed');

        $this->assertDatabaseCount('user_topic_progress', 3);
    }

    public function test_progress_rejects_unknown_topics(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson('/progress/topics/start', [
            'content_key' => 'data-analyst/01-thinking-with-data/not-a-topic',
        ])->assertStatus(422);
    }

    public function test_learner_can_register_and_log_out(): void
    {
        $this->post('/register', [
            'name' => 'Helmy',
            'email' => 'helmy-progress@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect('/learn');

        $this->assertAuthenticated();

        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }
}
