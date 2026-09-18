<?php

namespace App\Http\Controllers;

use App\Domain\Learning\Content\MarkdownLessonRenderer;
use Illuminate\Contracts\View\View;

class SpikeLessonController extends Controller
{
    public function __invoke(MarkdownLessonRenderer $renderer): View
    {
        $key = '01-thinking-with-data/01-analyst-role';
        $lesson = $renderer->render($key);

        return view('learning.spike-lesson', [
            'lesson' => $lesson,
            'lessonKey' => $key,
        ]);
    }
}
