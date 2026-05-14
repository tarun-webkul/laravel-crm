<?php

namespace Webkul\TaskManager\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Task lifecycle events
        Event::listen('task_manager.task.created', 'Webkul\TaskManager\Listeners\TaskEventListener@onTaskCreated');
        Event::listen('task_manager.task.assigned', 'Webkul\TaskManager\Listeners\TaskEventListener@onTaskAssigned');
        Event::listen('task_manager.task.updated', 'Webkul\TaskManager\Listeners\TaskEventListener@onTaskUpdated');
        Event::listen('task_manager.task.deleted', 'Webkul\TaskManager\Listeners\TaskEventListener@onTaskDeleted');

        // Comment events
        Event::listen('task_manager.comment.created', 'Webkul\TaskManager\Listeners\CommentEventListener@onCommentCreated');

        // Followup events
        Event::listen('task_manager.followup.added', 'Webkul\TaskManager\Listeners\FollowupEventListener@onFollowupAdded');
        Event::listen('task_manager.followup.removed', 'Webkul\TaskManager\Listeners\FollowupEventListener@onFollowupRemoved');
    }
}
