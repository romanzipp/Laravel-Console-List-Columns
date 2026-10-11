<?php

namespace romanzipp\ColumnList\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TableService
{
    /**
     * The database connection to be used.
     *
     * @var \Illuminate\Database\Connection
     */
    protected $connection;

    /**
     * The package config.
     *
     * @var array<string, mixed>
     */
    protected $config = [];

    /**
     * @var string[]
     */
    protected $tableNames = [];

    public function __construct(string $input)
    {
        $this->tableNames = explode(',', $input);

        $this->config = config('column-list');

        $this->connection = DB::connection($this->config['connection']);
    }

    /**
     * @return string[]
     */
    public function getTables(): array
    {
        if ( ! $this->config['match_similar']) {
            return $this->tableNames;
        }

        $tables = [];

        foreach ($this->getExistingTables() as $value) {
            if ( ! Str::contains($value, $this->tableNames)) {
                continue;
            }

            $tables[] = $value;
        }

        return $tables;
    }

    /**
     * @return string[]
     */
    public function getExistingTables(): array
    {
        if (false === $this->config['match_similar']) {
            return [];
        }

        return array_column($this->connection->getSchemaBuilder()->getTables(), 'name');
    }
}
