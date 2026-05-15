<?php

namespace Webkul\TaskManager\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Webkul\TaskManager\Contracts\Task as TaskContract;
use Webkul\User\Models\User;

class Task extends Model implements TaskContract
{
    protected $table = 'tasks';

    protected $fillable = [
        'title', 'description', 'created_by', 'assigned_to',
        'group_id', 'status', 'priority', 'deadline', 'deadline_reminder_sent',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'deadline_reminder_sent' => 'boolean',
    ];

    // ─── Contract Implementation ──────────────────────────────────────────────

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCreatedBy(): ?int
    {
        return $this->created_by;
    }

    public function getAssignedTo(): ?int
    {
        return $this->assigned_to;
    }

    public function getGroupId(): ?int
    {
        return $this->group_id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function getDeadline(): ?Carbon
    {
        return $this->deadline;
    }

    public function isDeadlineReminderSent(): bool
    {
        return (bool) $this->deadline_reminder_sent;
    }

    public function getStatusLabel(): string
    {
        return config('task_manager.statuses.'.$this->status, ucfirst($this->status));
    }

    public function getPriorityLabel(): string
    {
        return config('task_manager.priorities.'.$this->priority, ucfirst($this->priority));
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isOnHold(): bool
    {
        return $this->status === 'on_hold';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isOverdue(): bool
    {
        return $this->deadline
            && $this->deadline->isPast()
            && ! $this->isCompleted()
            && ! $this->isCancelled();
    }

    // ─── Blade Accessors ─────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return $this->getStatusLabel();
    }

    public function getPriorityLabelAttribute(): string
    {
        return $this->getPriorityLabel();
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'in_progress' => 'blue',
            'on_hold' => 'orange',
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'green',
            'medium' => 'blue',
            'high' => 'orange',
            'critical' => 'red',
            default => 'gray',
        };
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    /**
     * Get the creator.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the assignee.
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the group.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(TaskGroup::class, 'group_id');
    }

    /**
     * Get the comments.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class, 'task_id')->latest();
    }

    /**
     * Get the group comments.
     */
    public function groupComments(): HasMany
    {
        return $this->hasMany(TaskComment::class, 'task_id')->where('type', 'group')->latest();
    }

    /**
     * Get the internal comments.
     */
    public function internalComments(): HasMany
    {
        return $this->hasMany(TaskComment::class, 'task_id')->where('type', 'internal')->latest();
    }

    /**
     * Get the followups.
     */
    public function followups(): HasMany
    {
        return $this->hasMany(TaskFollowup::class, 'task_id');
    }

    /**
     * Get the followers.
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_followups', 'task_id', 'user_id')
            ->withPivot('reason')
            ->withTimestamps();
    }

    /**
     * Get the notifications.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(TaskNotification::class, 'task_id');
    }

    // ─── Query Scopes ─────────────────────────────────────────────────────────

    /**
     * Visibility rules (OR):
     *  1. creator  2. assignee  3. group member  4. explicit follower
     */
    public function scopeVisibleTo(Builder $query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('created_by', $userId)
                ->orWhere('assigned_to', $userId)
                ->orWhereHas('group.users', fn ($u) => $u->where('users.id', $userId))
                ->orWhereHas('followers', fn ($f) => $f->where('users.id', $userId));
        });
    }

    public function scopeByStatus(Builder $query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority(Builder $query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeOverdue(Builder $query)
    {
        return $query->whereNotNull('deadline')
            ->where('deadline', '<', now())
            ->whereNotIn('status', ['completed', 'cancelled']);
    }

    public function scopeUpcomingDeadline(Builder $query, int $hours = 24)
    {
        return $query->whereNotNull('deadline')
            ->whereBetween('deadline', [now(), now()->addHours($hours)])
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->where('deadline_reminder_sent', false);
    }
}
