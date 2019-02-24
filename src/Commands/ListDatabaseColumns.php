<?php

namespace romanzipp\ColumnList\Commands;

use Illuminate\Console\Command;
use romanzipp\ColumnList\Services\ProcessingService;

class ListDatabaseColumns extends Command
{
    protected $signature = 'db:columns {--table=} {--connection=}';

    protected $description = 'List database columns';

    private $emojis = false;

    private $longestColumnName = 0;

    public function handle()
    {
        $tables = [];

        if ($selectedTable = $this->option('table')) {
            $tables[] = $selectedTable;
        }

        foreach ($tables as $table) {

            $processing = new ProcessingService($table);

            $processing->fetch();
            $processing->output();
        }
    }
}
