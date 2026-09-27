<?php

namespace App\Http\Controllers;

use App\Domain\Learner\Assessment\AttemptStore;
use App\Domain\Learner\Assessment\ExerciseValidator;
use App\Domain\Learning\Content\ContentValidationException;
use App\Domain\Learning\Content\MarkdownLessonRenderer;
use App\Domain\Projects\ProjectProgressStore;
use App\Domain\Projects\ProjectRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

final class ProjectsController extends Controller
{
    public function index(ProjectRepository $projects): View
    {
        return view('projects.index', [
            'projects' => array_map(fn (array $project): array => [
                'project' => $project,
                'progress' => auth()->user()?->projectProgress()->where('project_key', $project['key'])->first(),
            ], $projects->projects()),
        ]);
    }

    public function show(string $projectKey, ProjectRepository $projects, ProjectProgressStore $progress): View
    {
        $project = $this->published($projects, $projectKey);
        $userProgress = auth()->user() ? $progress->find(auth()->user(), $projectKey) : null;

        return view('projects.show', [
            'project' => $project,
            'progress' => $userProgress,
            'statuses' => $this->stageStatuses($project, $projects),
        ]);
    }

    public function stage(
        string $projectKey,
        string $stageKey,
        ProjectRepository $projects,
        MarkdownLessonRenderer $renderer,
        ProjectProgressStore $progress,
    ): View {
        $project = $this->published($projects, $projectKey);

        try {
            $source = $projects->stage($projectKey, $stageKey);
        } catch (ContentValidationException) {
            abort(404);
        }

        $exercise = $source['exercises'][$source['stage']['exercise_id']];
        $user = auth()->user();
        $exerciseKey = $this->exerciseKey($projectKey, $stageKey, $source['stage']['exercise_id']);
        $attempt = $user?->attempts()->where('exercise_key', $exerciseKey)->first();
        $userProgress = $user ? $progress->find($user, $projectKey) : null;

        return view('projects.stage', [
            'project' => $project,
            'stage' => $source['stage'],
            'stageKey' => $stageKey,
            'lesson' => $renderer->renderProject($projectKey, $stageKey, $source['markdown'], $source['source_hash']),
            'exercise' => $exercise,
            'attempt' => $attempt,
            'progress' => $userProgress,
            'statuses' => $this->stageStatuses($project, $projects),
            'feedback' => session('project_feedback'),
            'referenceUnlocked' => $this->referenceUnlocked($projectKey, $user, $attempt),
        ]);
    }

    public function start(string $projectKey, Request $request, ProjectRepository $projects, ProjectProgressStore $progress): RedirectResponse
    {
        $project = $this->published($projects, $projectKey);
        $progress->start($request->user(), $projectKey, $project['stage_keys'][0]);

        return redirect()->route('projects.stage', [$projectKey, $project['stage_keys'][0]]);
    }

    public function check(
        string $projectKey,
        string $stageKey,
        Request $request,
        ProjectRepository $projects,
        ProjectProgressStore $progress,
        ExerciseValidator $validator,
        AttemptStore $attempts,
    ): RedirectResponse {
        $project = $this->published($projects, $projectKey);

        try {
            $source = $projects->stage($projectKey, $stageKey);
        } catch (ContentValidationException) {
            abort(404);
        }

        $exercise = $source['exercises'][$source['stage']['exercise_id']];
        $answer = $this->answerFromRequest($request, $exercise);
        $result = $validator->validate($exercise, $answer);
        $exerciseKey = $this->exerciseKey($projectKey, $stageKey, $source['stage']['exercise_id']);
        $attempts->record($request->user(), $exerciseKey, $result);
        $progress->start($request->user(), $projectKey, $project['stage_keys'][0]);

        if ($result->completed) {
            $statuses = $this->stageStatuses($project, $projects, $request->user(), $stageKey, true);
            $stageIndex = array_search($stageKey, $project['stage_keys'], true);
            $nextStage = $project['stage_keys'][$stageIndex + 1] ?? null;
            $progress->completeStage($request->user(), $projectKey, $stageKey, $nextStage, $statuses === [] || count(array_filter($statuses)) === count($project['stage_keys']));
        }

        return redirect()->route('projects.stage', [$projectKey, $stageKey])->with('project_feedback', [
            'valid' => $result->valid,
            'completed' => $result->completed,
            'text' => $result->feedback,
        ]);
    }

