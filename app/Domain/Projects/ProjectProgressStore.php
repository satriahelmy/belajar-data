<?php

namespace App\Domain\Projects;

use App\Models\User;
use App\Models\UserProjectProgress;
use Illuminate\Support\Facades\DB;

final class ProjectProgressStore
{
    public function start(User $user, string $projectKey, string $firstStage): UserProjectProgress
    {
        return DB::transaction(function () use ($user, $projectKey, $firstStage): UserProjectProgress {
            $progress = UserProjectProgress::query()->firstOrNew([
                'user_id' => $user->id,
                'project_key' => $projectKey,
            ]);

            if ($progress->status !== 'completed') {
                $progress->status = 'started';
                $progress->current_stage_key ??= $firstStage;
                $progress->started_at ??= now();
            }

            $progress->last_activity_at = now();
            $progress->save();

            return $progress;
        });
    }

    public function completeStage(User $user, string $projectKey, string $stageKey, ?string $nextStage, bool $projectComplete): UserProjectProgress
    {
        return DB::transaction(function () use ($user, $projectKey, $stageKey, $nextStage, $projectComplete): UserProjectProgress {
            $progress = UserProjectProgress::query()->firstOrCreate([
                'user_id' => $user->id,
                'project_key' => $projectKey,
            ], [
                'status' => 'started',
                'current_stage_key' => $stageKey,
                'started_at' => now(),
            ]);

            $progress->status = $projectComplete ? 'completed' : 'started';
            $progress->current_stage_key = $projectComplete ? $stageKey : $nextStage;
            $progress->completed_at = $projectComplete ? ($progress->completed_at ?? now()) : null;
            $progress->last_activity_at = now();
            $progress->save();

            return $progress;
        });
    }

    public function find(User $user, string $projectKey): ?UserProjectProgress
    {
        return $user->projectProgress()->where('project_key', $projectKey)->first();
    }
}
