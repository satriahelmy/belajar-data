<?php

namespace App\Domain\Learner\Progress;

use App\Models\User;
use App\Models\UserTopicProgress;

interface ProgressStore
{
    public function start(User $user, string $contentKey): UserTopicProgress;

    public function complete(User $user, string $contentKey): UserTopicProgress;

    /**
     * @param  array<string, string>  $guestTopics
     */
    public function mergeGuest(User $user, array $guestTopics): void;
}
