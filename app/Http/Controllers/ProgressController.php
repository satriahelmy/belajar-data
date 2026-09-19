<?php

namespace App\Http\Controllers;

use App\Domain\Learner\Progress\ProgressStore;
use App\Domain\Learning\Content\ContentValidationException;
use App\Domain\Learning\CurriculumRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class ProgressController extends Controller
{
    public function page(Request $request, CurriculumRepository $curriculum): View
    {
        $topics = $this->topicIndex($curriculum);
        $recentTopics = $request->user()
            ->topicProgress()
            ->orderByDesc('last_activity_at')
            ->get()
            ->map(function ($progress) use ($topics): ?array {
                $topic = $topics[$progress->content_key] ?? null;

                if (! $topic) {
                    return null;
                }

                return [...$progress->toProgressArray(), ...$topic];
            })
            ->filter()
            ->values();
        $bookmarks = $request->user()
            ->bookmarks()
            ->where('content_type', 'topic')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($bookmark) use ($topics): ?array {
                $topic = $topics[$bookmark->content_key] ?? null;

                if (! $topic) {
                    return null;
                }

                return [
                    'content_type' => $bookmark->content_type,
                    'content_key' => $bookmark->content_key,
                    'created_at' => $bookmark->created_at?->toISOString(),
                    ...$topic,
                ];
            })
            ->filter()
            ->values();

        return view('learning.progress', [
            'recentTopics' => $recentTopics,
            'bookmarks' => $bookmarks,
            'completedCount' => $recentTopics->where('status', 'completed')->count(),
            'startedCount' => $recentTopics->where('status', 'started')->count(),
            'publishedTopicCount' => count($topics),
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $topics = $request->user()
            ->topicProgress()
            ->orderByDesc('last_activity_at')
            ->get()
            ->mapWithKeys(fn ($progress): array => [$progress->content_key => $progress->toProgressArray()]);

        return response()->json(['topics' => $topics]);
    }

    public function start(
        Request $request,
        CurriculumRepository $curriculum,
        ProgressStore $progress,
    ): JsonResponse {
        $contentKey = $this->validatedTopicKey($request, $curriculum);

        return response()->json([
            'topic' => $progress->start($request->user(), $contentKey)->toProgressArray(),
        ]);
    }

    public function complete(
        Request $request,
        CurriculumRepository $curriculum,
        ProgressStore $progress,
    ): JsonResponse {
        $contentKey = $this->validatedTopicKey($request, $curriculum);

        return response()->json([
            'topic' => $progress->complete($request->user(), $contentKey)->toProgressArray(),
        ]);
    }

    public function mergeGuest(
        Request $request,
        CurriculumRepository $curriculum,
        ProgressStore $progress,
    ): JsonResponse {
        $data = $request->validate([
            'topics' => ['required', 'array', 'max:100'],
            'topics.*.content_key' => ['required', 'string', 'regex:/^[a-z0-9][a-z0-9-]*\/[a-z0-9][a-z0-9-]*\/[a-z0-9][a-z0-9-]*$/'],
            'topics.*.status' => ['required', 'in:started,completed'],
        ]);

        $guestTopics = [];

        foreach ($data['topics'] as $topic) {
            $contentKey = $topic['content_key'];
            $this->assertPublishedTopic($contentKey, $curriculum);
            $guestTopics[$contentKey] = $topic['status'];
        }

        $progress->mergeGuest($request->user(), $guestTopics);

        return $this->index($request);
    }

    private function validatedTopicKey(Request $request, CurriculumRepository $curriculum): string
    {
        $data = $request->validate([
            'content_key' => ['required', 'string', 'regex:/^[a-z0-9][a-z0-9-]*\/[a-z0-9][a-z0-9-]*\/[a-z0-9][a-z0-9-]*$/'],
        ]);

        $this->assertPublishedTopic($data['content_key'], $curriculum);

        return $data['content_key'];
    }

    private function assertPublishedTopic(string $contentKey, CurriculumRepository $curriculum): void
    {
        [$pathKey, $moduleKey, $topicKey] = explode('/', $contentKey);

        try {
            $curriculum->topic($moduleKey, $topicKey, $pathKey);
        } catch (ContentValidationException) {
            throw ValidationException::withMessages([
                'content_key' => 'Topic tidak tersedia untuk disimpan.',
            ]);
        }
    }

    /**
     * @return array<string, array{title: string, module_title: string, module_key: string, topic_key: string, url: string}>
     */
    private function topicIndex(CurriculumRepository $curriculum): array
    {
        $topics = [];

        foreach ($curriculum->modules() as $module) {
            if (! $module['published']) {
                continue;
            }

            foreach ($module['topics'] as $topic) {
                $contentKey = 'data-analyst/'.$module['key'].'/'.$topic['key'];
                $topics[$contentKey] = [
                    'title' => $topic['title'],
                    'module_title' => $module['title'],
                    'module_key' => $module['key'],
                    'topic_key' => $topic['key'],
                    'url' => route('learning.lesson', [
                        'pathKey' => 'data-analyst',
                        'moduleKey' => $module['key'],
                        'topicKey' => $topic['key'],
                    ]),
                ];
            }
        }

        return $topics;
    }
}
