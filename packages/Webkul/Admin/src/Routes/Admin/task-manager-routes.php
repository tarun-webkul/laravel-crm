<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\TaskManager\TaskGroup\TaskGroupController;
use Webkul\Admin\Http\Controllers\TaskManager\Tasks\TaskController;

Route::prefix('task_manager')->group(function () {

    /**
     * Tasks routes.
     */
    Route::controller(TaskController::class)
        ->prefix('tasks')
        ->group(function () {

            Route::get('', 'index')->name('admin.task_manager.tasks.index');

            Route::get('create', 'create')->name('admin.task_manager.tasks.create');

            Route::post('create', 'store')->name('admin.task_manager.tasks.store');

            Route::get('edit/{id?}', 'edit')->name('admin.task_manager.tasks.edit');

            Route::put('edit/{id}', 'update')->name('admin.task_manager.tasks.update');

            Route::delete('{id}', 'destroy')->name('admin.task_manager.tasks.delete');

            Route::put('mass-destroy', 'massDestroy')->name('admin.task_manager.tasks.mass_delete');

        });

    /**
     * Task groups routes.
     */
    Route::controller(TaskGroupController::class)
        ->prefix('task_groups')
        ->group(function () {

            Route::get('', 'index')->name('admin.task_manager.task_groups.index');

            Route::get('create', 'create')->name('admin.task_manager.task_groups.create');

            Route::post('create', 'store')->name('admin.task_manager.task_groups.store');

            Route::get('edit/{id?}', 'edit')->name('admin.task_manager.task_groups.edit');

            Route::put('edit/{id}', 'update')->name('admin.task_manager.task_groups.update');

            Route::delete('{id}', 'destroy')->name('admin.task_manager.task_groups.delete');

            Route::put('mass-destroy', 'massDestroy')->name('admin.task_manager.task_groups.mass_delete');
        });

});
