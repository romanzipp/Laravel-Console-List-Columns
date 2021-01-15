<?php

namespace romanzipp\ColumnList\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ListDatabaseColumnsAlias extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:columns
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
        $options = array_combine(
            array_map(static function ($key) {
                return sprintf('--%s', $key);
            }, array_keys($this->options())),
            array_values($this->options())
        );

        Artisan::call(ListDatabaseColumns::class, array_merge($options, [
            'table' => $this->argument('table'),
        ]));
    }
}
