<?php

namespace Webkul\Admin\Http\Controllers\TaskManager\TaskGroup;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Webkul\Admin\DataGrids\TaskManager\TaskGroupMembersDatagrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\AttributeForm;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\TaskManager\Repositories\TaskGroupRepository;
use Webkul\User\Repositories\UserRepository;

class TaskGroupUserController extends Controller
{
    public function __construct(
        protected TaskGroupRepository $taskGroupRepository,
        protected UserRepository $userRepository
    ) {}

    /**
     * Manage members page + datagrid endpoint
     */
    public function index(int $taskGroupId)
    {
        $taskGroup = $this->taskGroupRepository->findOrFail($taskGroupId);

        if (request()->ajax()) {
            return app(TaskGroupMembersDatagrid::class)->process();
        }

        $users = $this->userRepository->select('id', 'name')
            ->whereNotIn('id', [...$taskGroup->users()->pluck('user_id')->toArray(), $taskGroup->created_by])
            ->orderBy('name')
            ->get();

        return view(
            'admin::task_manager.task_groups.manage_members.index',
            compact('taskGroup', 'users')
        );
    }

    /**
     * Assign user to task group.
     */
    public function assign(AttributeForm $request, int $taskGroupId): JsonResponse
    {
        try {

            Event::dispatch('task_manager.task_group.manage_members.assign.before', $taskGroupId);

            $taskGroup = $this->taskGroupRepository->findOrFail($taskGroupId);

            $request->validate([
                'user_id' => ['required', 'exists:users,id'],
            ]);

            $taskGroup->users()->attach([
                $request->user_id,
            ]);

            Event::dispatch('task_manager.task_group.manage_members.assign.after', $taskGroupId);

            return response()->json([
                'message' => trans('admin::app.task_manager.task_groups.manage-members.assign-success'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => trans('admin::app.task_manager.task_groups.manage-members.assign-failed'),
            ], 500);
        }
    }

    /**
     * Unassign user from task group.
     */
    public function unassign(int $taskGroupId, int $userId): JsonResponse
    {
        try {
            $taskGroup = $this->taskGroupRepository->findOrFail($taskGroupId);

            Event::dispatch('task_manager.task_group.manage_members.delete.before', $taskGroup);

            $taskGroup->users()->detach($userId);

            Event::dispatch('task_manager.task_group.manage_members.delete.after', $taskGroup);

            return response()->json([
                'message' => trans('admin::app.task_manager.task_groups.manage-members.unassign-success'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => trans('admin::app.task_manager.task_groups.manage-members.unassign-failed'),
            ], 500);
        }
    }

    /**
     * Mass Delete the specified resources.
     */
    public function massDestroy(MassDestroyRequest $massDestroyRequest, int $taskGroupId): JsonResponse
    {
        try {
            $taskGroup = $this->taskGroupRepository->findOrFail($taskGroupId);

            Event::dispatch('task_manager.task_group.manage_members.delete.before', $taskGroup);

            $taskGroup->users()->detach($massDestroyRequest->input('indices'));

            Event::dispatch('task_manager.task_group.manage_members.delete.after', $taskGroup);

            return response()->json([
                'message' => trans('admin::app.task_manager.task_groups.manage-members.unassign-success'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => trans('admin::app.task_manager.task_groups.manage-members.unassign-failed'),
            ], 500);
        }
    }
}
