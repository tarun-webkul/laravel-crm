<?php

namespace Webkul\TaskManager\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class TaskManagerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->register(ModuleServiceProvider::class);

        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        $this->loadRoutesFrom(__DIR__.'/../Routes/Admin/task-manager-routes.php');

        $this->loadRoutesFrom(__DIR__.'/../Routes/breadcrumbs.php');

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'taskmanager');

        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'taskmanager');

        Route::middleware(['web', 'admin_locale', 'user'])
            ->group(__DIR__.'/../Routes/Admin/task-manager-routes.php');
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerConfig();
    }

    /**
     * Register package config.
     */
    protected function registerConfig(): void
    {
        $configPath = dirname(__DIR__).'/Config';

        $this->mergeConfigFrom($configPath.'/menu.php', 'menu.admin');
        $this->mergeConfigFrom($configPath.'/acl.php', 'acl');
        $this->mergeConfigFrom($configPath.'/attribute_entity_types.php', 'attribute_entity_types');
        $this->mergeConfigFrom($configPath.'/attribute_lookups.php', 'attribute_lookups');

    }
}
