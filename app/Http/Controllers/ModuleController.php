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
        $phases = $curriculum->phases();
        $flattenedModules = [];
        $currentPhase = null;

        foreach ($phases as $phase) {
            foreach ($phase['modules'] as $phaseModule) {
                $flattenedModules[] = $phaseModule;

                if ($phaseModule['key'] === $moduleKey) {
                    $currentPhase = $phase;
                }
            }
        }

        $currentIndex = array_search($moduleKey, array_column($flattenedModules, 'key'), true);

        return view('learning.module', [
            'module' => $module,
            'path' => $curriculum->path(),
            'phase' => $currentPhase,
            'nextModule' => $currentIndex === false ? null : ($flattenedModules[$currentIndex + 1] ?? null),
        ]);
    }
}
