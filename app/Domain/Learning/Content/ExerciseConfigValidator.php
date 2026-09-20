<?php

namespace App\Domain\Learning\Content;

final class ExerciseConfigValidator
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public function normalize(mixed $decoded, string $lessonKey): array
    {
        if (! is_array($decoded)
            || ($decoded['schema_version'] ?? null) !== 1
            || ! isset($decoded['exercises'])
            || ! is_array($decoded['exercises'])
        ) {
            throw new ContentValidationException("Exercise config for [{$lessonKey}] must contain an exercises array.");
        }

        $exercises = [];

        foreach ($decoded['exercises'] as $exercise) {
            $this->assertBaseShape($exercise, $lessonKey);
            $id = $exercise['id'];

            if (isset($exercises[$id])) {
                throw new ContentValidationException("Duplicate exercise id [{$id}] in [{$lessonKey}].");
            }

            $exercises[$id] = $this->normalizeExercise($exercise, $lessonKey);
        }

        return $exercises;
    }

    private function assertBaseShape(mixed $exercise, string $lessonKey): void
    {
        if (! is_array($exercise)
            || ! is_string($exercise['id'] ?? null)
            || ! is_string($exercise['type'] ?? null)
            || ! is_string($exercise['prompt'] ?? null)
        ) {
            throw new ContentValidationException("Every exercise in [{$lessonKey}] needs a string id and type.");
        }

        if (! preg_match('/^[a-z0-9][a-z0-9-]*$/', $exercise['id'])) {
            throw new ContentValidationException("Exercise id [{$exercise['id']}] in [{$lessonKey}] is invalid.");
        }

        if (trim($exercise['prompt']) === '') {
            throw new ContentValidationException("Invalid exercise configuration for [{$lessonKey}].");
        }

        $allowed = [
            'id', 'type', 'prompt', 'options', 'correct_option', 'correct_options',
            'reference_answer', 'checklist', 'hints', 'incorrect_feedback',
            'success_feedback', 'validator', 'interactive',
        ];
        $unknown = array_values(array_diff(array_keys($exercise), $allowed));

        if ($unknown !== []) {
            throw new ContentValidationException("Unknown exercise field [{$unknown[0]}] in [{$lessonKey}].");
        }
    }

    /** @param array<string, mixed> $exercise @return array<string, mixed> */
    private function normalizeExercise(array $exercise, string $lessonKey): array
    {
        $type = $exercise['type'];

        if (isset($exercise['hints'])) {
            $this->assertStringList($exercise['hints'], 'hints', $lessonKey, 3);
        }

        foreach (['incorrect_feedback', 'success_feedback'] as $field) {
            if (isset($exercise[$field]) && (! is_string($exercise[$field]) || trim($exercise[$field]) === '')) {
                throw new ContentValidationException("Invalid {$field} in [{$lessonKey}].");
            }
        }

        if ($type === 'multiple_choice') {
            $this->assertOptions($exercise, $lessonKey);

            if (! isset($exercise['correct_option'])
                || ! is_int($exercise['correct_option'])
                || $exercise['correct_option'] < 0
                || $exercise['correct_option'] >= count($exercise['options'])) {
                throw new ContentValidationException("Invalid multiple-choice exercise configuration for [{$lessonKey}].");
            }
        } elseif ($type === 'multi_select') {
            $this->assertOptions($exercise, $lessonKey);

            if (! is_array($exercise['correct_options'] ?? null)
                || $exercise['correct_options'] === []
                || count(array_filter($exercise['correct_options'], 'is_int')) !== count($exercise['correct_options'])
                || count(array_unique($exercise['correct_options'])) !== count($exercise['correct_options'])
                || count(array_diff($exercise['correct_options'], range(0, count($exercise['options']) - 1))) !== 0) {
                throw new ContentValidationException("Invalid multi-select exercise configuration for [{$lessonKey}].");
            }
        } elseif ($type === 'numeric') {
            $this->assertNumericValidator($exercise['validator'] ?? null, $lessonKey);
        } elseif ($type === 'result_based') {
            $this->assertTableValidator($exercise['validator'] ?? null, $lessonKey);
        } elseif ($type === 'text_self_assessment') {
            if (! is_string($exercise['reference_answer'] ?? null)
                || trim($exercise['reference_answer']) === ''
                || ! is_array($exercise['checklist'] ?? null)
                || $exercise['checklist'] === []
                || count(array_filter($exercise['checklist'], 'is_string')) !== count($exercise['checklist'])) {
                throw new ContentValidationException("Invalid self-assessment exercise configuration for [{$lessonKey}].");
            }
        } else {
            throw new ContentValidationException("Unsupported exercise type [{$type}] for [{$lessonKey}].");
        }

        if (isset($exercise['interactive'])) {
            $this->assertInteractiveConfig($exercise['interactive'], $lessonKey);
        }

        return $exercise;
    }

    private function assertInteractiveConfig(mixed $interactive, string $lessonKey): void
    {
        if (! is_array($interactive)
            || ! is_string($interactive['type'] ?? null)
            || ! is_string($interactive['dataset_key'] ?? null)
            || ! preg_match('/^[a-z0-9][a-z0-9-]*$/', $interactive['dataset_key'])
            || ! is_string($interactive['dataset_version'] ?? null)
            || ! preg_match('/^[a-z0-9][a-z0-9-]*$/', $interactive['dataset_version'])) {
            throw new ContentValidationException("Invalid interactive configuration for [{$lessonKey}].");
        }

        $type = $interactive['type'];

        if ($type === 'sql_playground') {
            if (! is_string($interactive['starter_query'] ?? null) || trim($interactive['starter_query']) === '') {
                throw new ContentValidationException("Invalid interactive configuration for [{$lessonKey}].");
            }

            $allowed = ['type', 'dataset_key', 'dataset_version', 'starter_query', 'desktop_note'];
        } elseif ($type === 'spreadsheet_playground') {
            $this->assertSpreadsheetInteractiveConfig($interactive, $lessonKey);
            $allowed = [
                'type', 'dataset_key', 'dataset_version', 'mode', 'starter_cells', 'editable_cells',
                'cell_labels', 'starter_filter', 'starter_sort', 'view_columns', 'summary_dimension',
                'summary_measures', 'desktop_note',
            ];
        } else {
            throw new ContentValidationException("Unsupported interactive type [{$type}] for [{$lessonKey}].");
        }

        $unknown = array_values(array_diff(array_keys($interactive), $allowed));

        if ($unknown !== []) {
            throw new ContentValidationException("Unknown interactive field [{$unknown[0]}] in [{$lessonKey}].");
        }

        if (isset($interactive['desktop_note'])
            && (! is_string($interactive['desktop_note']) || trim($interactive['desktop_note']) === '')) {
            throw new ContentValidationException("Invalid desktop_note in [{$lessonKey}].");
        }
    }

    /** @param array<string, mixed> $interactive */
    private function assertSpreadsheetInteractiveConfig(array $interactive, string $lessonKey): void
    {
        if (! in_array($interactive['mode'] ?? null, ['formula', 'table', 'summary'], true)) {
            throw new ContentValidationException("Invalid spreadsheet mode in [{$lessonKey}].");
        }

        if (isset($interactive['starter_cells'])) {
            if (! is_array($interactive['starter_cells']) || count($interactive['starter_cells']) > 24) {
                throw new ContentValidationException("Invalid starter_cells in [{$lessonKey}].");
            }

            foreach ($interactive['starter_cells'] as $cell => $value) {
                if (! is_string($cell) || ! preg_match('/^[A-Z]{1,3}[1-9][0-9]*$/', $cell)
                    || ! is_string($value) || mb_strlen($value) > 1000) {
                    throw new ContentValidationException("Invalid starter cell in [{$lessonKey}].");
                }
            }
        }

        if (isset($interactive['editable_cells'])
            && (! is_array($interactive['editable_cells'])
                || count(array_filter($interactive['editable_cells'], static fn (mixed $cell): bool => is_string($cell) && preg_match('/^[A-Z]{1,3}[1-9][0-9]*$/', $cell))) !== count($interactive['editable_cells']))) {
            throw new ContentValidationException("Invalid editable_cells in [{$lessonKey}].");
        }

        if (isset($interactive['cell_labels']) && (! is_array($interactive['cell_labels'])
            || count(array_filter($interactive['cell_labels'], static fn (mixed $label): bool => is_string($label) && trim($label) !== '')) !== count($interactive['cell_labels']))) {
            throw new ContentValidationException("Invalid cell_labels in [{$lessonKey}].");
        }

        if (isset($interactive['starter_filter']) && (! is_string($interactive['starter_filter']) || trim($interactive['starter_filter']) === '')) {
            throw new ContentValidationException("Invalid starter_filter in [{$lessonKey}].");
        }

        if (isset($interactive['starter_sort']) && ! in_array($interactive['starter_sort'], [
            'order_id_asc', 'order_date_asc', 'order_date_desc', 'revenue_asc', 'revenue_desc', 'quantity_desc',
        ], true)) {
            throw new ContentValidationException("Invalid starter_sort in [{$lessonKey}].");
        }

        if (isset($interactive['view_columns']) && (! is_array($interactive['view_columns'])
            || $interactive['view_columns'] === []
            || count(array_filter($interactive['view_columns'], static fn (mixed $column): bool => is_string($column) && preg_match('/^[a-z][a-z0-9_]*$/', $column))) !== count($interactive['view_columns']))) {
            throw new ContentValidationException("Invalid view_columns in [{$lessonKey}].");
        }

        if (($interactive['mode'] ?? null) === 'summary') {
            if (($interactive['summary_dimension'] ?? null) !== 'category'
                || ! is_array($interactive['summary_measures'] ?? null)
                || $interactive['summary_measures'] === []
                || count(array_filter($interactive['summary_measures'], static fn (mixed $measure): bool => in_array($measure, ['revenue', 'quantity', 'order_count'], true))) !== count($interactive['summary_measures'])) {
                throw new ContentValidationException("Invalid summary configuration in [{$lessonKey}].");
            }
        }
    }

    /** @param array<string, mixed> $exercise */
    private function assertOptions(array $exercise, string $lessonKey): void
    {
        if (! is_array($exercise['options'] ?? null)
            || count($exercise['options']) < 2
            || count(array_filter($exercise['options'], static fn (mixed $option): bool => is_string($option) && trim($option) !== '')) !== count($exercise['options'])) {
            throw new ContentValidationException("Invalid options in [{$lessonKey}].");
        }
    }

    private function assertStringList(mixed $value, string $field, string $lessonKey, int $max): void
    {
        if (! is_array($value)
            || $value === []
            || count($value) > $max
            || count(array_filter($value, static fn (mixed $item): bool => is_string($item) && trim($item) !== '')) !== count($value)) {
            throw new ContentValidationException("Invalid {$field} in [{$lessonKey}].");
        }
    }

    private function assertNumericValidator(mixed $validator, string $lessonKey): void
    {
        if (! is_array($validator)
            || ($validator['type'] ?? null) !== 'numeric'
            || ! is_numeric($validator['expected'] ?? null)
            || ! is_numeric($validator['tolerance'] ?? null)
            || (float) $validator['tolerance'] < 0) {
            throw new ContentValidationException("Invalid numeric validator in [{$lessonKey}].");
        }
    }

    private function assertTableValidator(mixed $validator, string $lessonKey): void
    {
        if (! is_array($validator)
            || ($validator['type'] ?? null) !== 'table'
            || ! is_array($validator['expected_columns'] ?? null)
            || $validator['expected_columns'] === []
            || count(array_filter($validator['expected_columns'], 'is_string')) !== count($validator['expected_columns'])
            || ! is_array($validator['expected_rows'] ?? null)
            || ! in_array($validator['row_order'] ?? 'ordered', ['ordered', 'unordered'], true)) {
            throw new ContentValidationException("Invalid table validator in [{$lessonKey}].");
        }

        foreach ($validator['expected_rows'] as $row) {
            if (! is_array($row) || count($row) !== count($validator['expected_columns'])) {
                throw new ContentValidationException("Invalid expected rows in [{$lessonKey}].");
            }
        }

        if (isset($validator['required_columns'])
            && (! is_array($validator['required_columns'])
                || count(array_filter($validator['required_columns'], 'is_string')) !== count($validator['required_columns']))) {
            throw new ContentValidationException("Invalid required_columns in [{$lessonKey}].");
        }

        if (isset($validator['numeric_tolerance']) && (! is_numeric($validator['numeric_tolerance']) || (float) $validator['numeric_tolerance'] < 0)) {
            throw new ContentValidationException("Invalid numeric_tolerance in [{$lessonKey}].");
        }

        if (isset($validator['null_behavior']) && ! in_array($validator['null_behavior'], ['strict', 'empty_as_null'], true)) {
            throw new ContentValidationException("Invalid null_behavior in [{$lessonKey}].");
        }
    }
}
