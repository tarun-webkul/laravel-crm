<?php

namespace Webkul\TaskManager\Contracts;

use Illuminate\Support\Carbon;

interface Task
{
    // ─── Core Fields ──────────────────────────────────────────────────────────

    public function getId(): int;

    public function getTitle(): string;

    public function getDescription(): ?string;

    // ─── Ownership ────────────────────────────────────────────────────────────

    public function getCreatedBy(): ?int;

    public function getAssignedTo(): ?int;

    public function getGroupId(): ?int;

    // ─── Status & Priority ────────────────────────────────────────────────────

    public function getStatus(): string;

    public function getPriority(): string;

    public function getStatusLabel(): string;

    public function getPriorityLabel(): string;

    // ─── Deadline ─────────────────────────────────────────────────────────────

    public function getDeadline(): ?Carbon;

    public function isOverdue(): bool;

    public function isDeadlineReminderSent(): bool;

    // ─── State Checks ─────────────────────────────────────────────────────────

    public function isPending(): bool;

    public function isInProgress(): bool;

    public function isOnHold(): bool;

    public function isCompleted(): bool;

    public function isCancelled(): bool;

    // ─── Relationships (for Eloquent models) ─────────────────────────────────

    public function creator();

    public function assignee();

    public function group();

    public function comments();

    public function groupComments();

    public function internalComments();

    public function followups();

    public function followers();

    public function notifications();
}
