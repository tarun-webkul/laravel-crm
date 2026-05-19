<?php

namespace Webkul\TaskManager\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ManageTaskManagerCommand extends Command
{
    /**
     * Console command signature.
     *
     * Available:
     * php artisan task-manager:install
     */
    protected $signature = 'task-manager:install';

    /**
     * Console command description.
     */
    protected $description = 'Install Task Manager package with migrations and seeders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->newLine();

        $this->info('======================================');
        $this->info('Installing Task Manager Package');
        $this->info('======================================');

        /**
         * Run migrations
         */
        $this->line('Running migrations...');

        Artisan::call('migrate', [
            '--force' => true,
        ]);

        $this->info(Artisan::output());

        /**
         * Run seeders
         */
        $this->line('Running seeders...');

        Artisan::call('db:seed', [
            '--class' => 'Webkul\TaskManager\Database\Seeders\DatabaseSeeder',
            '--force' => true,
        ]);

        $this->info(Artisan::output());

        /**
         * Clear cache
         */
        $this->line('Clearing caches...');

        Artisan::call('optimize:clear');

        $this->info(Artisan::output());

        $this->newLine();

        $this->info('======================================');
        $this->info('Task Manager installed successfully.');
        $this->info('======================================');

        return Command::SUCCESS;
    }
}
