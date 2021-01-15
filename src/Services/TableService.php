<?php

namespace romanzipp\ColumnList\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use LogicException;

class TableService
{
    /**
     * The database connection to be used.
     *
     * @var \Illuminate\Database\ConnectionInterface
     */
    protected $connection;

    /**
     * The package config.
     *
     * @var array
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

    public function getTables(): array
    {
        if ( ! $this->config['match_similar']) {
            return $this->tableNames;
        }

        $existingTables = $this->getExistingTables();

        $tables = [];

        foreach ($existingTables as $existingTable) {
            /** @var \stdClass $existingTable */
            $value = array_values(
                array_filter(
                    get_object_vars($existingTable), static function ($value) {
                        return 'BASE TABLE' !== $value;
                    }
                )
            )[0];

            if ( ! Str::contains($value, $this->tableNames)) {
                continue;
            }

            $tables[] = $value;
        }

        return $tables;
    }

    public function getExistingTables(): array
    {
        if (false === $this->config['match_similar'] || ! $this->supportsGettingAllTables()) {
            return [];
        }

        /**
         * @var \Illuminate\Database\Schema\MySqlBuilder|\Illuminate\Database\Schema\PostgresBuilder $schemaBuilder
         */
        $schemaBuilder = $this->connection->getSchemaBuilder();

        return $schemaBuilder->getAllTables();
    }

    private function supportsGettingAllTables(): bool
    {
        if ( ! method_exists($schemaBuilder = $this->connection->getSchemaBuilder(), 'getAllTables')) {
            return false;
        }

        try {
            $schemaBuilder->getAllTables();
        } catch (LogicException $exception) {
            return false;
        }

        return true;
    }
}
