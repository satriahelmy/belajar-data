<?php

namespace App\Domain\Datasets;

use App\Domain\Learning\Content\MachineKey;
use Illuminate\Support\Facades\File;
use JsonException;

class DatasetManifestRepository
{
    /**
     * @return array<string, mixed>
     */
    public function load(string $datasetKey, string $version = 'v1'): array
    {
        try {
            MachineKey::assert($datasetKey, 'dataset key');
            MachineKey::assertVersion($version);
        } catch (\Throwable $exception) {
            throw new DatasetValidationException($exception->getMessage(), previous: $exception);
        }

        $directory = rtrim((string) config('belajardata.datasets_path'), '/\\')."/{$datasetKey}/{$version}";
        $manifestPath = $directory.'/manifest.json';

        if (! File::exists($manifestPath)) {
            throw new DatasetValidationException("Missing dataset manifest [{$datasetKey}/{$version}].");
        }

        $manifest = $this->readJson($manifestPath, 'manifest');
        $this->validateManifest($manifest, $datasetKey, $version, $directory);

        $dictionaryPath = $directory.'/'.$manifest['files']['data_dictionary'];
        $dictionary = $this->readJson($dictionaryPath, 'data dictionary');
        $this->validateDictionary($dictionary, $datasetKey, $version, $manifest);

        return [
            'directory' => $directory,
            'manifest' => $manifest,
            'data_dictionary' => $dictionary,
        ];
    }

    /**
     * @return list<array{dataset_key: string, version: string}>
     */
    public function validateAll(): array
    {
        $root = config('belajardata.datasets_path');
        $results = [];

        if (! File::isDirectory($root)) {
            throw new DatasetValidationException('The datasets directory does not exist.');
        }

        foreach (File::directories($root) as $datasetDirectory) {
            $datasetKey = basename($datasetDirectory);
            foreach (File::directories($datasetDirectory) as $versionDirectory) {
                $version = basename($versionDirectory);
                $this->load($datasetKey, $version);
                $results[] = ['dataset_key' => $datasetKey, 'version' => $version];
            }
        }

        if ($results === []) {
            throw new DatasetValidationException('No versioned dataset manifests were found.');
        }

        return $results;
    }

    /**
     * @param array<string, mixed> $manifest
     */
    private function validateManifest(array $manifest, string $datasetKey, string $version, string $directory): void
    {
        if (($manifest['schema_version'] ?? null) !== 1
            || ($manifest['dataset_key'] ?? null) !== $datasetKey
            || ($manifest['version'] ?? null) !== $version
            || ! is_string($manifest['grain'] ?? null)
            || ! is_array($manifest['files'] ?? null)
            || ! is_array($manifest['tables'] ?? null)
        ) {
            throw new DatasetValidationException("Invalid manifest contract [{$datasetKey}/{$version}].");
        }

        foreach ($manifest['files'] as $name => $relativePath) {
            if (! is_string($relativePath) || str_contains($relativePath, '..') || str_starts_with($relativePath, '/')) {
                throw new DatasetValidationException("Invalid file reference [{$name}] in [{$datasetKey}/{$version}].");
            }

            if (! File::exists($directory.'/'.$relativePath)) {
                throw new DatasetValidationException("Missing dataset file [{$relativePath}] in [{$datasetKey}/{$version}].");
            }
        }

        foreach ($manifest['tables'] as $tableKey => $table) {
            if (! is_array($table) || ! is_string($table['file'] ?? null) || ! is_array($table['columns'] ?? null)) {
                throw new DatasetValidationException("Invalid table contract [{$tableKey}] in [{$datasetKey}/{$version}].");
            }

            $columns = array_keys($table['columns']);
            if (count($columns) !== count(array_unique($columns))) {
                throw new DatasetValidationException("Duplicate columns in [{$tableKey}] for [{$datasetKey}/{$version}].");
            }
        }
    }

    /**
     * @param array<string, mixed> $dictionary
     * @param array<string, mixed> $manifest
     */
    private function validateDictionary(array $dictionary, string $datasetKey, string $version, array $manifest): void
    {
        if (($dictionary['schema_version'] ?? null) !== 1
            || ($dictionary['dataset_key'] ?? null) !== $datasetKey
            || ($dictionary['version'] ?? null) !== $version
            || ! is_array($dictionary['tables'] ?? null)
        ) {
            throw new DatasetValidationException("Invalid data dictionary contract [{$datasetKey}/{$version}].");
        }

        $manifestTables = array_keys($manifest['tables']);
        $dictionaryTables = array_keys($dictionary['tables']);

        if ($manifestTables !== $dictionaryTables) {
            throw new DatasetValidationException("Manifest and data dictionary tables do not match [{$datasetKey}/{$version}].");
        }

        foreach ($dictionary['tables'] as $tableKey => $table) {
            if (! is_array($table) || ! is_array($table['columns'] ?? null) || $table['columns'] === []) {
                throw new DatasetValidationException("Data dictionary table [{$tableKey}] has no columns.");
            }

            $manifestColumns = $manifest['tables'][$tableKey]['columns'] ?? null;

            if (! is_array($manifestColumns) || array_keys($manifestColumns) !== array_keys($table['columns'])) {
                throw new DatasetValidationException("Manifest and data dictionary columns do not match [{$tableKey}].");
            }

            foreach ($table['columns'] as $columnKey => $column) {
                if (! is_array($column) || ($column['type'] ?? null) !== $manifestColumns[$columnKey]) {
                    throw new DatasetValidationException("Invalid data dictionary column [{$tableKey}.{$columnKey}].");
                }
            }
        }
    }

    /**
     * @return array<string, mixed>|array<int, mixed>
     */
    private function readJson(string $path, string $label): array
    {
        try {
            $decoded = json_decode((string) File::get($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new DatasetValidationException("Invalid {$label} JSON [{$path}].", previous: $exception);
        }

        if (! is_array($decoded)) {
            throw new DatasetValidationException("The {$label} must decode to an array [{$path}].");
        }

        return $decoded;
    }
}
