<?php

namespace Webkul\TaskManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\TaskManager\Contracts\TaskComment as TaskCommentContract;
use Webkul\User\Models\User;

class TaskComment extends Model implements TaskCommentContract
{
    protected $table = 'task_comments';

    protected $fillable = ['task_id', 'user_id', 'type', 'comment', 'is_edited'];

    protected $casts = ['is_edited' => 'boolean'];

    // ─── Contract Implementation ──────────────────────────────────────────────

    public function getId(): int
    {
        return $this->id;
    }

    public function getTaskId(): int
    {
        return $this->task_id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function isEdited(): bool
    {
        return $this->is_edited;
    }

    public function isInternal(): bool
    {
        return $this->type === 'internal';
    }

    public function isGroupComment(): bool
    {
        return $this->type === 'group';
    }

    /**
     * Visibility rules:
     *  - internal → only task creator + assignee
     *  - group    → all followers (creator, assignee, explicit followers)
     */
    public function isVisibleTo(int $userId): bool
    {
        if ($this->type === 'internal') {
            return $this->task->created_by === $userId
                || $this->task->assigned_to === $userId;
        }

        // group comment
        return $this->task->created_by === $userId
            || $this->task->assigned_to === $userId
            || $this->task->followers()->where('users.id', $userId)->exists();
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
