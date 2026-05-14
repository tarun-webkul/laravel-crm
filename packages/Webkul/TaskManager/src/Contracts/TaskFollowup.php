<?php

namespace Webkul\TaskManager\Contracts;

interface TaskFollowup
{
    public function getId(): int;

    public function getTaskId(): int;

    public function getUserId(): int;

    public function getReason(): ?string;

    // ─── Relationships (for Eloquent models) ─────────────────────────────────
    public function task();

    public function user();
}
