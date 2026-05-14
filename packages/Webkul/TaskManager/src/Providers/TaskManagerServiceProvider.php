<?php

namespace Webkul\TaskManager\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Webkul\TaskManager\Contracts\Task as TaskContract;
use Webkul\TaskManager\Contracts\TaskComment as TaskCommentContract;
use Webkul\TaskManager\Contracts\TaskFollowup as TaskFollowupContract;
use Webkul\TaskManager\Contracts\TaskGroup as TaskGroupContract;
use Webkul\TaskManager\Contracts\TaskNotification as TaskNotificationContract;
use Webkul\TaskManager\Models\Task as TaskModel;
use Webkul\TaskManager\Models\TaskComment as TaskCommentModel;
use Webkul\TaskManager\Models\TaskFollowup as TaskFollowupModel;
use Webkul\TaskManager\Models\TaskGroup as TaskGroupModel;
use Webkul\TaskManager\Models\TaskNotification as TaskNotificationModel;

class TaskManagerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            'task_groups' => TaskGroupModel::class,
        ], false);

        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        foreach ([
            TaskGroupContract::class => TaskGroupModel::class,
            TaskContract::class => TaskModel::class,
            TaskNotificationContract::class => TaskNotificationModel::class,
            TaskCommentContract::class => TaskCommentModel::class,
            TaskFollowupContract::class => TaskFollowupModel::class,
        ] as $contract => $model) {
            $this->app->bind($contract, $model);
        }
    }
}
