<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProjectLearningTest extends TestCase
{
    use DatabaseTransactions;

    public function test_published_project_library_and_roadmap_project_render(): void
    {
        $this->get('/projects')
            ->assertOk()
            ->assertSee('NusaMart Revenue Slowdown')
            ->assertSee('Customer Retention Analysis')
            ->assertSee('Delivery Performance Investigation')
            ->assertSee('2 launch projects')
            ->assertSee('Roadmap');

        $this->get('/projects/nusamart-revenue-slowdown')
            ->assertOk()
            ->assertSee('Enam stage kerja')
            ->assertSee('The Brief')
            ->assertSee('Reference Approach');

        $this->get('/projects/delivery-performance-investigation')->assertNotFound();
    }

    public function test_project_stage_is_repository_backed_and_reference_is_confirmed_separately(): void
    {
        $this->get('/projects/nusamart-revenue-slowdown/brief')
            ->assertOk()
            ->assertSee('NusaMart melihat revenue')
            ->assertSee('Checkpoint')
            ->assertSee('Pertanyaan awal mana yang paling siap');

        $this->get('/projects/nusamart-revenue-slowdown/reference')
            ->assertOk()
            ->assertSee('Sudah punya rencana sendiri?')
            ->assertDontSee('Revenue naik 150');

        $this->post('/projects/nusamart-revenue-slowdown/reference/unlock')
            ->assertRedirect('/projects/nusamart-revenue-slowdown/reference');

        $this->get('/projects/nusamart-revenue-slowdown/reference')
            ->assertOk()
            ->assertSee('Revenue naik 150');
    }

    public function test_project_progress_is_independent_and_idempotent(): void
    {
        $user = User::factory()->create();

        $this->post('/projects/nusamart-revenue-slowdown/start')
            ->assertRedirect('/login');

        $this->actingAs($user)->post('/projects/nusamart-revenue-slowdown/start')
            ->assertRedirect('/projects/nusamart-revenue-slowdown/brief');

        $this->assertDatabaseHas('user_project_progress', [
            'user_id' => $user->id,
            'project_key' => 'nusamart-revenue-slowdown',
            'status' => 'started',
            'current_stage_key' => 'brief',
        ]);

        $this->actingAs($user)->post('/projects/nusamart-revenue-slowdown/brief/check', [
            'option' => 1,
        ])->assertRedirect('/projects/nusamart-revenue-slowdown/brief');

        $this->assertDatabaseHas('learning_attempts', [
            'user_id' => $user->id,
            'exercise_key' => 'project/nusamart-revenue-slowdown/brief/brief-question',
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('user_project_progress', [
            'user_id' => $user->id,
            'project_key' => 'nusamart-revenue-slowdown',
            'current_stage_key' => 'understand',
        ]);

        $this->actingAs($user)->post('/projects/nusamart-revenue-slowdown/start')
            ->assertRedirect('/projects/nusamart-revenue-slowdown/brief');

        $this->assertDatabaseCount('user_project_progress', 1);
    }

    public function test_project_checkpoint_persists_numeric_and_guided_self_assessment_answers(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/projects/nusamart-revenue-slowdown/start');
        $this->actingAs($user)->post('/projects/nusamart-revenue-slowdown/investigate/check', [
            'value' => '600',
        ])->assertRedirect('/projects/nusamart-revenue-slowdown/investigate');

        $this->assertDatabaseHas('learning_attempts', [
            'user_id' => $user->id,
            'exercise_key' => 'project/nusamart-revenue-slowdown/investigate/september-revenue',
            'status' => 'completed',
        ]);

        $this->actingAs($user)->post('/projects/nusamart-revenue-slowdown/plan/check', [
            'text' => 'Bandingkan dua bulan, breakdown region, dan cek totalnya.',
            'checklist' => ['0' => '1', '1' => '1', '2' => '1'],
            'complete' => '1',
        ])->assertRedirect('/projects/nusamart-revenue-slowdown/plan');

        $this->assertDatabaseHas('learning_attempts', [
            'user_id' => $user->id,
            'exercise_key' => 'project/nusamart-revenue-slowdown/plan/analysis-plan',
            'status' => 'completed',
        ]);
    }
}
