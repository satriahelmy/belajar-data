<?php

namespace App\Domain\Datasets;

use Illuminate\Support\Facades\File;
use RuntimeException;

final class PythonDatasetFixture
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

        foreach ([
            'transactions' => 'python_transactions',
            'products' => 'python_products',
        ] as $tableKey => $fileKey) {
            $relativeFile = $manifest['files'][$fileKey] ?? null;
            $table = $manifest['tables'][$tableKey] ?? null;

            if (! is_string($relativeFile)
                || str_contains($relativeFile, '..')
                || ! is_array($table)
                || ! is_array($table['columns'] ?? null)) {
                throw new RuntimeException("Invalid Python fixture contract [{$tableKey}].");
            }

            $path = $contract['directory'].'/'.$relativeFile;
            if (! File::exists($path)) {
                throw new RuntimeException("Missing Python fixture [{$path}].");
            }

            $lines = preg_split('/\r?\n/', trim(File::get($path))) ?: [];
            $header = str_getcsv((string) array_shift($lines));

            if ($header === [] || count(array_unique($header)) !== count($header)) {
                throw new RuntimeException("Invalid Python fixture header [{$path}].");
            }

            $rows = [];
            foreach ($lines as $line) {
                if (trim($line) === '') {
                    continue;
                }

                $values = str_getcsv($line);
                if (count($values) !== count($header)) {
                    throw new RuntimeException("Invalid Python fixture row [{$path}].");
                }

                $row = [];
                foreach ($header as $index => $column) {
                    $value = $values[$index] ?? null;
                    $type = $table['columns'][$column] ?? 'string';
                    $row[$column] = match ($type) {
                        'integer' => (int) $value,
                        'number' => (float) $value,
                        default => $value,
                    };
                }
                $rows[] = $row;
            }

            $columns = [];
            foreach ($header as $column) {
                $columns[$column] = $table['columns'][$column] ?? 'string';
            }

            $tables[$tableKey] = [
                'columns' => $columns,
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
