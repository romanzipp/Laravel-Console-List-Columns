<?php

namespace romanzipp\ColumnList\Providers;

use Illuminate\Support\ServiceProvider;
use romanzipp\ColumnList\Commands\ListDatabaseColumns;

class ColumnListProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            dirname(__DIR__) . '/../config/column-list.php' => config_path('column-list.php'),
        ], 'config');

        if ($this->app->runningInConsole()) {

            $this->commands([
                ListDatabaseColumns::class,
            ]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/../config/column-list.php', 'column-list'
        );
    }
}
