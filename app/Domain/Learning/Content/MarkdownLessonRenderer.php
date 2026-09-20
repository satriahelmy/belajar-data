<?php

namespace App\Domain\Learning\Content;

use App\Domain\Datasets\SpreadsheetDatasetFixture;
use App\Domain\Datasets\SqlDatasetFixture;
use Illuminate\Support\Facades\Cache;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use DOMDocument;
use DOMXPath;

class MarkdownLessonRenderer
{
    private const ALLOWED_CALLOUT_TYPES = ['note', 'important', 'common-mistake', 'warning'];

    public function __construct(
        private readonly ContentRepository $repository,
        private readonly DirectiveParser $directives,
        private readonly ComponentRegistry $components,
        private readonly SqlDatasetFixture $sqlFixture,
        private readonly SpreadsheetDatasetFixture $spreadsheetFixture,
    ) {
    }

    public function render(string $key, string $pathKey = 'data-analyst'): RenderedLesson
    {
        $source = $this->repository->lesson($key, $pathKey);
        return $this->renderCached($source, $pathKey);
    }

    public function renderChallenge(string $moduleKey, string $pathKey = 'data-analyst'): RenderedLesson
    {
        return $this->renderCached($this->repository->challenge($moduleKey, $pathKey), $pathKey);
    }

    private function renderCached(LessonSource $source, string $pathKey): RenderedLesson
    {
        $cacheKey = "content:lesson:{$pathKey}:{$source->key}:{$source->sourceHash}";

        if (str_contains($source->markdown, ':::sql-playground')) {
            $cacheKey .= ':sql-'.$this->sqlFixture->sourceHash();
        }

        if (str_contains($source->markdown, ':::spreadsheet-playground')) {
            $cacheKey .= ':spreadsheet-'.$this->spreadsheetFixture->sourceHash();
        }

        $ttlDays = max(1, (int) config('belajardata.content_cache_ttl_days', 1));
        $payload = Cache::remember($cacheKey, now()->addDays($ttlDays), function () use ($source): array {
            return $this->renderSource($source);
        });

        return new RenderedLesson($payload['html'], $payload['headings'], $cacheKey);
    }

    /**
     * @return array{html: string, headings: list<array{level: int, text: string, slug: string}>}
     */
    private function renderSource(LessonSource $source): array
    {
        $processed = $this->directives->replace(
            $source->markdown,
            fn (string $name, array $attributes, string $body): string => $this->renderDirective($name, $attributes, $body, $source),
        );

        $converter = new GithubFlavoredMarkdownConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        $html = (string) $converter->convert($processed['markdown']);

        foreach ($processed['blocks'] as $token => $blockHtml) {
            $html = preg_replace(
                '/<p>'.preg_quote($token, '/').'\s*<\/p>/u',
                $blockHtml,
                $html,
                1,
            ) ?? $html;
        }

        return [
            'html' => $this->addHeadingIds($html),
            'headings' => $this->extractHeadings($html),
        ];
    }

    private function addHeadingIds(string $html): string
    {
        $slugCounts = [];

        return preg_replace_callback('/<h([1-6])>(.*?)<\/h\\1>/is', function (array $matches) use (&$slugCounts): string {
            $text = trim(strip_tags($matches[2]));
            $baseSlug = trim((string) preg_replace('/[^a-z0-9]+/i', '-', strtolower($text)), '-');
            $baseSlug = $baseSlug !== '' ? $baseSlug : 'section';
            $slugCounts[$baseSlug] = ($slugCounts[$baseSlug] ?? 0) + 1;
            $slug = $slugCounts[$baseSlug] === 1 ? $baseSlug : $baseSlug.'-'.$slugCounts[$baseSlug];

            return '<h'.$matches[1].' id="'.e($slug).'">'.$matches[2].'</h'.$matches[1].'>';
        }, $html) ?? $html;
    }

    private function renderDirective(string $name, array $attributes, string $body, LessonSource $source): string
    {
        return match ($name) {
            'callout' => $this->renderCallout($attributes, $body),
            'practice' => $this->renderPractice($attributes, $body, $source),
            'sql-playground' => $this->renderSqlPlayground($attributes, $body, $source),
            'spreadsheet-playground' => $this->renderSpreadsheetPlayground($attributes, $body, $source),
            default => throw new ContentValidationException("Unknown directive [{$name}] in [{$source->key}]."),
        };
    }

    private function renderCallout(array $attributes, string $body): string
    {
        $this->assertOnlyAttributes($attributes, ['type'], 'callout');
        $type = $attributes['type'] ?? 'note';

        if (! in_array($type, self::ALLOWED_CALLOUT_TYPES, true)) {
            throw new ContentValidationException("Unsupported callout type [{$type}].");
        }

        if ($body === '') {
            throw new ContentValidationException('Callout directives must contain text.');
        }

        return '<aside class="learning-callout" data-callout-type="'.e($type).'">'
            .'<strong>'.e(ucwords(str_replace('-', ' ', $type))).'</strong>'
            .'<p>'.nl2br(e($body), false).'</p>'
            .'</aside>';
    }

