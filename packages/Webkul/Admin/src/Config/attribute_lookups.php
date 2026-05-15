<?php

return [
    'leads' => [
        'name' => 'Leads',
        'repository' => 'Webkul\Lead\Repositories\LeadRepository',
        'label_column' => 'title',
    ],

    'lead_sources' => [
        'name' => 'Lead Sources',
        'repository' => 'Webkul\Lead\Repositories\SourceRepository',
    ],

    'lead_types' => [
        'name' => 'Lead Types',
        'repository' => 'Webkul\Lead\Repositories\TypeRepository',
    ],

    'lead_pipelines' => [
        'name' => 'Lead Pipelines',
        'repository' => 'Webkul\Lead\Repositories\PipelineRepository',
    ],

    'lead_pipeline_stages' => [
        'name' => 'Lead Pipeline Stages',
        'repository' => 'Webkul\Lead\Repositories\StageRepository',
    ],

    'users' => [
        'name' => 'Sales Owners',
        'repository' => 'Webkul\User\Repositories\UserRepository',
    ],

    'task_groups' => [
        'name' => 'TaskGroups',
        'repository' => 'Webkul\TaskManager\Repositories\TaskGroupRepository',
    ],

    'tasks' => [
        'name' => 'Tasks',
        'repository' => 'Webkul\TaskManager\Repositories\TaskRepository',
    ],

    'organizations' => [
        'name' => 'Organizations',
        'repository' => 'Webkul\Contact\Repositories\OrganizationRepository',
    ],

    'persons' => [
        'name' => 'Persons',
        'repository' => 'Webkul\Contact\Repositories\PersonRepository',
    ],

    'warehouses' => [
        'name' => 'Warehouses',
        'repository' => 'Webkul\Warehouse\Repositories\WarehouseRepository',
    ],

    'locations' => [
        'name' => 'Locations',
        'repository' => 'Webkul\Warehouse\Repositories\LocationRepository',
    ],

    'attribute_options' => [
        'key' => 'attribute_options',
        'name' => 'Attribute Options',
        'model' => 'Webkul\Attribute\Models\AttributeOption',
    ],

];
