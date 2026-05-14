<?php

namespace Webkul\TaskManager\Console\Commands;

use Illuminate\Console\Command;
use Webkul\TaskManager\Repositories\TaskNotificationRepository;
use Webkul\TaskManager\Repositories\TaskRepository;

class SendDeadlineReminders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'task-manager:deadline-reminders
                            {--hours=24 : Hours before deadline to send reminder}';

    /**
     * The console command description.
     */
    protected $description = 'Send deadline reminder notifications for upcoming tasks';

    public function __construct(
        protected TaskRepository $taskRepository,
        protected TaskNotificationRepository $notificationRepository,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $hours = (int) $this->option('hours');

        $this->info("Checking tasks with deadlines within {$hours} hour(s)...");

        $tasks = $this->taskRepository->getUpcomingDeadlines($hours);

        if ($tasks->isEmpty()) {
            $this->info('No upcoming deadline tasks found.');

            return 0;
        }

        $count = 0;

        foreach ($tasks as $task) {
            $followers = $task->followers;

            if ($followers->isEmpty()) {
                continue;
            }

            $deadlineFormatted = $task->deadline->format('d M Y H:i');

            $this->notificationRepository->notifyUsers(
                $followers,
                $task,
                'deadline_reminder',
                "⏰ Reminder: Task \"{$task->title}\" is due on {$deadlineFormatted}.",
                [
                    'task_id' => $task->id,
                    'deadline' => $task->deadline->toIso8601String(),
                    'hours' => $hours,
                ]
            );

            // Mark as reminder sent so we don't spam
            $task->update(['deadline_reminder_sent' => true]);

            $count++;

            $this->line("  → Notified {$followers->count()} follower(s) for task: \"{$task->title}\"");
        }

        $this->info("Done. Sent reminders for {$count} task(s).");

        return 0;
    }
}
