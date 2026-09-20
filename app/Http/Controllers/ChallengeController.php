<?php

namespace App\Http\Controllers;

use App\Domain\Learning\Content\ContentRepository;
use App\Domain\Learning\Content\ContentValidationException;
use App\Domain\Learning\Content\MarkdownLessonRenderer;
use App\Domain\Learning\CurriculumRepository;
use Illuminate\Contracts\View\View;

final class ChallengeController extends Controller
{
    public function __invoke(
        string $pathKey,
        string $moduleKey,
        ContentRepository $content,
        MarkdownLessonRenderer $renderer,
        CurriculumRepository $curriculum,
    ): View {
        try {
            $curriculum->publishedModule($moduleKey, $pathKey);
            $module = $curriculum->module($moduleKey, $pathKey);
            $challenge = $module['challenge'];
            $source = $content->challenge($moduleKey, $pathKey);
            $lesson = $renderer->renderChallenge($moduleKey, $pathKey);
        } catch (ContentValidationException) {
            abort(404);
        }

        $assessment = [
            'completed' => 0,
            'total' => count($source->exercises),
            'is_complete' => false,
        ];

        if (auth()->check()) {
            $exerciseKeys = array_map(
                static fn (string $exerciseId): string => $pathKey.'/'.$source->key.'/'.$exerciseId,
                array_keys($source->exercises),
            );
            $completed = auth()->user()->attempts()
                ->whereIn('exercise_key', $exerciseKeys)
                ->where('status', 'completed')
                ->count();
            $assessment = [
                'completed' => $completed,
                'total' => count($exerciseKeys),
                'is_complete' => $exerciseKeys !== [] && $completed === count($exerciseKeys),
            ];
        }

        return view('learning.challenge', [
            'lesson' => $lesson,
            'pathKey' => $pathKey,
            'module' => $module,
            'challenge' => $challenge,
            'challengeTitle' => $challenge['title'],
            'assessment' => $assessment,
        ]);
    }
}
