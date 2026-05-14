<?php

namespace Webkul\TaskManager\Listeners;

use Webkul\TaskManager\Repositories\TaskNotificationRepository;

class CommentEventListener
{
    public function __construct(
        protected TaskNotificationRepository $notificationRepository,
    ) {}

    /**
     * Comment created → notify relevant users.
     *
     * Internal comments: notify creator + assignee only.
     * Group comments: notify all followers.
     */
    public function onCommentCreated(array $payload): void
    {
        [$task, $comment] = $payload;

        $commenterId = auth()->id();
        $commenterName = auth()->user()?->name ?? 'Someone';

        if ($comment->type === 'internal') {
            // Notify only creator and assignee (not the commenter)
            $notifyIds = collect([$task->created_by, $task->assigned_to])
                ->filter()
                ->unique()
                ->reject(fn ($id) => $id === $commenterId)
                ->values();

            foreach ($notifyIds as $userId) {
                $this->notificationRepository->notifyUser(
                    $userId,
                    $task,
                    'comment_added',
                    "{$commenterName} added an internal note on \"{$task->title}\".",
                    ['task_id' => $task->id, 'comment_type' => 'internal']
                );
            }

        } else {
            // Group comment → notify all followers except commenter
            $followers = $task->followers()
                ->where('users.id', '!=', $commenterId)
                ->get();

            if ($followers->isEmpty()) {
                return;
            }

            $this->notificationRepository->notifyUsers(
                $followers,
                $task,
                'comment_added',
                "{$commenterName} commented on \"{$task->title}\".",
                ['task_id' => $task->id, 'comment_type' => 'group']
            );
        }
    }
}
