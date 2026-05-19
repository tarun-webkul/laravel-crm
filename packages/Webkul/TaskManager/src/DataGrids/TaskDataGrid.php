<?php

namespace Webkul\TaskManager\DataGrids;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;
use Webkul\TaskManager\Repositories\TaskGroupRepository;

class TaskDataGrid extends DataGrid
{
    protected TaskGroupRepository $taskGroupRepository;

    public function __construct(TaskGroupRepository $taskGroupRepository)
    {
        $this->taskGroupRepository = $taskGroupRepository;
    }

    /**
     * Get attribute options (for dropdown filters)
     */
    protected function getAttributeOptions(string $code): array
    {
        return Cache::remember("attr_options_$code", 3600, function () use ($code) {
            return DB::table('attributes as a')
                ->join('attribute_options as ao', 'ao.attribute_id', '=', 'a.id')
                ->where('a.code', $code)
                ->select('ao.id', 'ao.name')
                ->orderBy('ao.sort_order')
                ->get()
                ->map(fn ($row) => [
                    'label' => $row->name,
                    'value' => $row->id,
                ])
                ->toArray();
        });
    }

    /**
     * Get attribute map (id => name) for display
     */
    protected function getAttributeMap(string $code): array
    {
        return Cache::remember("attr_map_$code", 3600, function () use ($code) {
            return DB::table('attributes as a')
                ->join('attribute_options as ao', 'ao.attribute_id', '=', 'a.id')
                ->where('a.code', $code)
                ->pluck('ao.name', 'ao.id')
                ->toArray();
        });
    }

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
                'tasks.status',
                'tasks.priority',
                'task_groups.id as task_groups_id',
                'tasks.assigned_to',
            )
            ->leftJoin('task_groups', 'task_groups.id', '=', 'tasks.group_id');

        /**
         * Filters
         */
        $this->addFilter('id', 'tasks.id');
        $this->addFilter('title', 'tasks.title');
        $this->addFilter('task_group_name', 'task_groups.id');
        $this->addFilter('created_at', 'tasks.created_at');

        // attribute-based filters
        $this->addFilter('status', 'tasks.status');
        $this->addFilter('priority', 'tasks.priority');
        $this->addFilter('assigned_to', 'tasks.assigned_to');

        return $query;
    }

    /**
     * Columns
     */
    public function prepareColumns(): void
    {
        /**
         * ID
         */
        $this->addColumn([
            'index' => 'id',
            'label' => trans('taskmanager::app.task_manager.tasks.index.datagrid.id'),
            'type' => 'integer',
            'filterable' => true,
            'sortable' => true,
        ]);

        /**
         * TITLE
         */
        $this->addColumn([
            'index' => 'title',
            'label' => trans('taskmanager::app.task_manager.tasks.index.datagrid.title'),
            'type' => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable' => true,
        ]);

        /**
         * TASK GROUP
         */
        $this->addColumn([
            'index' => 'task_group_name',
            'label' => trans('taskmanager::app.task_manager.tasks.index.datagrid.task_group'),
            'type' => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable' => true,
            'filterable_type' => 'dropdown',
            'filterable_options' => $this->taskGroupRepository->all()
                ->map(fn ($group) => [
                    'label' => $group->name,
                    'value' => $group->id,
                ])
                ->toArray(),
            'closure' => function ($row) {
                if (! $row->task_groups_id) {
                    return '--';
                }

                return $row->task_group_name;
            },
        ]);

        /**
         * ASSIGNED TO
         */
        $this->addColumn([
            'index' => 'assigned_to',
            'label' => trans('taskmanager::app.task_manager.tasks.index.datagrid.assigned-to'),
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
                if (! $row->assigned_to) {
                    return '--';
                }
                $user = app(config('auth.providers.users.model'))->find($row->assigned_to);

                return $user ? $user->name." ({$user->email})" : '--';
            },
        ]);

        /**
         * STATUS
         */
        $statusMap = $this->getAttributeMap('status');

        $this->addColumn([
            'index' => 'status',
            'label' => trans('taskmanager::app.task_manager.tasks.index.datagrid.status'),
            'type' => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable' => true,
            'filterable_type' => 'dropdown',
            'filterable_options' => $this->getAttributeOptions('status'),
            'closure' => function ($row) use ($statusMap) {
                return $statusMap[$row->status] ?? $row->status;
            },
        ]);

        /**
         * PRIORITY
         */
        $priorityMap = $this->getAttributeMap('priority');

        $this->addColumn([
            'index' => 'priority',
            'label' => trans('taskmanager::app.task_manager.tasks.index.datagrid.priority'),
            'type' => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable' => true,
            'filterable_type' => 'dropdown',
            'filterable_options' => $this->getAttributeOptions('priority'),
            'closure' => function ($row) use ($priorityMap) {
                return $priorityMap[$row->priority] ?? $row->priority;
            },
        ]);

        /**
         * CREATED AT
         */
        $this->addColumn([
            'index' => 'created_at',
            'label' => trans('taskmanager::app.task_manager.tasks.index.datagrid.created-at'),
            'type' => 'date',
            'filterable' => true,
            'filterable_type' => 'date_range',
            'searchable' => true,
            'sortable' => true,

            'closure' => fn ($row) => core()->formatDate($row->created_at),
        ]);
    }

    /**
     * Actions
     */
    public function prepareActions(): void
    {

        if (bouncer()->hasPermission('task_manager.tasks.view')) {
            $this->addAction([
                'icon' => 'icon-eye',
                'title' => trans('taskmanager::app.task_manager.tasks.index.datagrid.view'),
                'method' => 'GET',
                'url' => fn ($row) => route('admin.task_manager.tasks.view', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('task_manager.tasks.edit')) {
            $this->addAction([
                'icon' => 'icon-edit',
                'title' => trans('taskmanager::app.task_manager.tasks.index.datagrid.edit'),
                'method' => 'GET',
                'url' => fn ($row) => route('admin.task_manager.tasks.edit', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('task_manager.tasks.delete')) {
            $this->addAction([
                'icon' => 'icon-delete',
                'title' => trans('taskmanager::app.task_manager.tasks.index.datagrid.delete'),
                'method' => 'DELETE',
                'url' => fn ($row) => route('admin.task_manager.tasks.delete', $row->id),
            ]);
        }
    }

    /**
     * Mass Actions
     */
    public function prepareMassActions(): void
    {
        $this->addMassAction([
            'icon' => 'icon-delete',
            'title' => trans('taskmanager::app.task_manager.tasks.index.datagrid.delete'),
            'method' => 'PUT',
            'url' => route('admin.task_manager.tasks.mass_delete'),
        ]);
    }
}
