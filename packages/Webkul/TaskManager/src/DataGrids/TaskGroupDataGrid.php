<?php

namespace Webkul\TaskManager\DataGrids;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class TaskGroupDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        $query = DB::table('task_groups')
            ->addSelect(
                'task_groups.id',
                'task_groups.name',
                'task_groups.description',
                'task_groups.created_at',
                'task_groups.created_by',
            );

        $this->addFilter('id', 'task_groups.id');
        $this->addFilter('name', 'task_groups.name');
        $this->addFilter('created_by', 'task_groups.created_by');
        $this->addFilter('created_at', 'task_groups.created_at');

        return $query;
    }

    /**
     * Add columns.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index' => 'id',
            'label' => trans('taskmanager::app.task_manager.task_groups.index.datagrid.id'),
            'type' => 'integer',
            'filterable' => true,
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'name',
            'label' => trans('taskmanager::app.task_manager.task_groups.index.datagrid.name'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
        ]);

        /**
         * CREATED BY
         */
        $this->addColumn([
            'index' => 'created_by',
            'label' => trans('taskmanager::app.task_manager.task_groups.index.datagrid.created-by'),
            'type' => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable' => true,
            'filterable_type' => 'dropdown',
            'filterable_options' => app(config('auth.providers.users.model'))->all()
                ->map(fn ($user) => [
                    'label' => $user->name." ({$user->email})",
                    'value' => $user->id,
                ])
                ->toArray(),
            'closure' => function ($row) {
                if (! $row->created_by) {
                    return '--';
                }
                $user = app(config('auth.providers.users.model'))->find($row->created_by);

                return $user ? $user->name." ({$user->email})" : '--';
            },
        ]);

        $this->addColumn([
            'index' => 'members_count',
            'label' => trans('taskmanager::app.task_manager.task_groups.index.datagrid.members-count'),
            'type' => 'string',
            'searchable' => false,
            'sortable' => false,
            'filterable' => false,
            'closure' => function ($row) {
                $membersCount = DB::table('task_group_users')
                    ->where('group_id', $row->id)
                    ->count();

                return $membersCount;
            },
        ]);

        $this->addColumn([
            'index' => 'created_at',
            'label' => trans('taskmanager::app.task_manager.task_groups.index.datagrid.created-at'),
            'type' => 'date',
            'searchable' => true,
            'filterable' => true,
            'filterable_type' => 'date_range',
            'sortable' => true,
            'closure' => fn ($row) => core()->formatDate($row->created_at),
        ]);
    }

    /**
     * Prepare actions.
     */
    public function prepareActions(): void
    {
        if (bouncer()->hasPermission('task_manager.task_groups.manage')) {
            $this->addAction([
                'icon' => 'icon-user',
                'title' => trans('taskmanager::app.task_manager.task_groups.index.datagrid.manage-members'),
                'method' => 'GET',
                'url' => fn ($row) => route('admin.task_manager.task_groups.manage-members.index', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('task_manager.task_groups.edit')) {
            $this->addAction([
                'icon' => 'icon-edit',
                'title' => trans('taskmanager::app.task_manager.task_groups.index.datagrid.edit'),
                'method' => 'GET',
                'url' => fn ($row) => route('admin.task_manager.task_groups.edit', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('task_manager.task_groups.delete')) {
            $this->addAction([
                'icon' => 'icon-delete',
                'title' => trans('taskmanager::app.task_manager.task_groups.index.datagrid.delete'),
                'method' => 'DELETE',
                'url' => fn ($row) => route('admin.task_manager.task_groups.delete', $row->id),
            ]);
        }
    }

    /**
     * Prepare mass actions.
     */
    public function prepareMassActions(): void
    {
        $this->addMassAction([
            'icon' => 'icon-delete',
            'title' => trans('taskmanager::app.task_manager.task_groups.index.datagrid.delete'),
            'method' => 'PUT',
            'url' => route('admin.task_manager.task_groups.mass_delete'),
        ]);
    }
}
