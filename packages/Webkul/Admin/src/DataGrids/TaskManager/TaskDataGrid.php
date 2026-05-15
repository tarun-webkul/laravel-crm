<?php

namespace Webkul\Admin\DataGrids\TaskManager;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class TaskDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        $query = DB::table('tasks')
            ->addSelect(
                'tasks.id',
                'tasks.title',
                'tasks.description',
                'tasks.created_at',
                'tasks.group_id',
                'task_groups.name as task_group_name',
            )
            ->leftJoin('task_groups', 'task_groups.id', '=', 'tasks.group_id');

        $this->addFilter('id', 'tasks.id');
        $this->addFilter('name', 'tasks.name');
        $this->addFilter('created_at', 'tasks.created_at');
        $this->addFilter('task_group_name', 'task_groups.name');

        return $query;
    }

    /**
     * Add columns.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index' => 'id',
            'label' => trans('admin::app.task_manager.tasks.index.datagrid.id'),
            'type' => 'integer',
            'filterable' => true,
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'title',
            'label' => trans('admin::app.task_manager.tasks.index.datagrid.name'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index' => 'task_group_name',
            'label' => trans('admin::app.task_manager.tasks.index.datagrid.task_group'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index' => 'created_at',
            'label' => trans('admin::app.task_manager.tasks.index.datagrid.created-at'),
            'type' => 'date',
            'searchable' => true,
            'filterable' => true,
            'filterable_type' => 'date_range',
            'sortable' => true,
            'closure' => fn ($row) => core()->formatDate($row->created_at),
        ]);
    }

    // /**
    //  * Prepare actions.
    //  */
    // public function prepareActions(): void
    // {
    //     if (bouncer()->hasPermission('task_manager.task_groups.edit')) {
    //         $this->addAction([
    //             'icon' => 'icon-edit',
    //             'title' => trans('admin::app.task_manager.tasks.index.datagrid.edit'),
    //             'method' => 'GET',
    //             'url' => fn ($row) => route('admin.task_manager.tasks.edit', $row->id),
    //         ]);
    //     }

    //     if (bouncer()->hasPermission('task_manager.task_groups.delete')) {
    //         $this->addAction([
    //             'icon' => 'icon-delete',
    //             'title' => trans('admin::app.task_manager.tasks.index.datagrid.delete'),
    //             'method' => 'DELETE',
    //             'url' => fn ($row) => route('admin.task_manager.tasks.delete', $row->id),
    //         ]);
    //     }
    // }

    // /**
    //  * Prepare mass actions.
    //  */
    // public function prepareMassActions(): void
    // {
    //     $this->addMassAction([
    //         'icon' => 'icon-delete',
    //         'title' => trans('admin::app.task_manager.tasks.index.datagrid.delete'),
    //         'method' => 'PUT',
    //         'url' => route('admin.task_manager.tasks.mass_delete'),
    //     ]);
    // }
}
