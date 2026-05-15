<?php

namespace Webkul\TaskManager\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Attribute\Repositories\AttributeValueRepository;
use Webkul\Core\Eloquent\Repository;
use Webkul\TaskManager\Contracts\Task;
use Webkul\TaskManager\Models\TaskFollowupProxy;

class TaskRepository extends Repository
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
        return Task::class;
    }

    /**
     * Followup model.
     */
    protected function followupModel(): string
    {
        return TaskFollowupProxy::modelClass();
    }

    // ─── Read ─────────────────────────────────────────────────────────────

    /**
     * Get all tasks.
     */
    public function getAll(array $filters = [])
    {
        $userId = Auth::id();

        $query = $this->model()::with([
            'creator:id,name',
            'assignee:id,name',
            'group:id,name',
        ])->visibleTo($userId);

        if (! empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (! empty($filters['priority'])) {
            $query->byPriority($filters['priority']);
        }

        if (! empty($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        if (! empty($filters['group_id'])) {
            $query->where('group_id', $filters['group_id']);
        }

        if (! empty($filters['search'])) {
            $query->where('title', 'like', '%'.$filters['search'].'%');
        }

        return $query->latest()->paginate(20);
    }

    /**
     * Get upcoming deadlines.
     */
    public function getUpcomingDeadlines(int $hours = 24)
    {
        return $this->model()::with(['followers'])
            ->upcomingDeadline($hours)
            ->get();
    }

    // ─── Write ────────────────────────────────────────────────────────────

    /**
     * Create task.
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {

            $user = Auth::user();

            $data['created_by'] = $user->id;

            $task = parent::create($data);

            //  /**
            //  * Save attribute values.
            //  */
            // $this->attributeValueRepository->save(array_merge($data, [
            //     'entity_id' => $task->id,
            // ]));

            /**
             * Auto-follow creator.
             */
            $this->addFollower($task, $user->id, 'creator');

            /**
             * Auto-follow assignee.
             */
            if (
                ! empty($data['assigned_to'])
                && $data['assigned_to'] !== $user->id
            ) {
                $this->addFollower($task, $data['assigned_to'], 'assigned');
            }

            Event::dispatch('task_manager.task.created', $task);

            return $task;
        });
    }

    /**
     * Update task.
     */
    public function update(array $data, $id, $attributes = [])
    {
        return DB::transaction(function () use ($data, $id) {

            $task = $this->findOrFail($id);

            $previousAssignee = $task->getAssignedTo();

            $task = parent::update($data, $id);

            // /**
            //  * If attributes are provided then only save provided attributes.
            //  */
            // if (! empty($attributes)) {

            //     $conditions = [
            //         'entity_type' => $data['entity_type'] ?? 'tasks',
            //     ];

            //     if (isset($data['quick_add'])) {
            //         $conditions['quick_add'] = 1;
            //     }

            //     $attributes = $this->attributeRepository
            //         ->where($conditions)
            //         ->whereIn('code', $attributes)
            //         ->get();

            //     $this->attributeValueRepository->save(array_merge($data, [
            //         'entity_id' => $task->id,
            //     ]), $attributes);

            // } else {

            //     /**
            //      * Save all attributes.
            //      */
            //     $this->attributeValueRepository->save(array_merge($data, [
            //         'entity_id' => $task->id,
            //     ]));
            // }

            /**
             * Assignee changed.
             */
            if (
                isset($data['assigned_to'])
                && $data['assigned_to'] !== $previousAssignee
            ) {
                $this->addFollower($task, $data['assigned_to'], 'assigned');

                Event::dispatch('task_manager.task.assigned', $task);
            }

            Event::dispatch('task_manager.task.updated', $task);

            return $task->refresh();
        });
    }

    /**
     * Delete task.
     */
    public function delete($id)
    {
        $task = $this->findOrFail($id);

        DB::transaction(function () use ($task, $id) {

            $this->attributeValueRepository->deleteWhere([
                'entity_id' => $id,
                'entity_type' => 'tasks',
            ]);

            Event::dispatch('task_manager.task.deleted', $task);

            $task->delete();
        });
    }

    // ─── Followups ────────────────────────────────────────────────────────

    /**
     * Add follower.
     */
    public function addFollower(
        Task $task,
        int $userId,
        string $reason = 'manual'
    ): void {
        /**
         * Manual additions require group membership.
         */
        if ($reason === 'manual' && $task->getGroupId()) {

            $inGroup = $task->group
                ->users()
                ->where('users.id', $userId)
                ->exists();

            if (! $inGroup) {
                return;
            }
        }

        $this->followupModel()::firstOrCreate(
            [
                'task_id' => $task->getId(),
                'user_id' => $userId,
            ],
            [
                'reason' => $reason,
            ]
        );

        Event::dispatch('task_manager.followup.added', [
            $task,
            $userId,
        ]);
    }

    /**
     * Remove follower.
     */
    public function removeFollower(Task $task, int $userId): void
    {
        $this->followupModel()::where('task_id', $task->getId())
            ->where('user_id', $userId)
            ->delete();

        Event::dispatch('task_manager.followup.removed', [
            $task,
            $userId,
        ]);
    }

    // ─── Permission Helpers ───────────────────────────────────────────────

    /**
     * Check if user can view task.
     */
    public function canView(Task $task, int $userId): bool
    {
        if (
            $task->getCreatedBy() === $userId
            || $task->getAssignedTo() === $userId
        ) {
            return true;
        }

        if (
            $task->getGroupId()
            && $task->group->hasUser($userId)
        ) {
            return true;
        }

        return $task->followers()
            ->where('users.id', $userId)
            ->exists();
    }

    /**
     * Check if user can edit task.
     */
    public function canEdit(Task $task, int $userId): bool
    {
        return $task->getCreatedBy() === $userId
            || Auth::user()->hasPermissionTo('task_manager.tasks.edit');
    }
}
