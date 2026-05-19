<x-admin::layouts>
    <x-slot:title>
        @lang('taskmanager::app.task_manager.tasks.view.title', [
            'title' => strip_tags($task->title)
        ])
    </x-slot>

    <div class="flex flex-col gap-4">

        <!-- Header -->
        <div class="rounded-lg border border-gray-300 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="flex flex-col gap-2 p-4">

                <x-admin::breadcrumbs
                    name="task_manager.tasks.view"
                    :entity="$task"
                />

                <div class="flex flex-col gap-1">

                    <h3 class="text-lg font-bold dark:text-white">
                        {{ $task->title }}
                    </h3>

                    @if ($task->description)
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $task->description }}
                        </p>
                    @endif

                </div>

                @php
                    $statusOption = $task->statusOptions()->firstWhere('id', $task->status);

                    $priorityOption = $task->priorityOptions()->firstWhere('id', $task->priority);
                @endphp

                <div class="flex flex-wrap items-center gap-2">

                    <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                        {{ $statusOption?->name }}
                    </span>

                    <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                        {{ $priorityOption?->name }}
                    </span>

                </div>

            </div>
        </div>

        <!-- Content -->
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start">

            <!-- Sidebar -->
            <div class="w-full rounded-lg border border-gray-300 bg-white dark:border-gray-800 dark:bg-gray-900 lg:max-w-[394px]">

                <x-admin::accordion
                    class="!border-none"
                    :is-active="false"
                >
                    <x-slot:header>
                        <div class="px-4 py-3">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                @lang('taskmanager::app.task_manager.tasks.view.details')
                            </h4>
                        </div>
                    </x-slot>

                    <x-slot:content>

                        <div class="grid grid-cols-2 border-t border-gray-100 dark:border-gray-800">

                            <div class="border-b border-r border-gray-100 px-4 py-3 dark:border-gray-800">
                                <p class="text-xs text-gray-400">
                                    @lang('taskmanager::app.task_manager.tasks.view.assigned-to')
                                </p>

                                @if ($task->assignee)
                                    <p class="text-sm dark:text-white">
                                        {{ $task->assignee->name }}
                                    </p>
                                @endif
                            </div>

                            <div class="border-b border-gray-100 px-4 py-3 dark:border-gray-800">
                                <p class="text-xs text-gray-400">
                                    @lang('taskmanager::app.task_manager.tasks.view.created-by')
                                </p>

                                @if ($task->creator)
                                    <p class="text-sm dark:text-white">
                                        {{ $task->creator->name }}
                                    </p>
                                @endif
                            </div>

                            <div class="border-r border-gray-100 px-4 py-3 dark:border-gray-800">
                                <p class="text-xs text-gray-400">
                                    @lang('taskmanager::app.task_manager.tasks.view.deadline')
                                </p>

                                <p class="text-sm dark:text-white">
                                    {{ $task->deadline?->format('M d, Y') ?? '--' }}
                                </p>
                            </div>

                            <div class="px-4 py-3">
                                <p class="text-xs text-gray-400">
                                    @lang('taskmanager::app.task_manager.tasks.view.created-at')
                                </p>

                                <p class="text-sm dark:text-white">
                                    {{ $task->created_at->format('M d, Y') }}
                                </p>
                            </div>

                        </div>

                    </x-slot:content>
                </x-admin::accordion>

            </div>

            <!-- Comments -->
            <div class="flex-1 min-w-0">

                <v-task-comments
                    task-id="{{ $task->id }}"
                    comments-url="{{ route('admin.task_manager.tasks.comments.index', $task->id) }}"
                    store-url="{{ route('admin.task_manager.tasks.comments.store', $task->id) }}"
                    csrf-token="{{ csrf_token() }}"
                >
                </v-task-comments>

            </div>

        </div>

    </div>

    @pushOnce('scripts')

        <script type="text/x-template" id="v-task-comments-template">

            <div class="rounded-lg border border-gray-300 bg-white dark:border-gray-800 dark:bg-gray-900">

                <!-- Header -->
                <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3 dark:border-gray-800">

                    <div class="flex items-center gap-2">

                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                            {{ __('taskmanager::app.task_manager.task_comments.index.title') }}
                        </h4>

                        <span
                            v-if="comments.length"
                            class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-600 dark:bg-gray-700 dark:text-gray-300"
                        >
                            @{{ filteredComments.length ?? 0 }}
                        </span>

                    </div>

                    <div class="flex items-center gap-2">

                        <!-- Tabs -->
                        <div class="flex rounded-md border border-gray-200 p-0.5 dark:border-gray-700">

                            <button
                                v-for="tab in tabs"
                                :key="tab.key"
                                type="button"
                                @click="activeTab = tab.key"
                                :class="[
                                    'rounded px-2.5 py-1 text-xs font-medium',
                                    activeTab === tab.key
                                        ? 'bg-blue-600 text-white'
                                        : 'text-gray-500 dark:text-gray-400'
                                ]"
                            >
                                @{{ tab.label }}
                            </button>

                        </div>

                        <!-- Add -->
                        <button
                            type="button"
                            class="primary-button"
                            @click="$refs.commentModal.open()"
                        >
                            {{ __('taskmanager::app.task_manager.task_comments.index.post') }}
                        </button>

                    </div>

                </div>

                <!-- Modal -->
                <x-admin::modal ref="commentModal">

                    <x-slot:header>
                        <p class="text-lg font-bold">
                            {{ __('taskmanager::app.task_manager.task_comments.index.post') }}
                        </p>
                    </x-slot>

                    <x-slot:content>

                        <x-admin::form
                            v-slot="{ errors, handleSubmit }"
                            as="div"
                        >
                            <form @submit.prevent="handleSubmit($event, submitComment)">

                                <!-- Type -->
                                <x-admin::form.control-group>

                                    <x-admin::form.control-group.label>
                                        {{ __('taskmanager::app.task_manager.task_comments.index.type') }}
                                    </x-admin::form.control-group.label>

                                    <select
                                        v-model="form.type"
                                        name="type"
                                        class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                    >
                                        <option
                                            v-for="type in commentTypes"
                                            :key="type.value"
                                            :value="type.value"
                                        >
                                            @{{ type.label }}
                                        </option>
                                    </select>

                                </x-admin::form.control-group>

                                <!-- Comment -->
                                <x-admin::form.control-group>

                                    <x-admin::form.control-group.label>
                                        {{ __('taskmanager::app.task_manager.task_comments.index.comment') }}
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="textarea"
                                        name="comment"
                                        rules="required"
                                        v-model="form.comment"
                                    >
                                    </x-admin::form.control-group.control>

                                    <x-admin::form.control-group.error
                                        control-name="comment"
                                    >
                                    </x-admin::form.control-group.error>

                                </x-admin::form.control-group>

                                <div class="mt-4 flex justify-end gap-2">

                                    <button
                                        type="button"
                                        class="secondary-button"
                                        @click="$refs.commentModal.close()"
                                    >
                                        {{ __('taskmanager::app.task_manager.task_comments.index.cancel') }}
                                    </button>

                                    <button
                                        type="submit"
                                        class="primary-button"
                                    >
                                        {{ __('taskmanager::app.task_manager.task_comments.index.save') }}
                                    </button>

                                </div>

                            </form>
                        </x-admin::form>

                    </x-slot:content>

                </x-admin::modal>

                <!-- List -->
                <div class="divide-y divide-gray-100 dark:divide-gray-800">

                    <template v-if="loading">

                        <div
                            v-for="i in 3"
                            :key="i"
                            class="p-4"
                        >
                            <div class="animate-pulse">

                                <div class="mb-2 h-3 w-32 rounded bg-gray-200 dark:bg-gray-700"></div>

                                <div class="mb-2 h-4 rounded bg-gray-100 dark:bg-gray-800"></div>

                                <div class="h-4 w-2/3 rounded bg-gray-100 dark:bg-gray-800"></div>

                            </div>
                        </div>

                    </template>

                   <template v-else-if="! filteredComments.length">
                        <div class="flex flex-col items-center justify-center py-12 text-center">

                        
                            <!-- Title -->
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">
                                {{ __('taskmanager::app.task_manager.task_comments.index.no-comments') }}
                            </p>

                            <!-- Subtitle -->
                            <p class="mt-1 max-w-xmd text-xs text-gray-400 dark:text-gray-500">
                                Start the conversation by adding the first comment to this task.
                            </p>

                        </div>
                    </template>

                    <template v-else>

                        <div class="max-h-[400px] overflow-y-auto pr-2">

                            <div
                                v-for="comment in filteredComments"
                                :key="comment.id"
                                class="border-b border-gray-200 p-4 last:border-b-0 dark:border-gray-800"
                            >

                                <div class="mb-2 flex items-center gap-2">

                                    <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                        @{{ comment.user?.name || 'Unknown' }}
                                    </span>

                                    <span
                                        class="rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-semibold text-blue-700 dark:bg-blue-900/40 dark:text-blue-300"
                                    >
                                        @{{ comment.type }}
                                    </span>

                                    <span class="text-xs text-gray-400">
                                        @{{ comment.created_at }}
                                    </span>

                                </div>

                                <p class="whitespace-pre-wrap break-words text-sm leading-6 text-gray-700 dark:text-gray-300">
                                    @{{ comment.comment }}
                                </p>

                            </div>

                        </div>

                    </template>

                </div>

            </div>

        </script>

        <script type="module">

            app.component('v-task-comments', {

                template: '#v-task-comments-template',

                props: {
                    taskId: {
                        type: [String, Number],
                        required: true,
                    },

                    commentsUrl: {
                        type: String,
                        required: true,
                    },

                    storeUrl: {
                        type: String,
                        required: true,
                    },

                    csrfToken: {
                        type: String,
                        required: true,
                    },
                },

                data() {
                    return {
                        comments: [],

                        loading: true,

                        activeTab: 'group',

                        form: {
                            type: 'group',
                            comment: '',
                        },

                        tabs: [
                            {
                                key: 'group',
                                label: '{{ addslashes(__('taskmanager::app.task_manager.task_comments.index.tab-group')) }}',
                            },

                            {
                                key: 'internal',
                                label: '{{ addslashes(__('taskmanager::app.task_manager.task_comments.index.tab-internal')) }}',
                            },
                        ],

                        commentTypes: [
                            {
                                label: '{{ addslashes(__('taskmanager::app.task_manager.task_comments.index.group')) }}',
                                value: 'group',
                            },

                            {
                                label: '{{ addslashes(__('taskmanager::app.task_manager.task_comments.index.internal')) }}',
                                value: 'internal',
                            },
                        ],
                    };
                },

                computed: {

                    filteredComments() {
                        return this.comments.filter((comment) => {
                            return comment.type === this.activeTab;
                        });
                    },

                },

                mounted() {
                    this.fetchComments();
                },

                methods: {

                    async fetchComments() {

                        this.loading = true;

                        try {

                            const response = await fetch(this.commentsUrl, {
                                headers: {
                                    Accept: 'application/json',
                                    'X-CSRF-TOKEN': this.csrfToken,
                                },
                            });

                            const result = await response.json();

                            this.comments = result.data || [];

                        } catch (error) {

                            console.error(error);

                        } finally {

                            this.loading = false;

                        }

                    },

                    async submitComment() {

                        try {

                            const response = await fetch(this.storeUrl, {
                                method: 'POST',

                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': this.csrfToken,
                                },

                                body: JSON.stringify(this.form),
                            });

                            // DEBUG RESPONSE
                            const text = await response.text();

                            let result = {};

                            try {
                                result = JSON.parse(text);
                            } catch (e) {
                                console.error('Invalid JSON Response:', text);
                                return;
                            }

                            if (! response.ok) {
                                console.error(result);
                                return;
                            }

                            this.comments.unshift(result.data);

                            this.form.comment = '';

                            this.form.type = 'group';

                            this.$refs.commentModal.close();

                        } catch (error) {

                            console.error(error);

                        }

                    }

                },

            });

        </script>

    @endPushOnce

</x-admin::layouts>