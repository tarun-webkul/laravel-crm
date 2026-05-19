<?php

use Illuminate\Support\Facades\Route;
use Webkul\TaskManager\Http\Controllers\TaskGroup\TaskGroupController;
use Webkul\TaskManager\Http\Controllers\TaskGroup\TaskGroupUserController;
use Webkul\TaskManager\Http\Controllers\Tasks\TaskCommentController;
use Webkul\TaskManager\Http\Controllers\Tasks\TaskController;

Route::prefix('task-manager')
    ->group(function () {

        /**
         * Tasks routes.
         */
        Route::controller(TaskController::class)
            ->prefix('tasks')
            ->group(function () {

                Route::get('', 'index')->name('admin.task_manager.tasks.index');

                Route::get('view/{id}', 'view')->name('admin.task_manager.tasks.view');

                Route::get('create', 'create')->name('admin.task_manager.tasks.create');

                Route::post('create', 'store')->name('admin.task_manager.tasks.store');

                Route::get('edit/{id?}', 'edit')->name('admin.task_manager.tasks.edit');

                Route::put('edit/{id}', 'update')->name('admin.task_manager.tasks.update');

                Route::delete('{id}', 'destroy')->name('admin.task_manager.tasks.delete');

                Route::put('mass-destroy', 'massDestroy')->name('admin.task_manager.tasks.mass_delete');

                /**
                 *  Task Comments
                 * */
                Route::controller(TaskCommentController::class)
                    ->prefix('{taskId}/comments')
                    ->group(function () {

                        Route::get('', 'index')->name('admin.task_manager.tasks.comments.index');

                        Route::post('create', 'store')->name('admin.task_manager.tasks.comments.store');

                        Route::put('edit/{id}', 'update')->name('admin.task_manager.tasks.comments.update');

                        Route::delete('{id}', 'destroy')->name('admin.task_manager.tasks.comments.destroy');
                    });

            });

        /**
         * Task groups routes.
         */
        Route::controller(TaskGroupController::class)
            ->prefix('task-groups')
            ->group(function () {

                Route::get('', 'index')->name('admin.task_manager.task_groups.index');

                Route::get('create', 'create')->name('admin.task_manager.task_groups.create');

                Route::post('create', 'store')->name('admin.task_manager.task_groups.store');

                Route::get('edit/{id?}', 'edit')->name('admin.task_manager.task_groups.edit');

                Route::put('edit/{id}', 'update')->name('admin.task_manager.task_groups.update');

                Route::delete('{id}', 'destroy')->name('admin.task_manager.task_groups.delete');

                Route::put('mass-destroy', 'massDestroy')->name('admin.task_manager.task_groups.mass_delete');

                Route::controller(TaskGroupUserController::class)
                    ->prefix('{taskGroupId}/manage-members')
                    ->group(function () {

                        Route::get('/', 'index')
                            ->name('admin.task_manager.task_groups.manage-members.index');

                        Route::post('assign', 'assign')
                            ->name('admin.task_manager.task_groups.manage-members.assign');

                        Route::delete('{userId}', 'unassign')
                            ->name('admin.task_manager.task_groups.manage-members.unassign');

                        Route::put('mass-destroy', 'massDestroy')
                            ->name('admin.task_manager.task_groups.manage-members.mass_delete');
                    });

            });

    });
