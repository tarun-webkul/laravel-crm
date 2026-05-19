<?php

namespace Webkul\TaskManager\Http\Controllers\Tasks;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\TaskManager\Repositories\TaskCommentRepository;
use Webkul\TaskManager\Repositories\TaskRepository;

class TaskCommentController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected TaskCommentRepository $taskCommentRepository,
        protected TaskRepository $taskRepository,
    ) {
        request()->request->add(['entity_type' => 'task_comments']);
    }

    /**
     * List comments for a task.
     * Filters based on the authenticated user's visibility rights.
     */
    public function index(int $taskId): JsonResponse
    {
        $task = $this->taskRepository->findOrFail($taskId);

        $comments = $this->taskCommentRepository->getForTask($task, Auth::id());

        return response()->json([
            'data' => $comments->map(fn ($c) => $this->formatComment($c)),
        ]);
    }

    /**
     * Store a new comment.
     */
    public function store(Request $request, int $taskId): JsonResponse
    {
        $this->taskRepository->findOrFail($taskId);

        $request->validate([
            'type' => ['required', 'in:internal,group'],
            'comment' => ['required', 'string', 'max:5000'],
        ]);

        Event::dispatch('task_manager.task_comments.create.before');

        $comment = $this->taskCommentRepository->create([
            'task_id' => $taskId,
            'type' => $request->input('type'),
            'comment' => $request->input('comment'),
        ]);

        Event::dispatch('task_manager.task_comments.create.after', $comment);

        return response()->json([
            'data' => $this->formatComment($comment),
            'message' => trans('taskmanager::app.task_manager.task_comments.index.create-success'),
        ], 201);
    }

    /**
     * Update an existing comment.
     * Only the comment author may edit their own comment.
     */
    public function update(Request $request, int $taskId, int $id): JsonResponse
    {
        $comment = $this->taskCommentRepository->findOrFail($id);

        if ($comment->task_id !== $taskId) {
            abort(404);
        }

        if ($comment->user_id !== Auth::id()) {
            abort(403, trans('taskmanager::app.task_manager.task_comments.index.edit_access_denied'));
        }

        $request->validate([
            'comment' => ['required', 'string', 'max:5000'],
        ]);

        Event::dispatch('task_manager.task_comments.update.before', $comment);

        $comment = $this->taskCommentRepository->update([
            'comment' => $request->input('comment'),
        ], $id);

        Event::dispatch('task_manager.task_comments.update.after', $comment);

        return response()->json([
            'data' => $this->formatComment($comment),
            'message' => trans('taskmanager::app.task_manager.task_comments.index.update-success'),
        ]);
    }

    /**
     * Delete a comment.
     * Only the comment author may delete their own comment.
     */
    public function destroy(int $taskId, int $id): JsonResponse
    {
        try {
            $comment = $this->taskCommentRepository->findOrFail($id);

            if ($comment->task_id !== $taskId) {
                abort(404);
            }

            if ($comment->user_id !== Auth::id()) {
                abort(403, trans('taskmanager::app.task_manager.task_comments.index.delete_access_denied'));
            }

            Event::dispatch('task_manager.task_comments.delete.before', $comment);

            $this->taskCommentRepository->delete($id);

            Event::dispatch('task_manager.task_comments.delete.after', $comment);

            return response()->json([
                'message' => trans('taskmanager::app.task_manager.task_comments.index.delete-success'),
            ], 200);

        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('taskmanager::app.task_manager.task_comments.index.delete-failed'),
            ], 400);
        }
    }

    /**
     * Format a comment model into a safe API response array.
     */
    protected function formatComment($comment): array
    {
        return [
            'id' => $comment->getId(),
            'task_id' => $comment->getTaskId(),
            'type' => $comment->getType(),
            'comment' => $comment->getComment(),
            'is_edited' => $comment->isEdited(),
            'is_mine' => $comment->getUserId() === Auth::id(),
            'created_at' => $comment->created_at?->diffForHumans(),
            'updated_at' => $comment->updated_at?->diffForHumans(),

            'user' => $comment->user ? [
                'id' => $comment->user->id,
                'name' => $comment->user->name,
                'email' => $comment->user->email,
                'avatar' => $this->getAvatar($comment->user),
            ] : null,
        ];
    }

    /**
     * Generate an avatar URL or an "initials:" string for the frontend.
     */
    protected function getAvatar($user): string
    {
        if (! empty($user->image)) {
            return asset('storage/'.$user->image);
        }

        $initials = collect(explode(' ', $user->name))
            ->take(2)
            ->map(fn ($part) => strtoupper($part[0] ?? ''))
            ->implode('');

        return 'initials:'.$initials;
    }
}
