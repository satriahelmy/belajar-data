<?php

namespace App\Http\Controllers;

use App\Domain\Datasets\DatasetManifestRepository;
use App\Domain\Datasets\DatasetValidationException;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class TableauDatasetController extends Controller
{
    private const DATASET_KEY = 'nusamart';
    private const DATASET_VERSION = 'v1';

    public function index(DatasetManifestRepository $datasets): View
    {
        $files = $this->tableauFiles($datasets);

        return view('downloads.tableau', [
            'datasetKey' => self::DATASET_KEY,
            'datasetVersion' => self::DATASET_VERSION,
            'files' => $files,
        ]);
    }

    public function download(string $file, DatasetManifestRepository $datasets): BinaryFileResponse
    {
        $dataset = $this->loadDataset($datasets);
        $relativePath = $dataset['manifest']['files']['tableau_'.$file] ?? null;

        if (! is_string($relativePath) || ! in_array($file, ['transactions', 'products', 'orders', 'customers'], true)) {
            abort(404);
        }

        $path = $dataset['directory'].'/'.$relativePath;

        if (! is_file($path)) {
            abort(404);
        }

        return response()->download(
            $path,
            'nusamart-'.self::DATASET_VERSION.'-'.$file.'.csv',
            ['Content-Type' => 'text/csv; charset=UTF-8'],
        );
    }

    /** @return list<array<string, mixed>> */
    private function tableauFiles(DatasetManifestRepository $datasets): array
    {
        $dataset = $this->loadDataset($datasets);
        $files = [];

        foreach ($dataset['manifest']['files'] as $key => $relativePath) {
            if (! str_starts_with((string) $key, 'tableau_') || ! is_string($relativePath)) {
                continue;
            }

            $file = substr((string) $key, strlen('tableau_'));
            $path = $dataset['directory'].'/'.$relativePath;

            if (! in_array($file, ['transactions', 'products', 'orders', 'customers'], true) || ! is_file($path)) {
                continue;
            }

            $files[] = [
                'key' => $file,
                'name' => basename($path),
                'url' => route('downloads.tableau.file', ['file' => $file]),
                'bytes' => filesize($path),
                'sha256' => hash_file('sha256', $path),
            ];
        }

        if ($files === []) {
            abort(404);
        }

        return $files;
    }

    /** @return array<string, mixed> */
    private function loadDataset(DatasetManifestRepository $datasets): array
    {
        try {
            return $datasets->load(self::DATASET_KEY, self::DATASET_VERSION);
        } catch (DatasetValidationException) {
            abort(404);
        }
    }
}
