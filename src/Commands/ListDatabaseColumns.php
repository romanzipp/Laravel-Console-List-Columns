<?php

namespace romanzipp\ColumnList\Commands;

use Illuminate\Console\Command;
use romanzipp\ColumnList\Services\ProcessingService;

class ListDatabaseColumns extends Command
{
    protected $signature = 'db:columns
                {table : The table to print out}
                {--connection= : Specified database connection}
                {--no-colors : Don\'t use colors in output}
                {--no-emojis : Don\'t use emojis in output}';

    protected $description = 'List database columns';

    private $emojis = false;

    private $longestColumnName = 0;

    public function handle()
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

        $tables = explode(',', $this->argument('table'));

        foreach ($tables as $table) {

            $processing = new ProcessingService($table);

            $processing->fetch();
            $processing->output();
        }
    }
}
