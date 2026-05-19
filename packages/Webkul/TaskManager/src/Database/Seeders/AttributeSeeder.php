<?php

namespace Webkul\TaskManager\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @param  array  $parameters
     * @return void
     */
    public function run($parameters = [])
    {
        $now = Carbon::now();

        $defaultLocale = $parameters['locale'] ?? config('app.locale');

        $attributes = [

            /**
             * Tasks Attributes
             **/
            [
                'code' => 'title',
                'name' => trans('taskmanager::app.seeders.attributes.tasks.title', [], $defaultLocale),
                'type' => 'text',
                'entity_type' => 'tasks',
                'lookup_type' => null,
                'validation' => null,
                'sort_order' => 1,
                'is_required' => 1,
                'is_unique' => 0,
                'quick_add' => 1,
                'is_user_defined' => 0,
            ],
            [
                'code' => 'description',
                'name' => trans('taskmanager::app.seeders.attributes.tasks.description', [], $defaultLocale),
                'type' => 'textarea',
                'entity_type' => 'tasks',
                'lookup_type' => null,
                'validation' => null,
                'sort_order' => 2,
                'is_required' => 0,
                'is_unique' => 0,
                'quick_add' => 1,
                'is_user_defined' => 0,
            ],
            [
                'code' => 'status',
                'name' => trans('taskmanager::app.seeders.attributes.tasks.status', [], $defaultLocale),
                'type' => 'select',
                'entity_type' => 'tasks',
                'lookup_type' => null,
                'validation' => null,
                'sort_order' => 3,
                'is_required' => 1,
                'is_unique' => 0,
                'quick_add' => 1,
                'is_user_defined' => 0,
            ],
            [
                'code' => 'priority',
                'name' => trans('taskmanager::app.seeders.attributes.tasks.priority', [], $defaultLocale),
                'type' => 'select',
                'entity_type' => 'tasks',
                'lookup_type' => null,
                'validation' => null,
                'sort_order' => 4,
                'is_required' => 1,
                'is_unique' => 0,
                'quick_add' => 1,
                'is_user_defined' => 0,
            ],
            [
                'code' => 'deadline',
                'name' => trans('taskmanager::app.seeders.attributes.tasks.deadline', [], $defaultLocale),
                'type' => 'date',
                'entity_type' => 'tasks',
                'lookup_type' => null,
                'validation' => null,
                'sort_order' => 5,
                'is_required' => 0,
                'is_unique' => 0,
                'quick_add' => 0,
                'is_user_defined' => 0,
            ],
            [
                'code' => 'assigned_to',
                'name' => trans('taskmanager::app.seeders.attributes.tasks.assigned_to', [], $defaultLocale),
                'type' => 'lookup',
                'entity_type' => 'tasks',
                'lookup_type' => 'users',
                'validation' => null,
                'sort_order' => 6,
                'is_required' => 0,
                'is_unique' => 0,
                'quick_add' => 1,
                'is_user_defined' => 0,
            ],
            [
                'code' => 'group_id',
                'name' => trans('taskmanager::app.seeders.attributes.tasks.group', [], $defaultLocale),
                'type' => 'lookup',
                'entity_type' => 'tasks',
                'lookup_type' => 'task_groups',
                'validation' => null,
                'sort_order' => 7,
                'is_required' => 1,
                'is_unique' => 0,
                'quick_add' => 0,
                'is_user_defined' => 0,
            ],

            /**
             * Task Groups Attributes
             **/
            [
                'code' => 'name',
                'name' => trans('taskmanager::app.seeders.attributes.task-groups.name', [], $defaultLocale),
                'type' => 'text',
                'entity_type' => 'task_groups',
                'lookup_type' => null,
                'validation' => null,
                'sort_order' => 1,
                'is_required' => 1,
                'is_unique' => 0,
                'quick_add' => 1,
                'is_user_defined' => 0,
            ],
            [
                'code' => 'description',
                'name' => trans('taskmanager::app.seeders.attributes.task-groups.description', [], $defaultLocale),
                'type' => 'textarea',
                'entity_type' => 'task_groups',
                'lookup_type' => null,
                'validation' => null,
                'sort_order' => 2,
                'is_required' => 1,
                'is_unique' => 0,
                'quick_add' => 1,
                'is_user_defined' => 0,
            ],
            [
                'code' => 'created_by',
                'name' => trans('taskmanager::app.seeders.attributes.task-groups.created_by', [], $defaultLocale),
                'type' => 'lookup',
                'entity_type' => 'task_groups',
                'lookup_type' => 'users',
                'validation' => null,
                'sort_order' => 3,
                'is_required' => 1,
                'is_unique' => 0,
                'quick_add' => 1,
                'is_user_defined' => 0,
            ],
        ];

        foreach ($attributes as $attribute) {
            DB::table('attributes')->updateOrInsert(
                [
                    'code' => $attribute['code'],
                    'entity_type' => $attribute['entity_type'],
                ],
                array_merge($attribute, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
    }
}
