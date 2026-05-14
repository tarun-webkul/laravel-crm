<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();

            // Ownership
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('group_id')->nullable();

            // Status & priority
            $table->string('status')->default('pending');
            $table->string('priority')->default('medium');

            // Deadline
            $table->dateTime('deadline')->nullable();
            $table->boolean('deadline_reminder_sent')->default(false);

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->foreign('group_id')->references('id')->on('task_groups')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            // Indexes for common queries
            $table->index(['assigned_to', 'status']);
            $table->index(['group_id', 'status']);
            $table->index(['deadline', 'status']);
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
