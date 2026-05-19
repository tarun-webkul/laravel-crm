<?php

namespace Webkul\TaskManager\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;
use Webkul\TaskManager\Models\Task;
use Webkul\TaskManager\Models\TaskComment;
use Webkul\TaskManager\Models\TaskFollowup;
use Webkul\TaskManager\Models\TaskGroup;
use Webkul\TaskManager\Models\TaskNotification;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    /**
     * The models to be used by this module.
     *
     * @var array
     */
    protected $models = [
        Task::class,
        TaskComment::class,
        TaskFollowup::class,
        TaskNotification::class,
        TaskGroup::class,
    ];
}
