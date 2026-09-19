<?php

namespace App\Http\Controllers;

use App\Domain\Learning\Content\ContentValidationException;
use App\Domain\Learning\CurriculumRepository;
use App\Models\Bookmark;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

final class BookmarkController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'bookmarks' => $request->user()->bookmarks()
                ->orderByDesc('created_at')
                ->get()
                ->map(fn (Bookmark $bookmark): array => $this->bookmarkArray($bookmark))
                ->values(),
        ]);
    }

    public function toggle(
        Request $request,
        CurriculumRepository $curriculum,
    ): JsonResponse|RedirectResponse {
        $data = $request->validate([
            'content_type' => ['required', 'in:topic'],
            'content_key' => ['required', 'string', 'regex:/^[a-z0-9][a-z0-9-]*\/[a-z0-9][a-z0-9-]*\/[a-z0-9][a-z0-9-]*$/'],
        ]);

        $this->assertPublishedTopic($data['content_key'], $curriculum);

        $bookmark = $request->user()->bookmarks()->firstWhere([
            'content_type' => $data['content_type'],
            'content_key' => $data['content_key'],
        ]);

        if ($bookmark) {
            $bookmark->delete();
            $bookmarked = false;
            $bookmarkPayload = null;
        } else {
            $bookmark = $request->user()->bookmarks()->create($data);
            $bookmarked = true;
            $bookmarkPayload = $this->bookmarkArray($bookmark);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'bookmarked' => $bookmarked,
                'bookmark' => $bookmarkPayload,
            ]);
        }

        return back()->with('bookmark_status', $bookmarked ? 'Lesson disimpan.' : 'Bookmark dihapus.');
    }

    /**
     * @return array{content_type: string, content_key: string, created_at: string|null}
     */
    private function bookmarkArray(Bookmark $bookmark): array
    {
        return [
            'content_type' => $bookmark->content_type,
            'content_key' => $bookmark->content_key,
            'created_at' => $bookmark->created_at?->toISOString(),
        ];
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
