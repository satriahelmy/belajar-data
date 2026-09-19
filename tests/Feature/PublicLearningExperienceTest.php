<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicLearningExperienceTest extends TestCase
{
    public function test_homepage_is_the_public_learning_entry_point(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Belajar data, tanpa bingung mulai dari mana.')
            ->assertSee('Learn')
            ->assertSee('Explore Skills')
            ->assertSee('Projects')
            ->assertSee('Mulai belajar')
            ->assertSee('Pertanyaan')
            ->assertSee('transactions')
            ->assertSee('JOIN products')
            ->assertSee('data-nav-toggle', false)
            ->assertSee('aria-label="Navigasi utama"', false)
            ->assertDontSee('fake learner counts');
    }

    public function test_learn_page_preserves_the_five_phase_and_module_order(): void
    {
        $response = $this->get('/learn');

        $response
            ->assertOk()
            ->assertSee('Foundations')
            ->assertSee('Working with Data')
            ->assertSee('From Analysis to Decision')
            ->assertSee('Business Impact')
            ->assertSee('Apply Your Skills')
            ->assertSee('01')
            ->assertSee('13')
            ->assertSee('Urutan ini adalah rekomendasi, bukan kunci.')
            ->assertSee('path-rail', false)
            ->assertSee('status-dot', false)
            ->assertSee('Roadmap');

        $this->assertLessThan(
            strpos($response->getContent(), 'Spreadsheet for Analysis'),
            strpos($response->getContent(), 'Thinking with Data'),
        );
    }

    public function test_published_module_is_open_without_prerequisite_lock(): void
    {
        $this->get('/learn/01-thinking-with-data')
            ->assertOk()
            ->assertSee('Thinking with Data')
            ->assertSee('What Does a Data Analyst Actually Do?')
            ->assertSee('Understanding Data &amp; Tables', false)
            ->assertSee('Mulai belajar')
            ->assertSee('Module berikutnya')
            ->assertSee('Spreadsheet for Analysis')
            ->assertSee('Urutan path adalah rekomendasi, bukan prerequisite lock');
    }

    public function test_unpublished_module_and_unknown_topic_are_not_publicly_available(): void
    {
        $this->get('/learn/02-spreadsheet-for-analysis')->assertNotFound();
        $this->get('/learn/data-analyst/01-thinking-with-data/not-a-topic')->assertNotFound();
    }

    public function test_lesson_has_canonical_context_navigation_and_further_reading(): void
    {
        $this->get('/learn/data-analyst/01-thinking-with-data/01-analyst-role')
            ->assertOk()
            ->assertSee('Module 01')
            ->assertSee('Topic 1 dari 7')
            ->assertSee('Lesson section navigation')
            ->assertSee('lesson-mobile-nav', false)
            ->assertSee('aria-current="page"', false)
            ->assertSee('Further Reading')
            ->assertSee('/learn/data-analyst/01-thinking-with-data/02-business-to-data-question', false)
            ->assertSee('Lanjut ke topic berikutnya');

        $this->get('/learn/data-analyst/01-thinking-with-data/02-business-to-data-question')
            ->assertOk()
            ->assertSee('Topic 2 dari 7')
            ->assertSee('What Does a Data Analyst Actually Do?')
            ->assertSee('Di halaman ini')
            ->assertSee('Lanjut ke topic berikutnya');
    }

    public function test_module_one_challenge_is_available_after_topic_sequence(): void
    {
        $this->get('/learn/01-thinking-with-data')
            ->assertOk()
            ->assertSee('NusaMart Sales Drop Investigation')
            ->assertSee('Buka challenge');

        $this->get('/learn/data-analyst/01-thinking-with-data/challenge')
            ->assertOk()
            ->assertSee('NusaMart Sales Drop Investigation')
            ->assertSee('September lebih tinggi daripada Agustus')
            ->assertSee('Kriteria selesai');
    }

    public function test_explore_skills_points_back_to_canonical_path_content(): void
    {
        $this->get('/skills')
            ->assertOk()
            ->assertSee('Explore Skills')
            ->assertSee('Analytical Thinking')
            ->assertSee('/learn/data-analyst/01-thinking-with-data/01-analyst-role', false)
            ->assertSee('#module-02-spreadsheet-for-analysis', false)
            ->assertSee('bukan curriculum kedua');
    }

    public function test_projects_is_a_public_future_destination_without_a_workspace(): void
    {
        $this->get('/projects')
            ->assertOk()
            ->assertSee('Projects')
            ->assertSee('NusaMart Revenue Slowdown')
            ->assertSee('workspace belum dibuka');
    }
}
