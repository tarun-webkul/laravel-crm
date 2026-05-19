<?php

return [
    [
        'key' => 'task_manager',
        'name' => 'taskmanager::app.acl.task_manager',
        'route' => 'admin.task_manager.tasks.index',
        'sort' => 7,
    ], [
        'key' => 'task_manager.tasks',
        'name' => 'taskmanager::app.acl.tasks',
        'route' => ['admin.task_manager.tasks.index'],
        'sort' => 1,
    ], [
        'key' => 'task_manager.tasks.create',
        'name' => 'taskmanager::app.acl.create',
        'route' => ['admin.task_manager.tasks.create', 'admin.task_manager.tasks.store'],
        'sort' => 2,
    ], [
        'key' => 'task_manager.tasks.edit',
        'name' => 'taskmanager::app.acl.edit',
        'route' => ['admin.task_manager.tasks.edit', 'admin.task_manager.tasks.update'],
        'sort' => 3,
    ], [
        'key' => 'task_manager.tasks.delete',
        'name' => 'taskmanager::app.acl.delete',
        'route' => ['admin.task_manager.tasks.delete', 'admin.task_manager.tasks.mass_delete'],
        'sort' => 4,
    ], [
        'key' => 'task_manager.tasks.view',
        'name' => 'taskmanager::app.acl.view',
        'route' => 'admin.task_manager.tasks.view',
        'sort' => 5,
    ], [
        'key' => 'task_manager.task_groups',
        'name' => 'taskmanager::app.acl.task_groups',
        'route' => 'admin.task_manager.task_groups.index',
        'sort' => 2,
    ], [
        'key' => 'task_manager.task_groups.create',
        'name' => 'taskmanager::app.acl.create',
        'route' => ['admin.task_manager.task_groups.create', 'admin.task_manager.task_groups.store'],
        'sort' => 1,
    ], [
        'key' => 'task_manager.task_groups.edit',
        'name' => 'taskmanager::app.acl.edit',
        'route' => ['admin.task_manager.task_groups.edit', 'admin.task_manager.task_groups.update'],
        'sort' => 2,
    ], [
        'key' => 'task_manager.task_groups.delete',
        'name' => 'taskmanager::app.acl.delete',
        'route' => ['admin.task_manager.task_groups.delete', 'admin.task_manager.task_groups.mass_delete'],
        'sort' => 3,
    ],
    [
        'key' => 'task_manager.task_groups.manage-members',
        'name' => 'taskmanager::app.acl.manage_members',
        'route' => 'admin.task_manager.task_groups.manage-members.index',
        'sort' => 3,
    ],
    [
        'key' => 'task_manager.task_groups.manage-members.assign',
        'name' => 'taskmanager::app.acl.manage_members_assign',
        'route' => ['admin.task_manager.task_groups.manage-members.assign'],
        'sort' => 1,
    ],
    [
        'key' => 'task_manager.task_groups.manage-members.unassign',
        'name' => 'taskmanager::app.acl.manage_members_unassign',
        'route' => ['admin.task_manager.task_groups.manage-members.unassign', 'admin.task_manager.task_groups.manage-members.mass_delete'],
        'sort' => 2,
    ],
];
