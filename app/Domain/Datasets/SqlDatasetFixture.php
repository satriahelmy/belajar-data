<?php

namespace App\Domain\Datasets;

use Illuminate\Support\Facades\File;
use JsonException;
use RuntimeException;

final class SqlDatasetFixture
{
    public function sourceHash(): string
    {
        return hash('sha256', json_encode($this->payload(), JSON_THROW_ON_ERROR));
    }

    /** @return array<string, mixed> */
    public function payload(): array
    {
        $root = rtrim((string) config('belajardata.datasets_path'), '/\\').'/nusamart/v1/sql';
        $manifest = $this->readJson($root.'/manifest.json');
        $dictionary = $this->readJson($root.'/data_dictionary.json');

        if (($manifest['schema_version'] ?? null) !== 1
            || ($manifest['dataset_key'] ?? null) !== 'nusamart'
            || ($manifest['version'] ?? null) !== 'v1'
            || ($manifest['profile'] ?? null) !== 'gate-a-sql-spike'
            || ! is_array($manifest['tables'] ?? null)
            || ($dictionary['schema_version'] ?? null) !== 1
            || ($dictionary['dataset_key'] ?? null) !== 'nusamart'
            || ($dictionary['version'] ?? null) !== 'v1'
            || ($dictionary['profile'] ?? null) !== 'gate-a-sql-spike'
            || ! is_array($dictionary['tables'] ?? null)
            || array_keys($dictionary['tables']) !== array_keys($manifest['tables'])
        ) {
            throw new RuntimeException('Invalid SQL fixture manifest or data dictionary.');
        }

        foreach ($manifest['tables'] as $tableKey => $table) {
            foreach ($table['columns'] as $columnKey => $type) {
                if (($dictionary['tables'][$tableKey]['columns'][$columnKey]['type'] ?? null) !== $type) {
                    throw new RuntimeException("SQL fixture data dictionary does not match [{$tableKey}.{$columnKey}].");
                }
            }
        }

        $tables = [];

        foreach ($manifest['tables'] as $tableKey => $table) {
            $rows = $this->readJson($root.'/'.$manifest['files'][$tableKey]);

            if (! array_is_list($rows)) {
                throw new RuntimeException("SQL fixture table [{$tableKey}] must be a list of rows.");
            }

            $tables[$tableKey] = [
                'columns' => $table['columns'],
                'rows' => $rows,
            ];
        }

        return [
            'dataset_key' => $manifest['dataset_key'],
            'version' => $manifest['version'],
            'profile' => $manifest['profile'],
            'grain' => $manifest['grain'],
            'relationships' => $manifest['relationships'],
            'tables' => $tables,
        ];
    }

    /** @return array<string, mixed>|list<mixed> */
    private function readJson(string $path): array
    {
        if (! File::exists($path)) {
            throw new RuntimeException("Missing SQL fixture file [{$path}].");
        }

        try {
            $value = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException("Invalid SQL fixture JSON [{$path}].", previous: $exception);
        }

        if (! is_array($value)) {
            throw new RuntimeException("SQL fixture JSON must decode to an array [{$path}].");
        }

        return $value;
    }
}
