<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Dashboard > Task Manager
Breadcrumbs::for('task_manager', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('taskmanager::app.layouts.task_manager'), route('admin.task_manager.tasks.index'));
});

// Dashboard > Task Manager > Tasks
Breadcrumbs::for('task_manager.tasks', function (BreadcrumbTrail $trail) {
    $trail->parent('task_manager');
    $trail->push(trans('taskmanager::app.layouts.tasks'), route('admin.task_manager.tasks.index'));
});

// Dashboard > Task Manager > Tasks > Create
Breadcrumbs::for('task_manager.tasks.create', function (BreadcrumbTrail $trail) {
    $trail->parent('task_manager.tasks');
    $trail->push(trans('taskmanager::app.task_manager.tasks.create.title'), route('admin.task_manager.tasks.create'));
});

// Dashboard > Task Manager > Tasks > Edit
Breadcrumbs::for('task_manager.tasks.edit', function (BreadcrumbTrail $trail, $task) {
    $trail->parent('task_manager.tasks');
    $trail->push(trans('taskmanager::app.task_manager.tasks.edit.title'), route('admin.task_manager.tasks.edit', $task->id));
});

// Dashboard > Task Manager > Tasks > View
Breadcrumbs::for('task_manager.tasks.view', function (BreadcrumbTrail $trail, $task) {
    $trail->parent('task_manager.tasks');
    $trail->push('#'.$task->id, route('admin.task_manager.tasks.view', $task->id));
});

// Dashboard > Task Manager > TaskGroups
Breadcrumbs::for('task_manager.task_groups', function (BreadcrumbTrail $trail) {
    $trail->parent('task_manager');
    $trail->push(trans('taskmanager::app.layouts.task_groups'), route('admin.task_manager.task_groups.index'));
});

// Dashboard > Task Manager > TaskGroups > Create
Breadcrumbs::for('task_manager.task_groups.create', function (BreadcrumbTrail $trail) {
    $trail->parent('task_manager.task_groups');
    $trail->push(trans('taskmanager::app.task_manager.task_groups.create.title'), route('admin.task_manager.task_groups.create'));
});

// Dashboard > Task Manager > TaskGroups > Edit
Breadcrumbs::for('task_manager.task_groups.edit', function (BreadcrumbTrail $trail, $group) {
    $trail->parent('task_manager.task_groups');
    $trail->push(trans('taskmanager::app.task_manager.task_groups.edit.title'), route('admin.task_manager.task_groups.edit', $group->id));
});

// Dashboard > Task Manager > TaskGroups > Manage
Breadcrumbs::for('task_manager.task_groups.manage-members', function (BreadcrumbTrail $trail, $group) {
    $trail->parent('task_manager.task_groups');

    $trail->push(
        trans('taskmanager::app.task_manager.task_groups.manage-members.title', [
            'name' => $group->name,
        ]),
        route('admin.task_manager.task_groups.manage-members.index', $group->id)
    );
});
