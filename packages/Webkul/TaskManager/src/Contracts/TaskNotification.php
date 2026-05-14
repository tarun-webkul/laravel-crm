<?php

namespace Webkul\TaskManager\Contracts;

use Illuminate\Support\Carbon;

interface TaskNotification
{
    public function getId(): int;

    public function getUserId(): int;

    public function getTaskId(): ?int;

    public function getType(): string;

    public function getMessage(): string;

    public function getData(): ?array;

    public function isRead(): bool;

    public function getReadAt(): ?Carbon;

    public function markAsRead(): void;

    // ─── Relationships (for Eloquent models) ─────────────────────────────────
    public function task();

    public function user();
}
