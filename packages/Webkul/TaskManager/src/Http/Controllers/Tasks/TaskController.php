<?php

namespace Webkul\TaskManager\Http\Controllers\Tasks;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\AttributeForm;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\TaskManager\DataGrids\TaskDataGrid;
use Webkul\TaskManager\Repositories\TaskRepository;

class TaskController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected TaskRepository $taskRepository)
    {
        request()->request->add(['entity_type' => 'tasks']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View|JsonResponse
    {

        if (request()->ajax()) {
            return datagrid(TaskDataGrid::class)->process();
        }

        return view('taskmanager::tasks.index');
    }

    /**
     * Display the specified resource.
     */
    public function view(int $id): View
    {
        $task = $this->taskRepository->findOrFail($id);

        return view('taskmanager::tasks.view', compact('task'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('taskmanager::tasks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AttributeForm $request): RedirectResponse|JsonResponse
    {
        Event::dispatch('task_manager.tasks.create.before');

        $task = $this->taskRepository->create($request->all());

        Event::dispatch('task_manager.tasks.create.after', $task);

        if (request()->ajax()) {
            return response()->json([
                'data' => $task,
                'message' => trans('taskmanager::app.task_manager.tasks.index.create-success'),
            ]);
        }

        session()->flash('success', trans('taskmanager::app.task_manager.tasks.index.create-success'));

        return redirect()->route('admin.task_manager.tasks.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $task = $this->taskRepository->findOrFail($id);

        return view('taskmanager::tasks.edit', compact('task'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AttributeForm $request, int $id): RedirectResponse
    {
        Event::dispatch('task_manager.tasks.update.before', $id);

        $task = $this->taskRepository->update($request->all(), $id);

        Event::dispatch('task_manager.tasks.update.after', $task);

        session()->flash('success', trans('taskmanager::app.task_manager.tasks.index.update-success'));

        return redirect()->route('admin.task_manager.tasks.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            Event::dispatch('task_manager.tasks.delete.before', $id);

            $this->taskRepository->delete($id);

            Event::dispatch('task_manager.tasks.delete.after', $id);

            return response()->json([
                'message' => trans('taskmanager::app.task_manager.tasks.index.delete-success'),
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('taskmanager::app.task_manager.tasks.index.delete-failed'),
            ], 400);
        }
    }

    /**
     * Mass Delete the specified resources.
     */
    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResponse
    {
        $tasks = $this->taskRepository->findWhereIn('id', $massDestroyRequest->input('indices'));

        foreach ($tasks as $task) {
            Event::dispatch('task_manager.tasks.delete.before', $task);

            $this->taskRepository->delete($task->id);

            Event::dispatch('task_manager.tasks.delete.after', $task);
        }

        return response()->json([
            'message' => trans('taskmanager::app.task_manager.tasks.index.delete-success'),
        ]);
    }
}
