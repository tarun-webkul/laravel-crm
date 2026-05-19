<?php

namespace Webkul\TaskManager\Contracts;

interface TaskComment
{
    public function getId(): int;

    public function getTaskId(): int;

    public function getUserId(): ?int;

    public function getType(): string;       // 'internal' | 'group'

    public function getComment(): string;

    public function isEdited(): bool;

    public function isInternal(): bool;

    public function isGroupComment(): bool;

    public function isVisibleTo(int $userId): bool;

    // ─── Relationships (for Eloquent models) ─────────────────────────────────
    public function task();

    public function user();
}
