<?php

namespace Webkul\TaskManager\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Attribute\Repositories\AttributeValueRepository;
use Webkul\Core\Eloquent\Repository;
use Webkul\TaskManager\Contracts\TaskGroup;

class TaskGroupRepository extends Repository
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
        return TaskGroup::class;
    }

    /**
     * Get all groups.
     */
    public function getAll()
    {
        return $this->model()::with([
            'creator:id,name',
            'users:id,name',
        ])->latest()->paginate(20);
    }

    /**
     * Create group.
     */
    public function create(array $data)
    {
        $data['created_by'] = Auth::id();

        $group = parent::create($data);

        if (! empty($data['user_ids'])) {
            $group->users()->sync($data['user_ids']);
        }

        /**
         * Save attribute values.
         */
        $this->attributeValueRepository->save(array_merge($data, [
            'entity_id' => $group->id,
        ]));

        return $group;
    }

    /**
     * Update group.
     */
    public function update(array $data, $id, $attributes = [])
    {
        $group = parent::update($data, $id);

        if (isset($data['user_ids'])) {
            $group->users()->sync($data['user_ids']);
        }

        /**
         * If attributes are provided then only save provided attributes.
         */
        if (! empty($attributes)) {
            $conditions = [
                'entity_type' => $data['entity_type'] ?? 'task_groups',
            ];

            if (isset($data['quick_add'])) {
                $conditions['quick_add'] = 1;
            }

            $attributes = $this->attributeRepository
                ->where($conditions)
                ->whereIn('code', $attributes)
                ->get();

            $this->attributeValueRepository->save(array_merge($data, [
                'entity_id' => $group->id,
            ]), $attributes);

            return $group;
        }

        /**
         * Save all attribute values.
         */
        $this->attributeValueRepository->save(array_merge($data, [
            'entity_id' => $group->id,
        ]));

        return $group->refresh();
    }

    /**
     * Delete group.
     */
    public function delete($id)
    {
        $group = $this->findOrFail($id);

        DB::transaction(function () use ($group, $id) {

            $this->attributeValueRepository->deleteWhere([
                'entity_id' => $id,
                'entity_type' => 'task_groups',
            ]);

            $group->users()->detach();

            $group->delete();
        });
    }

    /**
     * Get groups for user.
     */
    public function getGroupsForUser(int $userId)
    {
        return $this->model()::whereHas('users', function ($query) use ($userId) {
            $query->where('users.id', $userId);
        })->get();
    }
}