    public function reference(string $projectKey, ProjectRepository $projects, MarkdownLessonRenderer $renderer): View
    {
        try {
            $reference = $projects->reference($projectKey);
        } catch (ContentValidationException) {
            abort(404);
        }

        $user = auth()->user();
        $hasAttempt = $user?->attempts()->where('exercise_key', 'like', "project/{$projectKey}/%")->exists() ?? false;
        $unlocked = (bool) session("project_reference.{$projectKey}") || $hasAttempt;

        return view('projects.reference', [
            'project' => $reference['project'],
            'unlocked' => $unlocked,
            'lesson' => $unlocked ? $renderer->renderProject($projectKey, 'reference', $reference['markdown'], $reference['source_hash']) : null,
        ]);
    }

    public function unlockReference(string $projectKey, Request $request, ProjectRepository $projects, ProjectProgressStore $progress): RedirectResponse
    {
        $project = $this->published($projects, $projectKey);
        session(["project_reference.{$projectKey}" => true]);

        if ($request->user()) {
            $progress->start($request->user(), $projectKey, $project['stage_keys'][0]);
        }

        return redirect()->route('projects.reference', [$projectKey]);
    }

    /** @return array<string, mixed> */
    private function published(ProjectRepository $projects, string $projectKey): array
    {
        try {
            return $projects->publishedProject($projectKey);
        } catch (ContentValidationException) {
            abort(404);
        }
    }

    /** @return array<string, bool> */
    private function stageStatuses(array $project, ProjectRepository $projects, ?\App\Models\User $user = null, ?string $justCompleted = null, bool $completeCurrent = false): array
    {
        $statuses = [];
        $user ??= auth()->user();

        foreach ($project['stage_keys'] as $stageKey) {
            $completed = false;

            if ($user) {
                $stage = $project['stages'][$stageKey];
                $completed = $user->attempts()->where('exercise_key', $this->exerciseKey($project['key'], $stageKey, $stage['exercise_id']))->where('status', 'completed')->exists();
            }

            if ($completeCurrent && $stageKey === $justCompleted) {
                $completed = true;
            }

            $statuses[$stageKey] = $completed;
        }

        return $statuses;
    }

    private function referenceUnlocked(string $projectKey, ?\App\Models\User $user, mixed $attempt): bool
    {
        return (bool) session("project_reference.{$projectKey}")
            || $attempt !== null
            || ($user?->attempts()->where('exercise_key', 'like', "project/{$projectKey}/%")->exists() ?? false);
    }

    private function exerciseKey(string $projectKey, string $stageKey, string $exerciseId): string
    {
        return "project/{$projectKey}/{$stageKey}/{$exerciseId}";
    }

    /** @param array<string, mixed> $exercise @return array<string, mixed> */
    private function answerFromRequest(Request $request, array $exercise): array
    {
        return match ($exercise['type']) {
            'multiple_choice' => ['option' => is_numeric($request->input('option')) ? (int) $request->input('option') : -1],
            'multi_select' => ['options' => array_values(array_map('intval', (array) $request->input('options', [])))],
            'numeric' => ['value' => $request->input('value')],
            'text_self_assessment' => [
                'text' => (string) $request->input('text', ''),
                'checklist' => array_map(
                    fn (int $index): bool => array_key_exists((string) $index, (array) $request->input('checklist', [])),
                    range(0, count($exercise['checklist']) - 1),
                ),
                'complete' => $request->boolean('complete'),
            ],
            default => [],
        };
    }
}
