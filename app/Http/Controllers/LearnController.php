<?php

namespace App\Http\Controllers;

use App\Domain\Learning\CurriculumRepository;
use Illuminate\Contracts\View\View;

final class LearnController extends Controller
{
    public function __invoke(CurriculumRepository $curriculum): View
    {
        return view('learning.index', [
            'path' => $curriculum->path(),
            'phases' => $curriculum->phases(),
        ]);
    }
}
