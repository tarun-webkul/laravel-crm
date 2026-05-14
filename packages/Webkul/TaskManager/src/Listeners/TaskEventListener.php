<?php

namespace Webkul\TaskManager\Listeners;

use Webkul\TaskManager\Models\Task;
use Webkul\TaskManager\Repositories\TaskNotificationRepository;

class TaskEventListener
{
    public function __construct(
        protected TaskNotificationRepository $notificationRepository,
    ) {}

    /**
     * Task created → notify assignee.
     */
    public function onTaskCreated(Task $task): void
    {
        if ($task->assigned_to && $task->assigned_to !== $task->created_by) {
            $this->notificationRepository->notifyUser(
                $task->assigned_to,
                $task,
                'task_assigned',
                "You have been assigned a new task: \"{$task->title}\"",
                ['task_id' => $task->id, 'creator' => $task->creator?->name]
            );
        }
    }

    /**
     * Task assigned → notify new assignee.
     */
    public function onTaskAssigned(Task $task): void
    {
        if ($task->assigned_to) {
            $this->notificationRepository->notifyUser(
                $task->assigned_to,
                $task,
                'task_assigned',
                "You have been assigned to task: \"{$task->title}\"",
                ['task_id' => $task->id]
            );
        }
    }

    /**
     * Task updated → notify all followers (except the updater).
     */
    public function onTaskUpdated(Task $task): void
    {
        $updaterId = auth()->id();

        $followers = $task->followers()
            ->where('users.id', '!=', $updaterId)
            ->get();

        if ($followers->isEmpty()) {
            return;
        }

        $this->notificationRepository->notifyUsers(
            $followers,
            $task,
            'task_updated',
            "Task \"{$task->title}\" has been updated.",
            ['task_id' => $task->id, 'status' => $task->status]
        );
    }

    /**
     * Task deleted → notify all followers.
     */
    public function onTaskDeleted(Task $task): void
    {
        $updaterId = auth()->id();

        $followers = $task->followers()
            ->where('users.id', '!=', $updaterId)
            ->get();

        if ($followers->isEmpty()) {
            return;
        }

        $this->notificationRepository->notifyUsers(
            $followers,
            $task,
            'task_updated',
            "Task \"{$task->title}\" has been deleted.",
            ['task_id' => $task->id]
        );
    }
}
