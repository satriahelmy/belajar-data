<?php

namespace App\Http\Controllers;

use App\Domain\Learning\Content\ContentValidationException;
use App\Domain\Learning\CurriculumRepository;
use Illuminate\Contracts\View\View;

final class ModuleController extends Controller
{
    public function __invoke(string $moduleKey, CurriculumRepository $curriculum): View
    {
        try {
            $module = $curriculum->publishedModule($moduleKey);
        } catch (ContentValidationException) {
            abort(404);
        }

        $module = $curriculum->module($moduleKey);

        return view('learning.module', [
            'module' => $module,
            'path' => $curriculum->path(),
        ]);
    }
}
