<?php

namespace Webkul\TaskManager\Contracts;

interface TaskGroup
{
    public function getId(): int;

    public function getName(): string;

    public function getDescription(): ?string;

    public function getCreatedBy(): ?int;

    public function hasUser(int $userId): bool;

    public function addUser(int $userId): void;

    public function removeUser(int $userId): void;

    // ─── Relationships (for Eloquent models) ─────────────────────────────────
    public function creator();

    public function users();

    public function tasks();
}
