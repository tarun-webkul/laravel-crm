<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\TaskManager\TaskGroup\TaskGroupController;

Route::prefix('task_manager')->group(function () {

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
