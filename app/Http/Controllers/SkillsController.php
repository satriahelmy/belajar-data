<?php

namespace App\Http\Controllers;

use App\Domain\Learning\CurriculumRepository;
use Illuminate\Contracts\View\View;

final class SkillsController extends Controller
{
    public function __invoke(CurriculumRepository $curriculum): View
    {
        return view('skills.index', [
            'skills' => $curriculum->skills(),
        ]);
    }
}
