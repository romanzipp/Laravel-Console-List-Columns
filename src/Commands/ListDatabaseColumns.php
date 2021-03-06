<?php

namespace romanzipp\ColumnList\Commands;

use Illuminate\Console\Command;
use romanzipp\ColumnList\Services\ProcessingService;
use romanzipp\ColumnList\Services\TableService;

class ListDatabaseColumns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:cols
                {table : Comma separated table names to print out}
                {--connection= : Specified database connection}
                {--no-colors : Don\'t use colors in output}
                {--no-emojis : Don\'t use emojis in output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List database columns';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        if ($connection = $this->option('connection')) {
            config(['column-list.connection' => $connection]);
        }

        if ($this->option('no-colors')) {
            config(['column-list.colors' => false]);
        }

        if ($this->option('no-emojis')) {
            config(['column-list.emojis' => false]);
        }

        $tables = new TableService(
            $this->argument('table')
        );

        foreach ($tables->getTables() as $table) {
            $processing = new ProcessingService($table);

            $processing->fetch();

            if ( ! $processing->exists()) {
                continue;
            }

            $processing->output();
        }
    }
}
