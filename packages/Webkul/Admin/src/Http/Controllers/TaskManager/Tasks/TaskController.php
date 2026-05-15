<?php

namespace Webkul\Admin\Http\Controllers\TaskManager\Tasks;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\TaskManager\TaskDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\AttributeForm;
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

        return view('admin::task_manager.tasks.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin::task_manager.tasks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AttributeForm $request): RedirectResponse|JsonResponse
    {
        Event::dispatch('contacts.tasks.create.before');

        $task = $this->taskRepository->create(request()->all());

        Event::dispatch('contacts.tasks.create.after', $task);

        if (request()->ajax()) {
            return response()->json([
                'data' => $task,
                'message' => trans('admin::app.task_manager.tasks.index.create-success'),
            ]);
        }

        session()->flash('success', trans('admin::app.task_manager.tasks.index.create-success'));

        return redirect()->route('admin.task_manager.tasks.index');
    }
}
