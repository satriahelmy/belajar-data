<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
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

        $this->postJson('/progress/merge-guest', [
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

    public function test_authenticated_learner_can_toggle_a_published_topic_bookmark(): void
    {
        $user = User::factory()->create();
        $payload = [
            'content_type' => 'topic',
            'content_key' => 'data-analyst/01-thinking-with-data/01-analyst-role',
        ];

        $this->actingAs($user)->postJson('/bookmarks/toggle', $payload)
            ->assertOk()
            ->assertJsonPath('bookmarked', true);
        $this->assertDatabaseHas('bookmarks', [
            'user_id' => $user->id,
            ...$payload,
        ]);

        $this->actingAs($user)->postJson('/bookmarks/toggle', $payload)
            ->assertOk()
            ->assertJsonPath('bookmarked', false);
        $this->assertDatabaseMissing('bookmarks', [
            'user_id' => $user->id,
            ...$payload,
        ]);
    }

    public function test_bookmark_rejects_unknown_topic_and_other_content_types(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson('/bookmarks/toggle', [
            'content_type' => 'javascript',
            'content_key' => 'data-analyst/01-thinking-with-data/01-analyst-role',
        ])->assertStatus(422);

        $this->actingAs($user)->postJson('/bookmarks/toggle', [
            'content_type' => 'topic',
            'content_key' => 'data-analyst/01-thinking-with-data/not-a-topic',
        ])->assertStatus(422);
    }

    public function test_progress_page_exposes_recent_topics_and_bookmarks(): void
    {
        $user = User::factory()->create();
        $contentKey = 'data-analyst/01-thinking-with-data/01-analyst-role';

        $this->actingAs($user)->postJson('/progress/topics/start', ['content_key' => $contentKey])
            ->assertOk();
        $this->actingAs($user)->postJson('/bookmarks/toggle', [
            'content_type' => 'topic',
            'content_key' => $contentKey,
        ])->assertOk();

        $this->actingAs($user)->get('/progress')
            ->assertOk()
            ->assertSee('Terakhir dipelajari')
            ->assertSee('What Does a Data Analyst Actually Do?')
            ->assertSee('Bookmark')
            ->assertSee('Hapus');
    }

    public function test_existing_account_can_login_and_merge_guest_progress(): void
    {
        $user = User::factory()->create([
            'email' => 'merge-login@example.test',
            'password' => Hash::make('password123'),
        ]);
        $contentKey = 'data-analyst/01-thinking-with-data/01-analyst-role';

        $this->post('/login', [
            'email' => 'merge-login@example.test',
            'password' => 'password123',
        ])->assertRedirect('/learn');

        $this->actingAs($user)->postJson('/progress/merge-guest', [
            'topics' => [
                ['content_key' => $contentKey, 'status' => 'completed'],
            ],
        ])->assertOk()
            ->assertJsonPath('topics.'.$contentKey.'.status', 'completed');

        $this->assertDatabaseHas('user_topic_progress', [
            'user_id' => $user->id,
            'content_key' => $contentKey,
            'status' => 'completed',
        ]);
    }

    public function test_new_account_can_register_and_merge_guest_progress(): void
    {
        $contentKey = 'data-analyst/01-thinking-with-data/02-business-to-data-question';

        $this->post('/register', [
            'name' => 'New Learner',
            'email' => 'merge-register@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect('/learn');

        $this->postJson('/progress/merge-guest', [
            'topics' => [
                ['content_key' => $contentKey, 'status' => 'started'],
            ],
        ])->assertOk()
            ->assertJsonPath('topics.'.$contentKey.'.status', 'started');

        $this->assertDatabaseHas('user_topic_progress', [
            'content_key' => $contentKey,
            'status' => 'started',
        ]);
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
