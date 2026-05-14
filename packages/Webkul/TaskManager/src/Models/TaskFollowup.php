<?php

namespace Webkul\TaskManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\TaskManager\Contracts\TaskFollowup as TaskFollowupContract;
use Webkul\User\Models\User;

class TaskFollowup extends Model implements TaskFollowupContract
{
    protected $table = 'task_followups';

    protected $fillable = ['task_id', 'user_id', 'reason'];

    // ─── Contract Implementation ──────────────────────────────────────────────

    public function getId(): int
    {
        return $this->id;
    }

    public function getTaskId(): int
    {
        return $this->task_id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getReason(): ?string
    {
        return $this->reason;
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
