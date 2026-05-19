
<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('taskmanager::app.task_manager.task_groups.edit.title')
    </x-slot>

    {!! view_render_event('admin.task_manager.task_groups.edit.form.before') !!}

    <x-admin::form
        :action="route('admin.task_manager.task_groups.update', $taskGroup->id)"
        method="PUT"
    >
        <div class="flex flex-col gap-4">
            <div class="scroll-reactive-sticky sticky top-[60px] z-[1000] flex items-center justify-between rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    {!! view_render_event('admin.task_manager.task_groups.edit.breadcrumbs.before', ['taskGroup' => $taskGroup]) !!}

                    <x-admin::breadcrumbs 
                        name="task_manager.task_groups.edit" 
                        :entity="$taskGroup"
                    />

                    {!! view_render_event('admin.task_manager.task_groups.edit.breadcrumbs.after', ['taskGroup' => $taskGroup]) !!}

                    <div class="text-xl font-bold dark:text-gray-300">
                        @lang('taskmanager::app.task_manager.task_groups.edit.title')
                    </div>
                </div>

                <div class="flex items-center gap-x-2.5">
                    <div class="flex items-center gap-x-2.5">
                        {!! view_render_event('admin.task_manager.task_groups.edit.save_button.before', ['taskGroup' => $taskGroup]) !!}

                        <!-- Save button for task group -->
                        <button
                            type="submit"
                            class="primary-button"
                        >
                            @lang('taskmanager::app.task_manager.task_groups.edit.save-btn')
                        </button>

                        {!! view_render_event('admin.task_manager.task_groups.edit.save_button.after', ['taskGroup' => $taskGroup]) !!}
                    </div>
                </div>
            </div>

            <div class="box-shadow rounded-lg border border-gray-300 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                {!! view_render_event('admin.task_manager.task_groups.edit.form_controls.before') !!}

                <x-admin::attributes
                    :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                        'entity_type' => 'task_groups',
                    ])"
                    :custom-validations="[
                        'name' => [
                            'max:100',
                        ],
                        'address' => [
                            'max:100',
                        ],
                        'postcode' => [
                            'postcode',
                        ],
                    ]"
                    :entity="$taskGroup"
                />
                
                {!! view_render_event('admin.task_manager.task_groups.edit.form_controls.after') !!}
            </div>
        </div>
    </x-admin::form>

    {!! view_render_event('admin.task_manager.task_groups.edit.form.after') !!}
</x-admin::layouts>
