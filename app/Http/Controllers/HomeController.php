<?php

namespace App\Http\Controllers;

use App\Domain\Learning\CurriculumRepository;
use Illuminate\Contracts\View\View;

final class HomeController extends Controller
{
    public function __invoke(CurriculumRepository $curriculum): View
    {
        $phases = $curriculum->phases();
        $firstModule = $phases[0]['modules'][0] ?? null;

        return view('home', [
            'path' => $curriculum->path(),
            'phases' => $phases,
            'firstModule' => $firstModule,
        ]);
    }
}
