
<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('taskmanager::app.task_manager.tasks.create.title')
    </x-slot>

    {!! view_render_event('admin.task_manager.tasks.create.form.before') !!}

    <x-admin::form
        :action="route('admin.task_manager.tasks.store')"
        method="POST"
    >
    
        <div class="flex flex-col gap-4">
            <div class="scroll-reactive-sticky sticky top-[60px] z-[1000] flex items-center justify-between rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    {!! view_render_event('admin.task_manager.tasks.create.breadcrumbs.before') !!}

                    <!-- Breadcrumbs -->
                    <x-admin::breadcrumbs name="task_manager.tasks.create" />

                    {!! view_render_event('admin.task_manager.tasks.create.breadcrumbs.after') !!}

                    <div class="text-xl font-bold dark:text-gray-300">
                        @lang('taskmanager::app.task_manager.tasks.create.title')
                    </div>
                </div>

                <div class="flex items-center gap-x-2.5">
                    <div class="flex items-center gap-x-2.5">
                        {!! view_render_event('admin.task_manager.tasks.create.save_buttons.before') !!}

                        <button
                            type="submit"
                            class="primary-button"
                        >
                            @lang('taskmanager::app.task_manager.tasks.create.save-btn')
                        </button>

                        {!! view_render_event('admin.task_manager.tasks.create.save_buttons.after') !!}
                    </div>
                </div>
            </div>

            <div class="box-shadow rounded-lg border border-gray-300 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                {!! view_render_event('admin.task_manager.tasks.create.form_controls.before') !!}

                <x-admin::attributes
                    :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                        'entity_type' => 'tasks',
                    ])"
                    :custom-validations="[
                        'title' => ['required', 'max:100'],
                        'description' => ['nullable', 'max:255'],
                        'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
                        'group_id' => ['required', 'integer', 'exists:task_groups,id'],

                        'status' => ['required', 'in:pending,in_progress,on_hold,completed,cancelled'],
                        'priority' => ['required', 'in:low,medium,high,critical'],

                        'deadline' => ['nullable', 'date'],
                    ]"
                />

                {!! view_render_event('admin.task_manager.tasks.create.form_controls.after') !!}
            </div>
        </div>
    </x-admin::form>

    {!! view_render_event('admin.task_manager.tasks.create.form.after') !!}
</x-admin::layouts>
