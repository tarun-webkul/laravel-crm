<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.task_manager.tasks.index.title')
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="scroll-reactive-sticky sticky top-[60px] z-[1000] flex items-center justify-between rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                {!! view_render_event('admin.task_manager.tasks.index.breadcrumbs.before') !!}

                <!-- Breadcrumbs -->
                <x-admin::breadcrumbs name="task_manager.tasks" />

                {!! view_render_event('admin.task_manager.tasks.index.breadcrumbs.after') !!}
                
                <div class="text-xl font-bold dark:text-gray-300">
                    @lang('admin::app.task_manager.tasks.index.title')
                </div>
            </div>

            <div class="flex items-center gap-x-2.5">
                <div class="flex items-center gap-x-2.5">
                    {!! view_render_event('admin.task_manager.tasks.index.create_button.before') !!}

                    @if (bouncer()->hasPermission('task_manager.tasks.create'))
                        <!-- Create button for task -->
                        <a
                            href="{{ route('admin.task_manager.tasks.create') }}"
                            class="primary-button"
                        >
                            @lang('admin::app.task_manager.tasks.index.create-btn')
                        </a>
                    @endif

                    {!! view_render_event('admin.task_manager.tasks.index.create_button.after') !!}
                </div>
            </div>
        </div>

        {!! view_render_event('admin.task_manager.tasks.datagrid.index.before') !!}

        <x-admin::datagrid :src="route('admin.task_manager.tasks.index')" >
            <!-- DataGrid Shimmer -->
            <x-admin::shimmer.datagrid />
        </x-admin::datagrid>

        {!! view_render_event('admin.task_manager.tasks.datagrid.index.after') !!}
    </div>
</x-admin::layouts>
