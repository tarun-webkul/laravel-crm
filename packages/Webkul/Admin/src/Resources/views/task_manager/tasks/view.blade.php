<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.task_manager.tasks.view.title', ['title' => strip_tags($task->title)])
    </x-slot>

    <div class="flex flex-col gap-4">

        {!! view_render_event('admin.task_manager.tasks.view.before', ['task' => $task]) !!}

        <div class="rounded-lg border border-gray-300 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="flex w-full flex-col gap-2 p-4">

                <!-- Breadcrumbs -->
                <x-admin::breadcrumbs
                    name="task_manager.tasks.view"
                    :entity="$task"
                />

                <!-- Title & Description -->
                <div class="flex flex-col gap-1">
                    {!! view_render_event('admin.task_manager.tasks.view.title.before', ['task' => $task]) !!}

                    <h3 class="text-lg font-bold dark:text-white">
                        {{ $task->title }}
                    </h3>

                    @if ($task->description)
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $task->description }}
                        </p>
                    @endif

                    {!! view_render_event('admin.task_manager.tasks.view.title.after', ['task' => $task]) !!}
                </div>

                <!-- Status & Priority Badges -->
                <div class="flex flex-wrap items-center gap-2">
                    {!! view_render_event('admin.task_manager.tasks.view.badges.before', ['task' => $task]) !!}

                    @php
                        $statusOption   = $task->statusOptions()->firstWhere('id', $task->status);
                        $priorityOption = $task->priorityOptions()->firstWhere('id', $task->priority);
                    @endphp

                    <!-- Status Badge -->
                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                        <span class="size-1.5 rounded-full bg-gray-400"></span>
                        {{ $statusOption?->name ?? __('admin::app.task_manager.tasks.view.no-status') }}
                    </span>

                    <!-- Priority Badge -->
                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold bg-gray-100 text-gray-400 dark:bg-gray-700 dark:text-gray-500">
                        <span class="size-1.5 rounded-full bg-gray-400"></span>
                        {{ $priorityOption?->name ?? __('admin::app.task_manager.tasks.view.no-priority') }}
                    </span>

                    {!! view_render_event('admin.task_manager.tasks.view.badges.after', ['task' => $task]) !!}
                </div>

            </div>
        </div>

        <div class="flex flex-col gap-4 lg:flex-row lg:items-start">

            {!! view_render_event('admin.task_manager.tasks.view.left.before', ['task' => $task]) !!}

            <div class="[&>div:last-child]:border-b-0 w-full rounded-lg border border-gray-300 bg-white dark:border-gray-800 dark:bg-gray-900 lg:sticky lg:top-[73px] lg:min-w-[394px] lg:max-w-[394px] lg:self-start">

                {!! view_render_event('admin.task_manager.tasks.view.attributes.before', ['task' => $task]) !!}

                <div class="flex w-full flex-col">
                    <x-admin::accordion class="select-none !border-none">
                        <x-slot:header class="!p-0">
                            <div class="flex items-center justify-between px-4 py-3">
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    @lang('admin::app.task_manager.tasks.view.details')
                                </h4>
                            </div>
                        </x-slot>

                        <x-slot:content class="mt-1 !px-0 !pb-0">
                            {!! view_render_event('admin.task_manager.tasks.view.attributes.form_controls.before', ['task' => $task]) !!}

                            <div class="grid grid-cols-2 gap-px border-t border-gray-100 dark:border-gray-800">

                                <!-- Assigned To -->
                                <div class="flex flex-col gap-1 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <span class="text-xs text-gray-400">
                                        @lang('admin::app.task_manager.tasks.view.assigned-to')
                                    </span>
                                    @if ($task->assignee)
                                        <div class="flex flex-col">
                                            <span class="text-sm dark:text-white">{{ $task->assignee->name }}</span>
                                            <span class="text-xs text-gray-400">{{ $task->assignee->email }}</span>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">--</span>
                                    @endif
                                </div>

                                <!-- Created By -->
                                <div class="flex flex-col gap-1 border-t border-gray-100 px-4 py-3 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-800">
                                    <span class="text-xs text-gray-400">
                                        @lang('admin::app.task_manager.tasks.view.created-by')
                                    </span>
                                    @if ($task->creator)
                                        <div class="flex flex-col">
                                            <span class="text-sm dark:text-white">{{ $task->creator->name }}</span>
                                            <span class="text-xs text-gray-400">{{ $task->creator->email }}</span>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">--</span>
                                    @endif
                                </div>


                                <!-- Deadline -->
                                <div class="flex flex-col gap-1 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <div class="text-xs text-gray-400 flex items-center gap-1">
                                        @lang('admin::app.task_manager.tasks.view.deadline')
                                        @if ($task->deadline && $task->deadline->isPast())
                                            <span class="block text-xs text-red-500">
                                                (@lang('admin::app.task_manager.tasks.view.overdue'))
                                            </span>
                                        @endif
                                    </div>
                                    <span class="text-sm dark:text-white @if($task->deadline && $task->deadline->isPast()) text-red-500 font-medium @endif">
                                        {{ $task->deadline ? $task->deadline->format('M d, Y') : '--' }}
                                    </span>
                                </div>

                                <!-- Created At -->
                                <div class="flex flex-col gap-1 border-t border-gray-100 px-4 py-3 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-800">
                                    <span class="text-xs text-gray-400">
                                        @lang('admin::app.task_manager.tasks.view.created-at')
                                    </span>
                                    <span class="text-sm dark:text-white">
                                        {{ $task->created_at->format('M d, Y') }}
                                    </span>
                                </div>

                            </div>

                            {!! view_render_event('admin.task_manager.tasks.view.attributes.form_controls.after', ['task' => $task]) !!}
                        </x-slot>
                    </x-admin::accordion>

                    {!! view_render_event('admin.task_manager.tasks.view.attributes.extra', ['task' => $task]) !!}
                </div>

                {!! view_render_event('admin.task_manager.tasks.view.attributes.after', ['task' => $task]) !!}
            </div>

            {!! view_render_event('admin.task_manager.tasks.view.left.after', ['task' => $task]) !!}



        </div>

        {!! view_render_event('admin.task_manager.tasks.view.after', ['task' => $task]) !!}

    </div>
</x-admin::layouts>