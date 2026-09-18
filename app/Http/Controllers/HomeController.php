<?php

namespace App\Http\Controllers;

use App\Domain\Datasets\DatasetManifestRepository;
use App\Domain\Learning\CurriculumRepository;
use Illuminate\Contracts\View\View;

final class HomeController extends Controller
{
    public function __invoke(CurriculumRepository $curriculum, DatasetManifestRepository $datasets): View
    {
        $phases = $curriculum->phases();
        $firstModule = $phases[0]['modules'][0] ?? null;
        $dataset = $datasets->load('nusamart', 'v1');
        $tables = [];

        foreach ($dataset['manifest']['tables'] as $key => $table) {
            $tables[] = [
                'key' => $key,
                'columns' => array_keys($table['columns']),
            ];
        }

        return view('home', [
            'path' => $curriculum->path(),
            'phases' => $phases,
            'firstModule' => $firstModule,
            'dataset' => [
                'name' => 'NusaMart',
                'version' => $dataset['manifest']['version'],
                'grain' => $dataset['manifest']['grain'],
                'tables' => $tables,
            ],
        ]);
    }
}
