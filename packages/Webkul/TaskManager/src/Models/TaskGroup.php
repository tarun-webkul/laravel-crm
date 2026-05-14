<?php

namespace Webkul\TaskManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\TaskManager\Contracts\TaskGroup as TaskGroupContract;
use Webkul\User\Models\User;

class TaskGroup extends Model implements TaskGroupContract
{
    protected $table = 'task_groups';

    protected $fillable = ['name', 'description', 'created_by'];

    // ─── Contract Implementation ──────────────────────────────────────────────

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCreatedBy(): ?int
    {
        return $this->created_by;
    }

    public function hasUser(int $userId): bool
    {
        return $this->users()->where('users.id', $userId)->exists();
    }

    public function addUser(int $userId): void
    {
        if (! $this->hasUser($userId)) {
            $this->users()->attach($userId);
        }
    }

    public function removeUser(int $userId): void
    {
        $this->users()->detach($userId);
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_group_users', 'group_id', 'user_id')
            ->withTimestamps();
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'group_id');
    }
}
