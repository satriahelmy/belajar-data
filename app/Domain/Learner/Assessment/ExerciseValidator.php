<?php

namespace App\Domain\Learner\Assessment;

final class ExerciseValidator
{
    /**
     * @param  array<string, mixed>  $exercise
     * @param  array<string, mixed>  $answer
     */
    public function validate(array $exercise, array $answer): ExerciseValidationResult
    {
        return match ($exercise['type'] ?? null) {
            'multiple_choice' => $this->multipleChoice($exercise, $answer),
            'multi_select' => $this->multiSelect($exercise, $answer),
            'numeric' => $this->numeric($exercise, $answer),
            'text_self_assessment' => $this->selfAssessment($exercise, $answer),
            'result_based' => $this->resultBased($exercise, $answer),
            default => new ExerciseValidationResult(false, false, 'invalid', null, 'Latihan ini belum dapat divalidasi.', []),
        };
    }

    /** @param array<string, mixed> $exercise @param array<string, mixed> $answer */
    private function multipleChoice(array $exercise, array $answer): ExerciseValidationResult
    {
        $option = $answer['option'] ?? null;

        if (! is_int($option)) {
            return $this->incomplete([], 'Pilih satu jawaban terlebih dahulu.');
        }

        $valid = $option === $exercise['correct_option'];

        return $this->deterministic($valid, ['option' => $option], $exercise);
    }

    /** @param array<string, mixed> $exercise @param array<string, mixed> $answer */
    private function multiSelect(array $exercise, array $answer): ExerciseValidationResult
    {
        $options = $answer['options'] ?? null;

        if (! is_array($options) || $options === [] || count(array_filter($options, 'is_int')) !== count($options)) {
            return $this->incomplete([], 'Pilih jawaban yang menurutmu tepat.');
        }

        $options = array_values(array_unique($options));
        $expected = array_values(array_unique($exercise['correct_options']));
        sort($options);
        sort($expected);

        return $this->deterministic($options === $expected, ['options' => $options], $exercise);
    }

    /** @param array<string, mixed> $exercise @param array<string, mixed> $answer */
    private function numeric(array $exercise, array $answer): ExerciseValidationResult
    {
        $value = $answer['value'] ?? null;
        $validator = $exercise['validator'];

        if (! is_int($value) && ! is_float($value) && ! (is_string($value) && is_numeric($value))) {
            return $this->incomplete([], 'Masukkan angka sebelum mengecek jawaban.');
        }

        $value = (float) $value;
        $valid = abs($value - (float) $validator['expected']) <= (float) $validator['tolerance'];

        return $this->deterministic($valid, ['value' => $value], $exercise);
    }

    /** @param array<string, mixed> $exercise @param array<string, mixed> $answer */
    private function selfAssessment(array $exercise, array $answer): ExerciseValidationResult
    {
        $text = $answer['text'] ?? '';
        $checklist = $answer['checklist'] ?? [];
        $fields = $answer['fields'] ?? null;

        if (! is_string($text) || trim($text) === '') {
            return $this->incomplete([], 'Tulis jawabanmu terlebih dahulu.');
        }

        if ($fields !== null
            && (! is_array($fields)
                || count($fields) > 5
                || count(array_filter($fields, static fn (mixed $value, mixed $key): bool => is_string($key) && preg_match('/^[a-z][a-z_]*$/', $key) && is_string($value) && mb_strlen($value) <= 2000, ARRAY_FILTER_USE_BOTH)) !== count($fields))) {
            return $this->incomplete([], 'Lengkapi field komunikasi sebelum mengecek jawaban.');
        }

        if (is_array($fields)
            && (count($fields) === 0
                || count(array_filter($fields, static fn (mixed $value): bool => trim((string) $value) !== '')) !== count($fields))) {
            return $this->incomplete([], 'Lengkapi field komunikasi sebelum mengecek jawaban.');
        }

        $normalized = [
            'text' => mb_substr(trim($text), 0, 5000),
            'checklist' => is_array($checklist)
                ? array_values(array_map(static fn (mixed $value): bool => $value === true, $checklist))
                : [],
            'complete' => ($answer['complete'] ?? false) === true,
        ];

        if (is_array($fields)) {
            $normalized['fields'] = array_map(static fn (mixed $value): string => mb_substr(trim((string) $value), 0, 2000), $fields);
        }

        $fieldsComplete = ! is_array($fields) || count($fields) > 0 && count(array_filter($fields, static fn (mixed $value): bool => trim((string) $value) !== '')) === count($fields);
        $normalized['complete'] = $normalized['complete'] && $fieldsComplete;
        $completed = $normalized['complete'] && count($exercise['checklist']) === count($normalized['checklist']) && ! in_array(false, $normalized['checklist'], true);

        return new ExerciseValidationResult(
            valid: true,
            completed: $completed,
            status: $completed ? 'completed' : 'reviewed',
            score: null,
            feedback: $completed ? 'Latihan selesai.' : 'Cocokkan jawabanmu dengan reference answer dan checklist.',
            answer: $normalized,
        );
    }

