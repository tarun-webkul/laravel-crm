<?php

namespace Webkul\TaskManager\Listeners;

use Webkul\TaskManager\Repositories\TaskNotificationRepository;

class FollowupEventListener
{
    public function __construct(
        protected TaskNotificationRepository $notificationRepository,
    ) {}

    /**
     * Notify user when they are added as a follower.
     */
    public function onFollowupAdded(array $payload): void
    {
        [$task, $userId] = $payload;

        // Don't notify if they added themselves
        if ($userId === auth()->id()) {
            return;
        }

        $this->notificationRepository->notifyUser(
            $userId,
            $task,
            'followup_added',
            "You are now following task: \"{$task->title}\".",
            ['task_id' => $task->id]
        );
    }

    /**
     * Optionally notify on followup removal (informational).
     */
    public function onFollowupRemoved(array $payload): void
    {
        // Intentionally left minimal — no notification for unfollow
    }
}
