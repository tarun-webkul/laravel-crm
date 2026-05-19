<?php

return [

    /**
     * Task Manager.
     */
    [
        'key' => 'task_manager',
        'name' => 'taskmanager::app.layouts.task_manager',
        'route' => 'admin.task_manager.task_groups.index',
        'sort' => 7,
        'icon-class' => 'icon-file',
    ],
    [
        'key' => 'task_manager.tasks',
        'name' => 'taskmanager::app.layouts.tasks',
        'route' => 'admin.task_manager.tasks.index',
        'sort' => 1,
        'icon-class' => '',
    ],
    [
        'key' => 'task_manager.task_groups',
        'name' => 'taskmanager::app.layouts.task_groups',
        'route' => 'admin.task_manager.task_groups.index',
        'sort' => 2,
        'icon-class' => '',
    ],

];
