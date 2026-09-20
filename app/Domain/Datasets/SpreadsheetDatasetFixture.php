<?php

namespace App\Domain\Datasets;

use Illuminate\Support\Facades\File;
use JsonException;
use RuntimeException;

final class SpreadsheetDatasetFixture
{
    public function __construct(private readonly DatasetManifestRepository $manifests) {}

    public function sourceHash(): string
    {
        return hash('sha256', json_encode($this->payload(), JSON_THROW_ON_ERROR));
    }

    /** @return array<string, mixed> */
    public function payload(): array
    {
        $contract = $this->manifests->load('nusamart', 'v1');
        $manifest = $contract['manifest'];
        $tables = [];

        foreach (['transactions', 'products'] as $tableKey) {
            $table = $manifest['tables'][$tableKey] ?? null;
            $relativeFile = $manifest['files'][$tableKey] ?? null;

            if (! is_array($table) || ! is_string($relativeFile) || str_contains($relativeFile, '..')) {
                throw new RuntimeException("Invalid spreadsheet table contract [{$tableKey}].");
            }

            $path = $contract['directory'].'/'.$relativeFile;

            try {
                $rows = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $exception) {
                throw new RuntimeException("Invalid spreadsheet fixture JSON [{$path}].", previous: $exception);
            }

            if (! is_array($rows) || ! array_is_list($rows)) {
                throw new RuntimeException("Spreadsheet table [{$tableKey}] must be a list of rows.");
            }

            $tables[$tableKey] = [
                'columns' => $table['columns'],
                'rows' => $rows,
            ];
        }

        return [
            'dataset_key' => $manifest['dataset_key'],
            'version' => $manifest['version'],
            'grain' => $manifest['grain'],
            'tables' => $tables,
        ];
    }
}