    private function renderPractice(array $attributes, string $body, LessonSource $source): string
    {
        $this->components->assertRegistered('practice');
        $this->assertOnlyAttributes($attributes, ['type', 'id'], 'practice');

        if ($body !== '') {
            throw new ContentValidationException('Practice directives must not contain a body.');
        }

        $id = $attributes['id'] ?? '';
        $type = $attributes['type'] ?? '';

        if (! preg_match('/^[a-z0-9][a-z0-9-]*$/', $id) || ! preg_match('/^[a-z0-9][a-z0-9_-]*$/', $type)) {
            throw new ContentValidationException('Practice directives require safe type and id attributes.');
        }

        $exercise = $source->exercises[$id] ?? null;

        if (! is_array($exercise)) {
            throw new ContentValidationException("Practice [{$id}] is not registered in [{$source->key}].");
        }

        if (($exercise['type'] ?? null) !== $type) {
            throw new ContentValidationException("Practice [{$id}] type does not match its registered config.");
        }

        $publicConfig = [
            'id' => $id,
            'type' => $type,
            'exercise_key' => ($source->metadata['path_key'] ?? 'data-analyst').'/'.$source->key.'/'.$id,
            'prompt' => $exercise['prompt'] ?? '',
            'options' => $exercise['options'] ?? [],
            'correct_option' => $exercise['correct_option'] ?? null,
            'correct_options' => $exercise['correct_options'] ?? [],
            'reference_answer' => $exercise['reference_answer'] ?? null,
            'checklist' => $exercise['checklist'] ?? [],
            'hints' => $exercise['hints'] ?? [],
            'incorrect_feedback' => $exercise['incorrect_feedback'] ?? null,
            'success_feedback' => $exercise['success_feedback'] ?? null,
            'validator' => $exercise['validator'] ?? null,
        ];

        $config = htmlspecialchars(
            json_encode($publicConfig, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES),
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8',
        );

        return '<section class="learning-block learning-block--practice" '
            .'data-learning-component="practice" '
            .'data-practice-id="'.e($id).'" '
            .'data-config="'.$config.'">'
            .'<p><strong>Practice:</strong> '.e($publicConfig['prompt']).'</p>'
            .'<div data-role="practice-mount"></div>'
            .'</section>';
    }

    private function renderSqlPlayground(array $attributes, string $body, LessonSource $source): string
    {
        $this->components->assertRegistered('sql-playground');
        $this->assertOnlyAttributes($attributes, ['id'], 'sql-playground');

        if ($body !== '') {
            throw new ContentValidationException('SQL playground directives must not contain a body.');
        }

        $id = $attributes['id'] ?? '';

        if (! preg_match('/^[a-z0-9][a-z0-9-]*$/', $id)) {
            throw new ContentValidationException('SQL playground directives require a safe id attribute.');
        }

        $exercise = $source->exercises[$id] ?? null;

        if (! is_array($exercise) || ($exercise['type'] ?? null) !== 'result_based') {
            throw new ContentValidationException("SQL playground [{$id}] is not registered as a result-based exercise in [{$source->key}].");
        }

        $interactive = $exercise['interactive'] ?? null;

        if (! is_array($interactive) || ($interactive['type'] ?? null) !== 'sql_playground') {
            throw new ContentValidationException("SQL playground [{$id}] is missing its interactive configuration.");
        }

        $fixture = $this->sqlFixture->payload();

        if (($interactive['dataset_key'] ?? null) !== $fixture['dataset_key']
            || ($interactive['dataset_version'] ?? null) !== $fixture['version']) {
            throw new ContentValidationException("SQL playground [{$id}] references an unavailable dataset.");
        }

        $publicConfig = [
            'id' => $id,
            'type' => 'sql_playground',
            'exercise_key' => ($source->metadata['path_key'] ?? 'data-analyst').'/'.$source->key.'/'.$id,
            'prompt' => $exercise['prompt'],
            'starter_query' => $interactive['starter_query'],
            'desktop_note' => $interactive['desktop_note'] ?? 'Untuk latihan SQL, layar desktop memberi ruang yang lebih nyaman untuk schema dan query.',
            'validator' => $exercise['validator'],
            'incorrect_feedback' => $exercise['incorrect_feedback'] ?? null,
            'success_feedback' => $exercise['success_feedback'] ?? null,
            'fixture' => $fixture,
            'limits' => [
                'max_rows' => 100,
                'max_query_characters' => 10000,
                'timeout_ms' => 2000,
            ],
        ];

        $config = htmlspecialchars(
            json_encode($publicConfig, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES),
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8',
        );

        return '<section class="learning-block learning-block--sql" '
            .'data-learning-component="sql-playground" '
            .'data-sql-exercise-id="'.e($id).'" '
            .'data-config="'.$config.'">'
            .'<p><strong>SQL Playground:</strong> '.e($publicConfig['prompt']).'</p>'
            .'<div data-role="sql-playground-mount"></div>'
            .'</section>';
    }

