<?php

namespace App\Domain\Learner\Progress;

use App\Models\User;
use App\Models\UserTopicProgress;
use Illuminate\Support\Facades\DB;

final class DatabaseProgressStore implements ProgressStore
{
    public function start(User $user, string $contentKey): UserTopicProgress
    {
        return DB::transaction(function () use ($user, $contentKey): UserTopicProgress {
            $progress = UserTopicProgress::query()->firstOrNew([
                'user_id' => $user->id,
                'content_key' => $contentKey,
            ]);

            $now = now();
            $progress->started_at ??= $now;
            $progress->last_activity_at = $now;

            if ($progress->status !== 'completed') {
                $progress->status = 'started';
            }

            $progress->save();

            return $progress;
        });
    }

    public function complete(User $user, string $contentKey): UserTopicProgress
    {
        return DB::transaction(function () use ($user, $contentKey): UserTopicProgress {
            $progress = UserTopicProgress::query()->firstOrNew([
                'user_id' => $user->id,
                'content_key' => $contentKey,
            ]);

            $now = now();
            $progress->started_at ??= $now;
            $progress->completed_at ??= $now;
            $progress->last_activity_at = $now;
            $progress->status = 'completed';
            $progress->save();

            return $progress;
        });
    }

    public function mergeGuest(User $user, array $guestTopics): void
    {
        DB::transaction(function () use ($user, $guestTopics): void {
            foreach ($guestTopics as $contentKey => $status) {
                if ($status === 'completed') {
                    $this->complete($user, $contentKey);

                    continue;
                }

                $this->start($user, $contentKey);
            }
        });
    }
}
