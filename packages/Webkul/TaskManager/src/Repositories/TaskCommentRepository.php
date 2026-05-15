<?php

namespace Webkul\TaskManager\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Attribute\Repositories\AttributeValueRepository;
use Webkul\Core\Eloquent\Repository;
use Webkul\TaskManager\Contracts\Task;
use Webkul\TaskManager\Contracts\TaskComment;

class TaskCommentRepository extends Repository
{
    /**
     * Create a new repository instance.
     */
    public function __construct(
        protected AttributeRepository $attributeRepository,
        protected AttributeValueRepository $attributeValueRepository,
        Container $container
    ) {
        parent::__construct($container);
    }

    /**
     * Specify model class.
     */
    public function model()
    {
        return TaskComment::class;
    }

    /**
     * Get comments for task.
     */
    public function getForTask(Task $task, int $userId)
    {
        $isInternal = $task->getCreatedBy() === $userId
            || $task->getAssignedTo() === $userId;

        $query = $this->model()::with('user:id,name')
            ->where('task_id', $task->getId());

        if (! $isInternal) {
            $query->where('type', 'group');
        }

        return $query->latest()->get();
    }

    /**
     * Create comment.
     */
    public function create(array $data)
    {
        $user = Auth::user();

        $task = app(Task::class)->findOrFail($data['task_id']);

        if (
            $data['type'] === 'internal'
            && $task->getCreatedBy() !== $user->id
            && $task->getAssignedTo() !== $user->id
        ) {
            abort(403, trans('admin::app.task_manager.task_comments.index.internal_notes_restricted'));
        }

        $canComment = $task->getCreatedBy() === $user->id
            || $task->getAssignedTo() === $user->id
            || $task->followers()->where('users.id', $user->id)->exists();

        if (! $canComment) {
            abort(403, trans('admin::app.task_manager.task_comments.index.comment_access_denied'));
        }

        $comment = parent::create([
            'task_id' => $task->getId(),
            'user_id' => $user->id,
            'type' => $data['type'],
            'comment' => $data['comment'],
        ]);

        Event::dispatch('task_manager.comment.created', [$task, $comment]);

        return $comment->load('user');
    }

    /**
     * Update comment.
     */
    public function update(array $data, $id, $attributes = [])
    {
        $comment = parent::update(array_merge($data, [
            'is_edited' => true,
        ]), $id);

        return $comment->refresh();
    }

    /**
     * Delete comment.
     */
    public function delete($id)
    {
        $comment = $this->findOrFail($id);

        return $comment->delete();
    }
}
