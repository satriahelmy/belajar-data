<?php

namespace App\Http\Controllers;

use App\Domain\Learning\Content\ContentValidationException;
use App\Domain\Learning\Content\MarkdownLessonRenderer;
use App\Domain\Learning\CurriculumRepository;
use Illuminate\Contracts\View\View;

final class ChallengeController extends Controller
{
    public function __invoke(
        string $pathKey,
        string $moduleKey,
        MarkdownLessonRenderer $renderer,
        CurriculumRepository $curriculum,
    ): View {
        try {
            $curriculum->publishedModule($moduleKey, $pathKey);
            $module = $curriculum->module($moduleKey, $pathKey);
            $challenge = $module['challenge'];
            $lesson = $renderer->renderChallenge($moduleKey, $pathKey);
        } catch (ContentValidationException) {
            abort(404);
        }

        return view('learning.challenge', [
            'lesson' => $lesson,
            'pathKey' => $pathKey,
            'module' => $module,
            'challenge' => $challenge,
            'challengeTitle' => $challenge['title'],
        ]);
    }
}
