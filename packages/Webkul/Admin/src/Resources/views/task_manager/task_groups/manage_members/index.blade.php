<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.task_manager.task_groups.manage-members.title', [
            'name' => $taskGroup->name
        ])
    </x-slot>

    <div class="flex flex-col gap-4" id="app">

        <v-manage-members ref="manageMembers"></v-manage-members>

        <!-- Sticky Header -->
        <div class="scroll-reactive-sticky sticky top-[60px] z-[1000] flex items-center justify-between rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">

            <div class="flex flex-col gap-2">

                {!! view_render_event('admin.task_manager.task_groups.manage-members.breadcrumbs.before') !!}

                <x-admin::breadcrumbs
                    name="task_manager.task_groups.manage-members"
                    :entity="$taskGroup"
                />

                {!! view_render_event('admin.task_manager.task_groups.manage-members.breadcrumbs.after') !!}

                <div class="text-xl font-bold dark:text-gray-300">
                    @lang('admin::app.task_manager.task_groups.manage-members.title', [
                        'name' => $taskGroup->name
                    ])
                </div>

            </div>

            <div class="flex items-center gap-x-2.5">

                {!! view_render_event('admin.task_manager.task_groups.manage-members.index.create_button.before') !!}

                @if (bouncer()->hasPermission('task_manager.task_groups.manage-members.assign'))
                    <button
                        type="button"
                        class="primary-button"
                        @click="$refs.manageMembers?.openAssignModal()"
                    >
                        @lang('admin::app.task_manager.task_groups.manage-members.assign-btn')
                    </button>
                @endif

                {!! view_render_event('admin.task_manager.task_groups.manage-members.index.assign_button.after') !!}

            </div>
        </div>

        <!-- DataGrid -->
        <x-admin::datagrid
            ref="datagrid"
            :src="route('admin.task_manager.task_groups.manage-members.index', $taskGroup->id)"
        >
            <x-admin::shimmer.datagrid />
        </x-admin::datagrid>

    </div>

    @pushOnce('scripts')

        <!--  Vue Component -->
        <script type="module">
            app.component('v-manage-members', {
                template: '#manage-members-template',

                data() {
                    return {
                        selectedUserId: '',
                        users: @json($users),
                    };
                },

                methods: {

                    //  open modal
                    openAssignModal() {
                        this.selectedUserId = '';
                        this.$refs.assignUserModal?.toggle();
                    },

                    //  assign user
                    assignUser() {

                        if (!this.selectedUserId) {
                            this.$emitter.emit('add-flash', {
                                type: 'error',
                                message: "{{ trans('admin::app.task_manager.task_groups.manage-members.select-user') }}"
                            });
                            return;
                        }

                        this.$axios.post(
                            "{{ route('admin.task_manager.task_groups.manage-members.assign', $taskGroup->id) }}",
                            {
                                user_id: this.selectedUserId
                            }
                        )
                        .then((response) => {

                            this.$emitter.emit('add-flash', {
                                type: 'success',
                                message: response.data.message
                            });

                            this.$refs.assignUserModal?.toggle();

                            this.$parent.$refs.datagrid?.get();

                        })
                        .catch((error) => {

                            this.$emitter.emit('add-flash', {
                                type: 'error',
                                message: error.response?.data?.message ?? 'Assignment failed'
                            });

                        });
                    }
                }
            });
        </script>

        <!--  Template -->
        <script type="text/x-template" id="manage-members-template">
            <x-admin::modal ref="assignUserModal">

                <x-slot:header>
                    Assign Member
                </x-slot>

                <x-slot:content>
                    <div class="p-4">
                        <label class="mb-2 block font-medium">
                            Select Member
                        </label>

                        <select
                            v-model="selectedUserId"
                            class="w-full rounded border p-2 relative focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300"
                            required
                        >
                            <option value="">-- Select Member --</option>

                            <option
                                v-for="user in users"
                                :key="user.id"
                                :value="user.id"
                            >
                                @{{ user.name }}
                            </option>
                        </select>
                    </div>
                </x-slot>

                <x-slot:footer>
                    <button
                        class="primary-button"
                        @click="assignUser"
                    >
                        Assign
                    </button>
                </x-slot>

            </x-admin::modal>
        </script>

    @endPushOnce

</x-admin::layouts>