<?php

namespace Tests\Feature;

use App\Domain\Learning\Content\ContentRepository;
use App\Domain\Learning\Content\ContentValidationException;
use App\Domain\Learning\Content\MarkdownLessonRenderer;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ContentSpikeTest extends TestCase
{
    public function test_repository_lesson_renders_with_registered_block_and_heading_metadata(): void
    {
        $response = $this->get('/__spike/lesson');

        $response
            ->assertOk()
            ->assertSee('What Does a Data Analyst Actually Do?')
            ->assertSee('<table', false)
            ->assertSee('<pre', false)
            ->assertSee('<a href="https://example.com/curriculum">', false)
            ->assertSee('<img src="/favicon.ico"', false)
            ->assertSee('learning-block--practice')
            ->assertSee('thinking-analyst-01')
            ->assertSee('On this page')
            ->assertSee('common-mistake');
    }

    public function test_content_validation_command_passes_for_the_representative_slice(): void
    {
        $this->assertSame(0, Artisan::call('content:validate'));
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

    public function test_unknown_directive_and_missing_practice_reference_are_rejected(): void
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
