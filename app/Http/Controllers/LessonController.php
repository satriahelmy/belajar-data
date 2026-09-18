<?php

namespace App\Http\Controllers;

use App\Domain\Learning\Content\MarkdownLessonRenderer;
use Illuminate\Contracts\View\View;

final class LessonController extends Controller
{
    public function __invoke(
        string $pathKey,
        string $moduleKey,
        string $topicKey,
        MarkdownLessonRenderer $renderer,
    ): View {
        $lessonKey = $moduleKey.'/'.$topicKey;
        $lesson = $renderer->render($lessonKey, $pathKey);

        return view('learning.lesson', [
            'lesson' => $lesson,
            'pathKey' => $pathKey,
            'lessonKey' => $lessonKey,
            'lessonTitle' => $lesson->headings[0]['text'] ?? $lessonKey,
        ]);
    }
}
