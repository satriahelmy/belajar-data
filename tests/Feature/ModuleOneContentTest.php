<?php

namespace Tests\Feature;

use App\Domain\Learning\Content\ContentRepository;
use App\Domain\Learning\Content\MarkdownLessonRenderer;
use App\Domain\Learning\CurriculumRepository;
use Tests\TestCase;

class ModuleOneContentTest extends TestCase
{
    public function test_module_one_uses_the_canonical_topic_sequence(): void
    {
        $module = app(CurriculumRepository::class)->module('01-thinking-with-data');

        $this->assertSame(
            [
                '01-analyst-role',
                '02-business-to-data-question',
                '03-data-tables',
                '04-metrics-dimensions',
                '05-granularity',
                '06-aggregation-comparison',
                '07-data-to-insight',
            ],
            array_column($module['topics'], 'key'),
        );
        $this->assertSame('published', $module['challenge']['status']);
        $this->assertCount(7, $module['topics']);
    }

    public function test_module_one_topics_render_as_server_content_with_registered_practice_blocks(): void
    {
        $topics = app(CurriculumRepository::class)->module('01-thinking-with-data')['topics'];

        foreach ($topics as $topic) {
            $this->get('/learn/data-analyst/01-thinking-with-data/'.$topic['key'])
                ->assertOk()
                ->assertSee('learning-block--practice')
                ->assertSee('data-learning-component="practice"', false);
        }
    }

    public function test_practice_shell_exposes_a_progressive_mount_contract_for_current_content(): void
    {
        $response = $this->get('/learn/data-analyst/01-thinking-with-data/01-analyst-role');

        $response->assertOk()
            ->assertSee('data-learning-component="practice"', false)
            ->assertSee('data-role="practice-mount"', false)
            ->assertSee('<strong>Practice:</strong>', false);
    }

    public function test_module_one_lessons_have_a_real_learning_progression_and_real_section_navigation(): void
    {
        $module = app(CurriculumRepository::class)->module('01-thinking-with-data');
        $content = app(ContentRepository::class);
        $renderer = app(MarkdownLessonRenderer::class);

        $this->assertSame(104, $module['estimated_minutes']);
        $this->assertSame(
            [10, 13, 12, 10, 12, 14, 13],
            array_column($module['topics'], 'estimated_minutes'),
        );

        $exerciseTypes = [];

        foreach ($module['topics'] as $topic) {
            $source = $content->lesson('01-thinking-with-data/'.$topic['key']);
            $rendered = $renderer->render('01-thinking-with-data/'.$topic['key']);
            $levelTwoHeadings = array_filter($rendered->headings, static fn (array $heading): bool => $heading['level'] === 2);

            $this->assertGreaterThanOrEqual(3, count($levelTwoHeadings), $topic['key'].' needs meaningful section beats.');
            $this->assertGreaterThanOrEqual(2, count($source->exercises), $topic['key'].' needs more than one reasoning opportunity.');
            $exerciseTypes = [...$exerciseTypes, ...array_column($source->exercises, 'type')];
        }

        $this->assertContains('text_self_assessment', $exerciseTypes);
    }

    public function test_module_one_output_has_no_implementation_copy_and_navigation_uses_real_headings(): void
    {
        $module = app(CurriculumRepository::class)->module('01-thinking-with-data');
        $renderer = app(MarkdownLessonRenderer::class);
        $bannedCopy = [
            'progressive enhancement',
            'latihan siap dikerjakan di browser',
            'jawaban tidak disimpan ke server',
            'component registered',
            'server-rendered',
            'server rendered',
            'persistence',
            'renderer',
        ];

        foreach ($module['topics'] as $topic) {
            $response = $this->get('/learn/data-analyst/01-thinking-with-data/'.$topic['key']);
            $html = $response->getContent();
            $visibleText = strtolower((string) preg_replace('/\s+/', ' ', strip_tags($html)));
            $rendered = $renderer->render('01-thinking-with-data/'.$topic['key']);

            $response->assertOk()->assertSee('<strong>Practice:</strong>', false);

            foreach ($bannedCopy as $phrase) {
                $this->assertStringNotContainsString($phrase, $visibleText, $topic['key'].' exposes implementation copy.');
            }

            foreach ($rendered->headings as $heading) {
                $this->assertSame(
                    2,
                    substr_count($html, 'href="#'.$heading['slug'].'"'),
                    $topic['key'].' section navigation must match real headings.',
                );
            }
        }

        $challenge = $this->get('/learn/data-analyst/01-thinking-with-data/challenge');
        $challengeText = strtolower((string) preg_replace('/\s+/', ' ', strip_tags($challenge->getContent())));

        foreach ($bannedCopy as $phrase) {
            $this->assertStringNotContainsString($phrase, $challengeText, 'Challenge exposes implementation copy.');
        }
    }

    public function test_module_one_challenge_evidence_matches_the_canonical_fixture(): void
    {
        $rows = json_decode(
            file_get_contents(base_path('datasets/nusamart/v1/transactions.json')),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );
        $revenueByMonth = [];

        foreach ($rows as $row) {
            $month = substr($row['order_date'], 0, 7);
            $revenueByMonth[$month] = ($revenueByMonth[$month] ?? 0) + $row['revenue'];
        }

        $this->assertSame(450, $revenueByMonth['2025-08']);
        $this->assertSame(600, $revenueByMonth['2025-09']);

        $challenge = app(CurriculumRepository::class)->module('01-thinking-with-data')['challenge'];
        $source = file_get_contents(base_path('content/data-analyst/01-thinking-with-data/'.$challenge['exercise_file']));
        $this->assertStringContainsString('sebesar 600', $source);
        $this->assertStringContainsString('sebesar 450', $source);
    }
}
