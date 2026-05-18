<?php

namespace Webkul\Admin\Http\Controllers\TaskManager\TaskGroup;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\TaskManager\TaskGroupDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\AttributeForm;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\TaskManager\Repositories\TaskGroupRepository;

class TaskGroupController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected TaskGroupRepository $taskGroupRepository)
    {
        request()->request->add(['entity_type' => 'task_groups']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View|JsonResponse
    {

        if (request()->ajax()) {
            return datagrid(TaskGroupDataGrid::class)->process();
        }

        return view('admin::task_manager.task_groups.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin::task_manager.task_groups.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AttributeForm $request): RedirectResponse|JsonResponse
    {
        Event::dispatch('taskmanager.task_group.create.before');

        $taskGroup = $this->taskGroupRepository->create($request->all());

        Event::dispatch('task_manager.task_group.create.after', $taskGroup);

        if (request()->ajax()) {
            return response()->json([
                'data' => $taskGroup,
                'message' => trans('admin::app.task_manager.task_groups.index.create-success'),
            ]);
        }

        session()->flash('success', trans('admin::app.task_manager.task_groups.index.create-success'));

        return redirect()->route('admin.task_manager.task_groups.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $taskGroup = $this->taskGroupRepository->findOrFail($id);

        return view('admin::task_manager.task_groups.edit', compact('taskGroup'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AttributeForm $request, int $id): RedirectResponse
    {
        Event::dispatch('task_manager.task_group.update.before', $id);

        $taskGroup = $this->taskGroupRepository->update($request->all(), $id);

        Event::dispatch('task_manager.task_group.update.after', $taskGroup);

        session()->flash('success', trans('admin::app.task_manager.task_groups.index.update-success'));

        return redirect()->route('admin.task_manager.task_groups.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            Event::dispatch('task_manager.task_group.delete.before', $id);

            $this->taskGroupRepository->delete($id);

            Event::dispatch('task_manager.task_group.delete.after', $id);

            return response()->json([
                'message' => trans('admin::app.task_manager.task_groups.index.delete-success'),
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.task_manager.task_groups.index.delete-failed'),
            ], 400);
        }
    }

    /**
     * Mass Delete the specified resources.
     */
    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResponse
    {
        try {

            $taskGroups = $this->taskGroupRepository->findWhereIn('id', $massDestroyRequest->input('indices'));

            Event::dispatch('task_manager.task_group.delete.before', $taskGroups);

            foreach ($taskGroups as $taskGroup) {

                $this->taskGroupRepository->delete($taskGroup->id);
            }

            Event::dispatch('task_manager.task_group.delete.after', $taskGroups);

            return response()->json([
                'message' => trans('admin::app.task_manager.task_groups.index.delete-success'),
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.task_manager.task_groups.index.delete-failed'),
            ], 400);
        }
    }
}
