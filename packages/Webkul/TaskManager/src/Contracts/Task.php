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

    // ─── Deadline ─────────────────────────────────────────────────────────────

    public function getDeadline(): ?Carbon;

    public function isOverdue(): bool;

    public function isDeadlineReminderSent(): bool;

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