    /** @param array<string, mixed> $exercise @param array<string, mixed> $answer */
    private function resultBased(array $exercise, array $answer): ExerciseValidationResult
    {
        $columns = $answer['columns'] ?? null;
        $rows = $answer['rows'] ?? null;

        if (! is_array($columns) || ! is_array($rows)) {
            return $this->incomplete([], 'Masukkan hasil tabel sebelum mengecek jawaban.');
        }

        $validator = $exercise['validator'];
        $valid = $this->tableMatches($validator, $columns, $rows);

        return $this->deterministic($valid, [
            'columns' => array_values(array_filter($columns, 'is_string')),
            'rows' => $rows,
        ], $exercise);
    }

    /** @param array<string, mixed> $exercise @param array<string, mixed> $answer */
    private function deterministic(bool $valid, array $answer, array $exercise): ExerciseValidationResult
    {
        return new ExerciseValidationResult(
            valid: $valid,
            completed: $valid,
            status: $valid ? 'completed' : 'incorrect',
            score: $valid ? 1.0 : 0.0,
            feedback: $valid
                ? ($exercise['success_feedback'] ?? 'Benar. Hubungkan jawabanmu dengan alasan di lesson.')
                : ($exercise['incorrect_feedback'] ?? 'Belum tepat. Periksa kembali konsep yang sedang diuji.'),
            answer: $answer,
        );
    }

    /** @param array<string, mixed> $answer */
    private function incomplete(array $answer, string $feedback): ExerciseValidationResult
    {
        return new ExerciseValidationResult(false, false, 'incomplete', null, $feedback, $answer);
    }

    /** @param array<string, mixed> $validator @param array<int, mixed> $columns @param array<int, mixed> $rows */
    private function tableMatches(array $validator, array $columns, array $rows): bool
    {
        if (count(array_filter($columns, 'is_string')) !== count($columns)) {
            return false;
        }

        $expectedColumns = $validator['expected_columns'];
        $requiredColumns = $validator['required_columns'] ?? [];

        if ($columns !== $expectedColumns || array_diff($requiredColumns, $columns) !== []) {
            return false;
        }

        $expectedRows = $validator['expected_rows'];
        if (count($rows) !== count($expectedRows)) {
            return false;
        }

        if (count(array_filter($rows, 'is_array')) !== count($rows)) {
            return false;
        }

        if (($validator['row_order'] ?? 'ordered') === 'unordered') {
            $rows = $this->sortRows($rows);
            $expectedRows = $this->sortRows($expectedRows);
        }

        foreach ($expectedRows as $index => $expectedRow) {
            if (! is_array($rows[$index] ?? null) || count($rows[$index]) !== count($expectedRow)) {
                return false;
            }

            foreach ($expectedRow as $column => $expectedValue) {
                if (! $this->cellMatches($expectedValue, $rows[$index][$column] ?? null, $validator)) {
                    return false;
                }
            }
        }

        return true;
    }

    /** @param array<int, mixed> $rows @return array<int, array<int, mixed>> */
    private function sortRows(array $rows): array
    {
        usort($rows, static fn (array $left, array $right): int => strcmp(json_encode($left), json_encode($right)));

        return $rows;
    }

    /** @param array<string, mixed> $validator */
    private function cellMatches(mixed $expected, mixed $actual, array $validator): bool
    {
        if ($expected === null || $actual === null) {
            if (($validator['null_behavior'] ?? 'strict') === 'empty_as_null') {
                return ($expected === null && ($actual === null || $actual === ''))
                    || ($actual === null && ($expected === null || $expected === ''));
            }

            return $expected === $actual;
        }

        if (is_numeric($expected) && is_numeric($actual)) {
            return abs((float) $expected - (float) $actual) <= (float) ($validator['numeric_tolerance'] ?? 0);
        }

        return mb_strtolower(trim((string) $expected)) === mb_strtolower(trim((string) $actual));
    }
}
