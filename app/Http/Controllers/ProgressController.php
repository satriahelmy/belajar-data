<?php

namespace App\Http\Controllers;

use App\Domain\Learner\Progress\ProgressStore;
use App\Domain\Learning\Content\ContentValidationException;
use App\Domain\Learning\CurriculumRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

final class ProgressController extends Controller
{
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
}
