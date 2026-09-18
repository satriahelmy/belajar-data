<?php

namespace Tests\Feature;

use App\Domain\Learning\Content\ContentValidationException;
use App\Domain\Learning\Content\MarkdownLessonRenderer;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ContentFoundationTest extends TestCase
{
    public function test_repository_lesson_renders_with_registered_block_and_heading_metadata(): void
    {
        $response = $this->get('/learn/data-analyst/01-thinking-with-data/01-analyst-role');

        $response
            ->assertOk()
            ->assertSee('What Does a Data Analyst Actually Do?')
            ->assertSee('<table', false)
            ->assertSee('<pre', false)
            ->assertSee('<a href="/learn">', false)
            ->assertSee('id="a-simple-workflow"', false)
            ->assertSee('learning-block--practice')
            ->assertSee('thinking-analyst-01')
            ->assertSee('common-mistake');

        $html = $response->getContent();
        $this->assertSame(1, preg_match_all('/<h1\b/i', $html));
        $this->assertStringNotContainsString('/favicon.ico', $html);
    }

    public function test_content_validation_command_passes_for_the_representative_slice(): void
    {
        $this->assertSame(0, Artisan::call('content:validate'));
    }

    public function test_render_cache_key_contains_the_repository_source_hash(): void
    {
        $rendered = app(MarkdownLessonRenderer::class)->render('01-thinking-with-data/01-analyst-role');

        $this->assertStringStartsWith('content:lesson:data-analyst:01-thinking-with-data/01-analyst-role:', $rendered->cacheKey);
        $this->assertMatchesRegularExpression('/:[a-f0-9]{64}$/', $rendered->cacheKey);
    }

    public function test_disposable_spike_route_is_not_part_of_the_product_surface(): void
    {
        $this->get('/__spike/lesson')->assertNotFound();
    }

    public function test_ordinary_lesson_does_not_load_the_sql_spike_runtime(): void
    {
        $this->get('/learn/data-analyst/01-thinking-with-data/01-analyst-role')
            ->assertOk()
            ->assertDontSee('sql-wasm')
            ->assertDontSee('sql-worker');
    }

    public function test_raw_html_and_unsafe_links_are_not_rendered_as_active_markup(): void
    {
        $source = "<script>alert('xss')</script>\n\n[unsafe](javascript:alert('xss'))";
        $renderer = app(MarkdownLessonRenderer::class);
        $method = new \ReflectionMethod($renderer, 'renderSource');

        $result = $method->invoke($renderer, new \App\Domain\Learning\Content\LessonSource(
            'test',
            $source,
            [],
            hash('sha256', $source),
        ));

        $this->assertStringNotContainsString('<script>', $result['html']);
        $this->assertStringNotContainsString('href="javascript:', $result['html']);
    }

    public function test_unknown_directive_is_rejected(): void
    {
        $renderer = app(MarkdownLessonRenderer::class);
        $method = new \ReflectionMethod($renderer, 'renderSource');

        $this->expectException(ContentValidationException::class);

        $method->invoke($renderer, new \App\Domain\Learning\Content\LessonSource(
            'test',
            ":::unknown\nbody\n:::",
            [],
            'test-unknown-directive',
        ));
    }

    public function test_missing_practice_reference_is_rejected_by_the_renderer(): void
    {
        $renderer = app(MarkdownLessonRenderer::class);
        $method = new \ReflectionMethod($renderer, 'renderSource');

        $this->expectException(ContentValidationException::class);

        $method->invoke($renderer, new \App\Domain\Learning\Content\LessonSource(
            'test',
            ":::practice type=\"multiple_choice\" id=\"missing\"\n:::",
            [],
            'test-missing-practice',
        ));
    }

    public function test_unknown_directive_attributes_are_rejected(): void
    {
        $renderer = app(MarkdownLessonRenderer::class);
        $method = new \ReflectionMethod($renderer, 'renderSource');

        $this->expectException(ContentValidationException::class);

        $method->invoke($renderer, new \App\Domain\Learning\Content\LessonSource(
            'test',
            ":::callout type=\"note\" onclick=\"alert(1)\"\nUnsafe\n:::",
            [],
            'test-invalid-attribute',
        ));
    }
}
