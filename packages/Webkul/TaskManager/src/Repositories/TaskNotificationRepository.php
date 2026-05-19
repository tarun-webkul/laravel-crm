<?php

namespace Webkul\TaskManager\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Attribute\Repositories\AttributeValueRepository;
use Webkul\Core\Eloquent\Repository;
use Webkul\TaskManager\Contracts\Task as TaskContract;
use Webkul\TaskManager\Contracts\TaskNotification;

class TaskNotificationRepository extends Repository
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
        return TaskNotification::class;
    }

    /**
     * Bulk insert notifications for users.
     */
    public function notifyUsers(
        Collection $users,
        TaskContract $task,
        string $type,
        string $message,
        array $data = []
    ): void {
        $now = now();

        $records = $users->map(function ($user) use (
            $task,
            $type,
            $message,
            $data,
            $now
        ) {
            return [
                'user_id' => $user->id,
                'task_id' => $task->getId(),
                'type' => $type,
                'message' => $message,
                'data' => json_encode($data),
                'is_read' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        })->toArray();

        $this->model()::insert($records);
    }

    /**
     * Create single notification.
     */
    public function create(array $data)
    {
        $notification = parent::create([
            'user_id' => $data['user_id'],
            'task_id' => $data['task_id'],
            'type' => $data['type'],
            'message' => $data['message'],
            'data' => $data['data'] ?? [],
            'is_read' => false,
        ]);

        /**
         * Save attribute values.
         */
        $this->attributeValueRepository->save(array_merge($data, [
            'entity_id' => $notification->id,
        ]));

        return $notification;
    }

    /**
     * Update notification.
     */
    public function update(array $data, $id, $attributes = [])
    {
        $notification = parent::update($data, $id);

        /**
         * If attributes are provided then save only provided attributes.
         */
        if (! empty($attributes)) {
            $conditions = [
                'entity_type' => $data['entity_type'] ?? 'task_notifications',
            ];

            if (isset($data['quick_add'])) {
                $conditions['quick_add'] = 1;
            }

            $attributes = $this->attributeRepository
                ->where($conditions)
                ->whereIn('code', $attributes)
                ->get();

            $this->attributeValueRepository->save(array_merge($data, [
                'entity_id' => $notification->id,
            ]), $attributes);

            return $notification;
        }

        /**
         * Save all attribute values.
         */
        $this->attributeValueRepository->save(array_merge($data, [
            'entity_id' => $notification->id,
        ]));

        return $notification->refresh();
    }

    /**
     * Delete notification.
     */
    public function delete($id)
    {
        $notification = $this->findOrFail($id);

        DB::transaction(function () use ($notification, $id) {

            $this->attributeValueRepository->deleteWhere([
                'entity_id' => $id,
                'entity_type' => 'task_notifications',
            ]);

            $notification->delete();
        });
    }

    /**
     * Get notifications for user.
     */
    public function getForUser(int $userId)
    {
        return $this->model()::with('task:id,title')
            ->forUser($userId)
            ->latest()
            ->paginate(20);
    }

    /**
     * Get unread notification count.
     */
    public function unreadCount(int $userId): int
    {
        return $this->model()::forUser($userId)
            ->unread()
            ->count();
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead(int $userId): void
    {
        $this->model()::forUser($userId)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Mark notification as read.
     */
    public function markRead($id): void
    {
        $notification = $this->findOrFail($id);

        $notification->markAsRead();
    }
}
