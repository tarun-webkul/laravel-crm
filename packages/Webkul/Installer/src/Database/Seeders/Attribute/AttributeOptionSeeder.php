<?php

namespace Webkul\Installer\Database\Seeders\Attribute;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeOptionSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @param  array  $parameters
     * @return void
     */
    public function run($parameters = [])
    {
        DB::table('attribute_options')->delete();

        $defaultLocale = $parameters['locale'] ?? config('app.locale');

        /**
         * Task Status Attribute ID
         */
        $statusAttributeId = DB::table('attributes')
            ->where('code', 'status')
            ->where('entity_type', 'tasks')
            ->value('id');

        /**
         * Task Priority Attribute ID
         */
        $priorityAttributeId = DB::table('attributes')
            ->where('code', 'priority')
            ->where('entity_type', 'tasks')
            ->value('id');

        if (! $statusAttributeId || ! $priorityAttributeId) {
            $this->command->warn('Task attributes not found. Please run AttributeSeeder first.');

            return;
        }

        DB::table('attribute_options')->insert([
            /**
             * Task Status Options
             */
            [
                'attribute_id' => $statusAttributeId,
                'name' => trans('installer::app.seeders.attribute-options.task-statuses.pending', [], $defaultLocale),
                'sort_order' => 1,
            ], [
                'attribute_id' => $statusAttributeId,
                'name' => trans('installer::app.seeders.attribute-options.task-statuses.in-progress', [], $defaultLocale),
                'sort_order' => 2,
            ], [
                'attribute_id' => $statusAttributeId,
                'name' => trans('installer::app.seeders.attribute-options.task-statuses.on-hold', [], $defaultLocale),
                'sort_order' => 3,
            ], [
                'attribute_id' => $statusAttributeId,
                'name' => trans('installer::app.seeders.attribute-options.task-statuses.completed', [], $defaultLocale),
                'sort_order' => 4,
            ], [
                'attribute_id' => $statusAttributeId,
                'name' => trans('installer::app.seeders.attribute-options.task-statuses.cancelled', [], $defaultLocale),
                'sort_order' => 5,
            ],

            /**
             * Task Priority Options
             */
            [
                'attribute_id' => $priorityAttributeId,
                'name' => trans('installer::app.seeders.attribute-options.task-priorities.low', [], $defaultLocale),
                'sort_order' => 1,
            ], [
                'attribute_id' => $priorityAttributeId,
                'name' => trans('installer::app.seeders.attribute-options.task-priorities.medium', [], $defaultLocale),
                'sort_order' => 2,
            ], [
                'attribute_id' => $priorityAttributeId,
                'name' => trans('installer::app.seeders.attribute-options.task-priorities.high', [], $defaultLocale),
                'sort_order' => 3,
            ], [
                'attribute_id' => $priorityAttributeId,
                'name' => trans('installer::app.seeders.attribute-options.task-priorities.critical', [], $defaultLocale),
                'sort_order' => 4,
            ],
        ]);
    }
}
