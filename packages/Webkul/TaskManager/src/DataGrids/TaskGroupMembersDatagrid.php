<?php

namespace Webkul\TaskManager\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class TaskGroupMembersDatagrid extends DataGrid
{
    /**
     * Prepare query builder
     */
    public function prepareQueryBuilder()
    {
        $taskGroupId = request()->route('taskGroupId');

        return DB::table('task_group_users')
            ->join('users', 'users.id', '=', 'task_group_users.user_id')
            ->where('task_group_users.group_id', $taskGroupId)
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'task_group_users.created_at'
            );
    }

    /**
     * Columns
     */
    public function prepareColumns()
    {
        $this->addColumn([
            'index' => 'id',
            'label' => trans('taskmanager::app.task_manager.task_groups.manage-members.datagrid.id'),
            'type' => 'integer',
            'sortable' => true,
            'filterable' => true,
            'searchable' => true,
        ]);

        $this->addColumn([
            'index' => 'name',
            'label' => trans('taskmanager::app.task_manager.task_groups.manage-members.datagrid.name'),
            'type' => 'string',
            'sortable' => true,
            'filterable' => true,
            'searchable' => true,
        ]);

        $this->addColumn([
            'index' => 'email',
            'label' => trans('taskmanager::app.task_manager.task_groups.manage-members.datagrid.email'),
            'type' => 'string',
            'sortable' => true,
            'filterable' => true,
            'searchable' => true,
        ]);

        $this->addColumn([
            'index' => 'created_at',
            'label' => trans('taskmanager::app.task_manager.task_groups.manage-members.datagrid.created_at'),
            'type' => 'date',
            'sortable' => true,
            'filterable' => true,
            'filterable_type' => 'date_range',
            'closure' => fn ($row) => core()->formatDate($row->created_at),
        ]);
    }

    /**
     * Prepare mass actions.
     */
    public function prepareMassActions(): void
    {
        $this->addMassAction([
            'icon' => 'icon-delete',
            'title' => trans('taskmanager::app.task_manager.task_groups.manage-members.datagrid.delete'),
            'method' => 'PUT',
            'url' => route('admin.task_manager.task_groups.manage-members.mass_delete', request()->route('taskGroupId')),
        ]);
    }

    /**
     * Prepare actions.
     */
    public function prepareActions(): void
    {
        $this->addAction([
            'icon' => 'icon-delete',
            'title' => trans('taskmanager::app.task_manager.task_groups.manage-members.datagrid.unassign'),
            'method' => 'DELETE',
            'url' => function ($row) {
                return route('admin.task_manager.task_groups.manage-members.unassign', [
                    'taskGroupId' => request()->route('taskGroupId'),
                    'userId' => $row->id,
                ]);
            },
        ]);
    }
}
