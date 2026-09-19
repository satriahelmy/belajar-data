<?php

namespace App\Http\Controllers;

use App\Domain\Learning\Content\ContentValidationException;
use App\Domain\Learning\Content\MarkdownLessonRenderer;
use App\Domain\Learning\CurriculumRepository;
use Illuminate\Contracts\View\View;

final class LessonController extends Controller
{
    public function __invoke(
        string $pathKey,
        string $moduleKey,
        string $topicKey,
        MarkdownLessonRenderer $renderer,
        CurriculumRepository $curriculum,
    ): View {
        $lessonKey = $moduleKey.'/'.$topicKey;

        try {
            $module = $curriculum->publishedModule($moduleKey, $pathKey);
            $topic = $curriculum->topic($moduleKey, $topicKey, $pathKey);
            $navigation = $curriculum->lessonNavigation($moduleKey, $topicKey, $pathKey);
            $lesson = $renderer->render($lessonKey, $pathKey);
            $module = $curriculum->module($moduleKey, $pathKey);
        } catch (ContentValidationException) {
            abort(404);
        }

        $contentKey = $pathKey.'/'.$lessonKey;
        $progressState = auth()->user()?->topicProgress()
            ->where('content_key', $contentKey)
            ->value('status');
        $bookmarked = auth()->user()?->bookmarks()
            ->where('content_type', 'topic')
            ->where('content_key', $contentKey)
            ->exists() ?? false;

        return view('learning.lesson', [
            'lesson' => $lesson,
            'pathKey' => $pathKey,
            'lessonKey' => $lessonKey,
            'module' => $module,
            'topic' => $topic,
            'navigation' => $navigation,
            'lessonTitle' => $topic['title'] ?? $lesson->headings[0]['text'] ?? $lessonKey,
            'progressKey' => $contentKey,
            'progressState' => $progressState,
            'bookmarked' => $bookmarked,
        ]);
    }
}