    private function renderSpreadsheetPlayground(array $attributes, string $body, LessonSource $source): string
    {
        $this->components->assertRegistered('spreadsheet-playground');
        $this->assertOnlyAttributes($attributes, ['id'], 'spreadsheet-playground');

        if ($body !== '') {
            throw new ContentValidationException('Spreadsheet playground directives must not contain a body.');
        }

        $id = $attributes['id'] ?? '';

        if (! preg_match('/^[a-z0-9][a-z0-9-]*$/', $id)) {
            throw new ContentValidationException('Spreadsheet playground directives require a safe id attribute.');
        }

        $exercise = $source->exercises[$id] ?? null;
        $interactive = is_array($exercise) ? ($exercise['interactive'] ?? null) : null;

        if (! is_array($exercise)
            || ($exercise['type'] ?? null) !== 'result_based'
            || ! is_array($interactive)
            || ($interactive['type'] ?? null) !== 'spreadsheet_playground') {
            throw new ContentValidationException("Spreadsheet playground [{$id}] is not registered with a result-based interactive config in [{$source->key}].");
        }

        $fixture = $this->spreadsheetFixture->payload();

        if (($interactive['dataset_key'] ?? null) !== $fixture['dataset_key']
            || ($interactive['dataset_version'] ?? null) !== $fixture['version']) {
            throw new ContentValidationException("Spreadsheet playground [{$id}] references an unavailable dataset.");
        }

        $publicConfig = [
            'id' => $id,
            'type' => 'spreadsheet_playground',
            'exercise_key' => ($source->metadata['path_key'] ?? 'data-analyst').'/'.$source->key.'/'.$id,
            'prompt' => $exercise['prompt'],
            'mode' => $interactive['mode'],
            'starter_cells' => $interactive['starter_cells'] ?? [],
            'editable_cells' => $interactive['editable_cells'] ?? array_keys($interactive['starter_cells'] ?? []),
            'cell_labels' => $interactive['cell_labels'] ?? [],
            'starter_filter' => $interactive['starter_filter'] ?? 'all',
            'starter_sort' => $interactive['starter_sort'] ?? 'order_id_asc',
            'view_columns' => $interactive['view_columns'] ?? array_keys($fixture['tables']['transactions']['columns']),
            'summary_dimension' => $interactive['summary_dimension'] ?? null,
            'summary_measures' => $interactive['summary_measures'] ?? [],
            'desktop_note' => $interactive['desktop_note'] ?? 'Untuk latihan spreadsheet, layar desktop memberi ruang yang lebih nyaman untuk data dan rumus.',
            'validator' => $exercise['validator'],
            'incorrect_feedback' => $exercise['incorrect_feedback'] ?? null,
            'success_feedback' => $exercise['success_feedback'] ?? null,
            'fixture' => $fixture,
        ];

        $config = htmlspecialchars(
            json_encode($publicConfig, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES),
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8',
        );

        return '<section class="learning-block learning-block--spreadsheet" '
            .'data-learning-component="spreadsheet-playground" '
            .'data-spreadsheet-exercise-id="'.e($id).'" '
            .'data-config="'.$config.'">'
            .'<p><strong>Spreadsheet Practice:</strong> '.e($publicConfig['prompt']).'</p>'
            .'<div data-role="spreadsheet-playground-mount"></div>'
            .'</section>';
    }

    /**
     * @param array<string, string> $attributes
     * @param list<string> $allowed
     */
    private function assertOnlyAttributes(array $attributes, array $allowed, string $directive): void
    {
        $unknown = array_diff(array_keys($attributes), $allowed);

        if ($unknown !== []) {
            $attribute = array_values($unknown)[0];

            throw new ContentValidationException("Unknown attribute [{$attribute}] on [{$directive}] directive.");
        }
    }

    /**
     * @return list<array{level: int, text: string, slug: string}>
     */
    private function extractHeadings(string $html): array
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $document->loadHTML('<!doctype html><html><body>'.$html.'</body></html>', LIBXML_NONET);
        libxml_clear_errors();

        $xpath = new DOMXPath($document);
        $headings = [];
        $slugCounts = [];

        foreach ($xpath->query('//h1|//h2|//h3|//h4|//h5|//h6') ?: [] as $heading) {
            $text = trim($heading->textContent);
            $baseSlug = trim((string) preg_replace('/[^a-z0-9]+/i', '-', strtolower($text)), '-');
            $baseSlug = $baseSlug !== '' ? $baseSlug : 'section';
            $slugCounts[$baseSlug] = ($slugCounts[$baseSlug] ?? 0) + 1;
            $slug = $slugCounts[$baseSlug] === 1 ? $baseSlug : $baseSlug.'-'.$slugCounts[$baseSlug];

            $headings[] = [
                'level' => (int) substr($heading->nodeName, 1),
                'text' => $text,
                'slug' => $slug,
            ];
        }

        return $headings;
    }
}
