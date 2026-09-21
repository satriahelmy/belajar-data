<?php

namespace App\Domain\Learning\Content;

use App\Domain\Datasets\SpreadsheetDatasetFixture;
use App\Domain\Datasets\SqlDatasetFixture;
use App\Domain\Datasets\PythonDatasetFixture;
use App\Domain\Datasets\VisualizationDatasetFixture;
use App\Domain\Datasets\JoinDatasetFixture;
use App\Domain\Datasets\SamplingDatasetFixture;
use App\Domain\Datasets\MetricTreeFixture;
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
        private readonly PythonDatasetFixture $pythonFixture,
        private readonly VisualizationDatasetFixture $visualizationFixture,
        private readonly JoinDatasetFixture $joinFixture,
        private readonly SamplingDatasetFixture $samplingFixture,
        private readonly MetricTreeFixture $metricTreeFixture,
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

        if (str_contains($source->markdown, ':::python-practice')) {
            $cacheKey .= ':python-'.$this->pythonFixture->sourceHash();
        }

        if (str_contains($source->markdown, ':::visualization')) {
            $cacheKey .= ':visualization-'.$this->visualizationFixture->sourceHash();
        }

        if (str_contains($source->markdown, ':::join-practice')) {
            $cacheKey .= ':join-'.$this->joinFixture->sourceHash();
        }

        if (str_contains($source->markdown, ':::sampling-practice')) {
            $cacheKey .= ':sampling-'.$this->samplingFixture->sourceHash();
        }

        if (str_contains($source->markdown, ':::metric-tree')) {
            $cacheKey .= ':metric-tree-'.$this->metricTreeFixture->sourceHash();
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
            'python-practice' => $this->renderPythonPractice($attributes, $body, $source),
            'visualization' => $this->renderVisualization($attributes, $body, $source),
            'join-practice' => $this->renderJoinPractice($attributes, $body, $source),
            'sampling-practice' => $this->renderSamplingPractice($attributes, $body, $source),
            'metric-tree' => $this->renderMetricTree($attributes, $body, $source),
            'communication-practice' => $this->renderCommunicationPractice($attributes, $body, $source),
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

    private function renderPythonPractice(array $attributes, string $body, LessonSource $source): string
    {
        $this->components->assertRegistered('python-practice');
        $this->assertOnlyAttributes($attributes, ['id'], 'python-practice');

        if ($body !== '') {
            throw new ContentValidationException('Python practice directives must not contain a body.');
        }

        $id = $attributes['id'] ?? '';

        if (! preg_match('/^[a-z0-9][a-z0-9-]*$/', $id)) {
            throw new ContentValidationException('Python practice directives require a safe id attribute.');
        }

        $exercise = $source->exercises[$id] ?? null;
        $interactive = is_array($exercise) ? ($exercise['interactive'] ?? null) : null;

        if (! is_array($exercise)
            || ($exercise['type'] ?? null) !== 'result_based'
            || ! is_array($interactive)
            || ($interactive['type'] ?? null) !== 'python_practice') {
            throw new ContentValidationException("Python practice [{$id}] is not registered with a result-based interactive config in [{$source->key}].");
        }

        $fixture = $this->pythonFixture->payload();

        if (($interactive['dataset_key'] ?? null) !== $fixture['dataset_key']
            || ($interactive['dataset_version'] ?? null) !== $fixture['version']) {
            throw new ContentValidationException("Python practice [{$id}] references an unavailable dataset.");
        }

        $publicConfig = [
            'id' => $id,
            'type' => 'python_practice',
            'exercise_key' => ($source->metadata['path_key'] ?? 'data-analyst').'/'.$source->key.'/'.$id,
            'prompt' => $exercise['prompt'],
            'mode' => $interactive['mode'],
            'starter_code' => $interactive['starter_code'],
            'required_operations' => $interactive['required_operations'],
            'output_metrics' => $interactive['output_metrics'] ?? [],
            'notebook_url' => $interactive['notebook_url'],
            'desktop_note' => $interactive['desktop_note'] ?? 'Untuk kode dan tabel hasil, layar desktop disarankan.',
            'validator' => $exercise['validator'],
            'incorrect_feedback' => $exercise['incorrect_feedback'] ?? null,
            'success_feedback' => $exercise['success_feedback'] ?? null,
            'fixture' => $fixture,
            'limits' => [
                'max_source_characters' => 8000,
                'max_output_characters' => 8000,
                'max_rows' => 20,
            ],
        ];

        $config = htmlspecialchars(
            json_encode($publicConfig, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES),
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8',
        );

        return '<section class="learning-block learning-block--python" '
            .'data-learning-component="python-practice" '
            .'data-python-exercise-id="'.e($id).'" '
            .'data-config="'.$config.'">'
            .'<p><strong>Python Practice:</strong> '.e($publicConfig['prompt']).'</p>'
            .'<div data-role="python-practice-mount"></div>'
            .'</section>';
    }

    private function renderVisualization(array $attributes, string $body, LessonSource $source): string
    {
        $this->components->assertRegistered('visualization-playground');
        $this->assertOnlyAttributes($attributes, ['id'], 'visualization');

        if ($body !== '') {
            throw new ContentValidationException('Visualization directives must not contain a body.');
        }

        $id = $attributes['id'] ?? '';

        if (! preg_match('/^[a-z0-9][a-z0-9-]*$/', $id)) {
            throw new ContentValidationException('Visualization directives require a safe id attribute.');
        }

        $exercise = $source->exercises[$id] ?? null;
        $interactive = is_array($exercise) ? ($exercise['interactive'] ?? null) : null;

        if (! is_array($exercise)
            || ($exercise['type'] ?? null) !== 'result_based'
            || ! is_array($interactive)
            || ($interactive['type'] ?? null) !== 'visualization_playground') {
            throw new ContentValidationException("Visualization [{$id}] is not registered with a result-based interactive config in [{$source->key}].");
        }

        $fixture = $this->visualizationFixture->payload();

        if (($interactive['dataset_key'] ?? null) !== $fixture['dataset_key']
            || ($interactive['dataset_version'] ?? null) !== $fixture['version']) {
            throw new ContentValidationException("Visualization [{$id}] references an unavailable dataset.");
        }

        $publicConfig = [
            'id' => $id,
            'type' => 'visualization_playground',
            'exercise_key' => ($source->metadata['path_key'] ?? 'data-analyst').'/'.$source->key.'/'.$id,
            'prompt' => $exercise['prompt'],
            'allowed_metrics' => $interactive['allowed_metrics'],
            'allowed_dimensions' => $interactive['allowed_dimensions'],
            'allowed_charts' => $interactive['allowed_charts'],
            'allowed_sorts' => $interactive['allowed_sorts'],
            'allowed_highlights' => $interactive['allowed_highlights'],
            'allowed_scale_modes' => $interactive['allowed_scale_modes'],
            'starter_metric' => $interactive['starter_metric'],
            'starter_dimension' => $interactive['starter_dimension'],
            'starter_chart' => $interactive['starter_chart'],
            'starter_sort' => $interactive['starter_sort'],
            'starter_highlight' => $interactive['starter_highlight'],
            'starter_scale_mode' => $interactive['starter_scale_mode'],
            'desktop_note' => $interactive['desktop_note'] ?? 'Layar desktop memberi ruang yang lebih nyaman untuk membaca visual dan tabel.',
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

        return '<section class="learning-block learning-block--visualization" '
            .'data-learning-component="visualization-playground" '
            .'data-visualization-exercise-id="'.e($id).'" '
            .'data-config="'.$config.'">'
            .'<p><strong>Visualization Practice:</strong> '.e($publicConfig['prompt']).'</p>'
            .$this->renderVisualizationFallback($fixture, $interactive)
            .'<div data-role="visualization-mount"></div>'
            .'</section>';
    }

    /** @param array<string, mixed> $fixture @param array<string, mixed> $interactive */
    private function renderVisualizationFallback(array $fixture, array $interactive): string
    {
        $dimension = $interactive['starter_dimension'];
        $metric = $interactive['starter_metric'];
        $rows = $fixture['views'][$dimension]['rows'] ?? [];
        $label = ucfirst(str_replace('_', ' ', $dimension));

        usort($rows, static function (array $left, array $right) use ($interactive, $metric): int {
            if (($interactive['starter_sort'] ?? null) === 'chronological') {
                return strcmp((string) $left['label'], (string) $right['label']);
            }

            $difference = (float) ($left[$metric] ?? 0) <=> (float) ($right[$metric] ?? 0);

            return ($interactive['starter_sort'] ?? 'descending') === 'ascending'
                ? $difference
                : ((-$difference) ?: strcmp((string) $left['label'], (string) $right['label']));
        });

        $html = '<div class="visualization-fallback" data-role="visualization-fallback">'
            .'<table><caption>'.e('Tabel angka yang mendasari visual: '.$label.' menurut '.$metric).'</caption><thead><tr>'
            .'<th scope="col">'.e($label).'</th><th scope="col">'.e(ucfirst(str_replace('_', ' ', $metric))).'</th><th scope="col">Orders</th>'
            .'</tr></thead><tbody>';

        foreach ($rows as $row) {
            $html .= '<tr><th scope="row">'.e((string) $row['label']).'</th><td>'.e((string) ($row[$metric] ?? '')).'</td><td>'.e((string) ($row['orders'] ?? '')).'</td></tr>';
        }

        return $html.'</tbody></table></div>';
    }

    private function renderJoinPractice(array $attributes, string $body, LessonSource $source): string
    {
        $this->components->assertRegistered('join-grain-playground');
        $this->assertOnlyAttributes($attributes, ['id'], 'join-practice');

        if ($body !== '') {
            throw new ContentValidationException('JOIN practice directives must not contain a body.');
        }

        $id = $attributes['id'] ?? '';

        if (! preg_match('/^[a-z0-9][a-z0-9-]*$/', $id)) {
            throw new ContentValidationException('JOIN practice directives require a safe id attribute.');
        }

        $exercise = $source->exercises[$id] ?? null;
        $interactive = is_array($exercise) ? ($exercise['interactive'] ?? null) : null;

        if (! is_array($exercise)
            || ($exercise['type'] ?? null) !== 'result_based'
            || ! is_array($interactive)
            || ($interactive['type'] ?? null) !== 'join_row_multiplication') {
            throw new ContentValidationException("JOIN practice [{$id}] is not registered with a result-based interactive config in [{$source->key}].");
        }

        $fixture = $this->joinFixture->payload();

        if (($interactive['dataset_key'] ?? null) !== $fixture['dataset_key']
            || ($interactive['dataset_version'] ?? null) !== $fixture['version']) {
            throw new ContentValidationException("JOIN practice [{$id}] references an unavailable dataset.");
        }

        $scenarios = array_intersect_key($fixture['scenarios'], array_flip($interactive['allowed_scenarios']));
        $publicConfig = [
            'id' => $id,
            'type' => 'join_row_multiplication',
            'exercise_key' => ($source->metadata['path_key'] ?? 'data-analyst').'/'.$source->key.'/'.$id,
            'prompt' => $exercise['prompt'],
            'allowed_scenarios' => array_values($interactive['allowed_scenarios']),
            'starter_scenario' => $interactive['starter_scenario'],
            'scenarios' => $scenarios,
            'desktop_note' => $interactive['desktop_note'] ?? 'Layar desktop memberi ruang yang lebih nyaman untuk membaca dua tabel dan hasil JOIN.',
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

        return '<section class="learning-block learning-block--join" '
            .'data-learning-component="join-grain-playground" '
            .'data-join-exercise-id="'.e($id).'" '
            .'data-config="'.$config.'">'
            .'<p><strong>JOIN Practice:</strong> '.e($publicConfig['prompt']).'</p>'
            .$this->renderJoinFallback($fixture, $interactive['starter_scenario'])
            .'<div data-role="join-grain-mount"></div>'
            .'</section>';
    }

    /** @param array<string, mixed> $fixture */
    private function renderJoinFallback(array $fixture, string $scenarioKey): string
    {
        $scenario = $fixture['scenarios'][$scenarioKey];
        $result = $this->joinRows($fixture, $scenario);
        $preview = array_slice($result['rows'], 0, 5);
        $html = '<div class="join-grain-fallback" data-role="join-grain-fallback">'
            .'<dl class="join-grain-fallback__summary">'
            .'<div><dt>LEFT ROWS</dt><dd>'.e((string) $result['left_rows']).'</dd></div>'
            .'<div><dt>RIGHT ROWS</dt><dd>'.e((string) $result['right_rows']).'</dd></div>'
            .'<div><dt>JOINED ROWS</dt><dd>'.e((string) $result['result_rows']).'</dd></div>'
            .'</dl><table><caption>Contoh hasil JOIN: '.e($scenario['label']).'</caption><thead><tr><th scope="col">Left key</th><th scope="col">Right key</th><th scope="col">Right quantity</th></tr></thead><tbody>';

        foreach ($preview as $row) {
            $html .= '<tr><th scope="row">'.e((string) ($row[0] ?? '')).'</th><td>'.e((string) ($row[1] ?? '')).'</td><td>'.e((string) ($row[2] ?? '')).'</td></tr>';
        }

        return $html.'</tbody></table><p class="join-grain-fallback__warning">'.e($scenario['warning']).'</p></div>';
    }

    /** @param array<string, mixed> $fixture @param array<string, mixed> $scenario @return array<string, mixed> */
    private function joinRows(array $fixture, array $scenario): array
    {
        $leftRows = $fixture['tables'][$scenario['left_table']]['rows'] ?? [];
        $rightRows = $fixture['tables'][$scenario['right_table']]['rows'] ?? [];
        $key = $scenario['join_key'];
        $rows = [];

        foreach ($leftRows as $left) {
            foreach ($rightRows as $right) {
                if (($left[$key] ?? null) === ($right[$key] ?? null) && ($left[$key] ?? null) !== null) {
                    $rows[] = [$left[$key], $right[$key], $right['quantity'] ?? null];
                }
            }
        }

        return [
            'left_rows' => count($leftRows),
            'right_rows' => count($rightRows),
            'result_rows' => count($rows),
            'rows' => $rows,
        ];
    }

    private function renderSamplingPractice(array $attributes, string $body, LessonSource $source): string
    {
        $this->components->assertRegistered('sampling-uncertainty-playground');
        $this->assertOnlyAttributes($attributes, ['id'], 'sampling-practice');

        if ($body !== '') {
            throw new ContentValidationException('Sampling practice directives must not contain a body.');
        }

        $id = $attributes['id'] ?? '';

        if (! preg_match('/^[a-z0-9][a-z0-9-]*$/', $id)) {
            throw new ContentValidationException('Sampling practice directives require a safe id attribute.');
        }

        $exercise = $source->exercises[$id] ?? null;
        $interactive = is_array($exercise) ? ($exercise['interactive'] ?? null) : null;

        if (! is_array($exercise)
            || ($exercise['type'] ?? null) !== 'result_based'
            || ! is_array($interactive)
            || ($interactive['type'] ?? null) !== 'sampling_uncertainty') {
            throw new ContentValidationException("Sampling practice [{$id}] is not registered with a result-based interactive config in [{$source->key}].");
        }

        $fixture = $this->samplingFixture->payload();

        if (($interactive['dataset_key'] ?? null) !== $fixture['dataset_key']
            || ($interactive['dataset_version'] ?? null) !== $fixture['version']) {
            throw new ContentValidationException("Sampling practice [{$id}] references an unavailable dataset.");
        }

        $publicConfig = [
            'id' => $id,
            'type' => 'sampling_uncertainty',
            'exercise_key' => ($source->metadata['path_key'] ?? 'data-analyst').'/'.$source->key.'/'.$id,
            'prompt' => $exercise['prompt'],
            'allowed_metrics' => $interactive['allowed_metrics'],
            'allowed_sample_sizes' => $interactive['allowed_sample_sizes'],
            'allowed_repeats' => $interactive['allowed_repeats'],
            'allowed_seeds' => $interactive['allowed_seeds'],
            'starter_metric' => $interactive['starter_metric'],
            'starter_sample_size' => $interactive['starter_sample_size'],
            'starter_repeats' => $interactive['starter_repeats'],
            'starter_seed' => $interactive['starter_seed'],
            'desktop_note' => $interactive['desktop_note'] ?? 'Layar desktop memberi ruang yang lebih nyaman untuk membandingkan beberapa estimasi sample.',
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

        return '<section class="learning-block learning-block--sampling" '
            .'data-learning-component="sampling-uncertainty-playground" '
            .'data-sampling-exercise-id="'.e($id).'" '
            .'data-config="'.$config.'">'
            .'<p><strong>Sampling Practice:</strong> '.e($publicConfig['prompt']).'</p>'
            .$this->renderSamplingFallback($fixture, $interactive)
            .'<div data-role="sampling-mount"></div>'
            .'</section>';
    }

    /** @param array<string, mixed> $fixture @param array<string, mixed> $interactive */
    private function renderSamplingFallback(array $fixture, array $interactive): string
    {
        $summary = $this->samplingFixture->summary(
            $interactive['starter_metric'],
            $interactive['starter_sample_size'],
            $interactive['starter_repeats'],
            $interactive['starter_seed'],
        );
        $html = '<div class="sampling-fallback" data-role="sampling-fallback">'
            .'<p class="sampling-fallback__summary">Population mean: <strong>'.e((string) $summary['population_mean']).'</strong> · Estimasi sample: <strong>'.e((string) $summary['estimate_min']).'</strong> sampai <strong>'.e((string) $summary['estimate_max']).'</strong></p>'
            .'<table><caption>Estimasi sample awal</caption><thead><tr><th scope="col">Repeat</th><th scope="col">Sample mean</th><th scope="col">Delta dari population mean</th></tr></thead><tbody>';

        foreach ($summary['estimates'] as $estimate) {
            $html .= '<tr><th scope="row">'.e((string) $estimate['repeat']).'</th><td>'.e((string) $estimate['sample_mean']).'</td><td>'.e((string) $estimate['delta']).'</td></tr>';
        }

        return $html.'</tbody></table></div>';
    }

    private function renderMetricTree(array $attributes, string $body, LessonSource $source): string
    {
        $this->components->assertRegistered('metric-tree-builder');
        $this->assertOnlyAttributes($attributes, ['id'], 'metric-tree');

        if ($body !== '') {
            throw new ContentValidationException('Metric tree directives must not contain a body.');
        }

        $id = $attributes['id'] ?? '';

        if (! preg_match('/^[a-z0-9][a-z0-9-]*$/', $id)) {
            throw new ContentValidationException('Metric tree directives require a safe id attribute.');
        }

        $exercise = $source->exercises[$id] ?? null;
        $interactive = is_array($exercise) ? ($exercise['interactive'] ?? null) : null;

        if (! is_array($exercise)
            || ($exercise['type'] ?? null) !== 'result_based'
            || ! is_array($interactive)
            || ($interactive['type'] ?? null) !== 'metric_tree_builder') {
            throw new ContentValidationException("Metric tree [{$id}] is not registered with a result-based interactive config in [{$source->key}].");
        }

        $fixture = $this->metricTreeFixture->payload();
        $tree = $fixture['trees'][$interactive['tree_key']] ?? null;

        if (! is_array($tree)
            || ($interactive['dataset_key'] ?? null) !== $fixture['dataset_key']
            || ($interactive['dataset_version'] ?? null) !== $fixture['version']
            || ($interactive['allowed_parent_options'] ?? null) != ($tree['parent_options'] ?? null)) {
            throw new ContentValidationException("Metric tree [{$id}] references an unavailable or mismatched tree fixture.");
        }

        $publicConfig = [
            'id' => $id,
            'type' => 'metric_tree_builder',
            'exercise_key' => ($source->metadata['path_key'] ?? 'data-analyst').'/'.$source->key.'/'.$id,
            'prompt' => $exercise['prompt'],
            'tree_key' => $interactive['tree_key'],
            'nodes' => $tree['nodes'],
            'root_id' => $tree['root_id'],
            'allowed_parent_options' => $interactive['allowed_parent_options'],
            'starter_parents' => $interactive['starter_parents'],
            'desktop_note' => $interactive['desktop_note'] ?? 'Layar desktop memberi ruang yang lebih nyaman untuk membaca hubungan antar-metric.',
            'validator' => $exercise['validator'],
            'incorrect_feedback' => $exercise['incorrect_feedback'] ?? null,
            'success_feedback' => $exercise['success_feedback'] ?? null,
        ];

        $config = htmlspecialchars(
            json_encode($publicConfig, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES),
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8',
        );

        return '<section class="learning-block learning-block--metric-tree" '
            .'data-learning-component="metric-tree-builder" '
            .'data-metric-tree-exercise-id="'.e($id).'" '
            .'data-config="'.$config.'">'
            .'<p><strong>Metric Tree Practice:</strong> '.e($publicConfig['prompt']).'</p>'
            .$this->renderMetricTreeFallback($tree, $interactive['starter_parents'])
            .'<div data-role="metric-tree-mount"></div>'
            .'</section>';
    }

    /** @param array<string, mixed> $tree @param array<string, string> $parents */
    private function renderMetricTreeFallback(array $tree, array $parents): string
    {
        $labels = [];

        foreach ($tree['nodes'] as $node) {
            $labels[$node['id']] = $node['label'];
        }

        $html = '<div class="metric-tree-fallback" data-role="metric-tree-fallback">'
            .'<table><caption>Susunan metric awal</caption><thead><tr><th scope="col">Metric</th><th scope="col">Parent</th></tr></thead><tbody>';

        foreach ($parents as $nodeId => $parentId) {
            $html .= '<tr><th scope="row">'.e((string) ($labels[$nodeId] ?? $nodeId)).'</th><td>'.e((string) ($labels[$parentId] ?? $parentId)).'</td></tr>';
        }

        return $html.'</tbody></table></div>';
    }

    private function renderCommunicationPractice(array $attributes, string $body, LessonSource $source): string
    {
        $this->components->assertRegistered('communication-builder');
        $this->assertOnlyAttributes($attributes, ['id'], 'communication-practice');

        if ($body !== '') {
            throw new ContentValidationException('Communication practice directives must not contain a body.');
        }

        $id = $attributes['id'] ?? '';

        if (! preg_match('/^[a-z0-9][a-z0-9-]*$/', $id)) {
            throw new ContentValidationException('Communication practice directives require a safe id attribute.');
        }

        $exercise = $source->exercises[$id] ?? null;
        $interactive = is_array($exercise) ? ($exercise['interactive'] ?? null) : null;

        if (! is_array($exercise)
            || ($exercise['type'] ?? null) !== 'text_self_assessment'
            || ! is_array($interactive)
            || ($interactive['type'] ?? null) !== 'communication_builder') {
            throw new ContentValidationException("Communication practice [{$id}] is not registered with a text self-assessment config in [{$source->key}].");
        }

        $publicConfig = [
            'id' => $id,
            'type' => 'communication_builder',
            'exercise_key' => ($source->metadata['path_key'] ?? 'data-analyst').'/'.$source->key.'/'.$id,
            'prompt' => $exercise['prompt'],
            'field_order' => $interactive['field_order'],
            'field_labels' => $interactive['field_labels'],
            'desktop_note' => $interactive['desktop_note'] ?? 'Layar desktop memberi ruang yang lebih nyaman untuk menyusun dan meninjau finding.',
            'reference_answer' => $exercise['reference_answer'],
            'checklist' => $exercise['checklist'],
        ];

        $config = htmlspecialchars(
            json_encode($publicConfig, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES),
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8',
        );

        return '<section class="learning-block learning-block--communication" '
            .'data-learning-component="communication-builder" '
            .'data-communication-exercise-id="'.e($id).'" '
            .'data-config="'.$config.'">'
            .'<p><strong>Communication Practice:</strong> '.e($publicConfig['prompt']).'</p>'
            .$this->renderCommunicationFallback($publicConfig)
            .'<div data-role="communication-mount"></div>'
            .'</section>';
    }

    /** @param array<string, mixed> $config */
    private function renderCommunicationFallback(array $config): string
    {
        $html = '<div class="communication-fallback" data-role="communication-fallback">'
            .'<p>Susun jawaban melalui lima bagian: </p><ol>';

        foreach ($config['field_order'] as $field) {
            $html .= '<li>'.e((string) $config['field_labels'][$field]).'</li>';
        }

        $html .= '</ol><details><summary>Reference answer dan checklist</summary>'
            .'<p>'.e((string) $config['reference_answer']).'</p><ul>';

        foreach ($config['checklist'] as $item) {
            $html .= '<li>'.e((string) $item).'</li>';
        }

        return $html.'</ul></details></div>';
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
