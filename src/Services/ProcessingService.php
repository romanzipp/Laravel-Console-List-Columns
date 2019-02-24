<?php

namespace romanzipp\ColumnList\Services;

use Illuminate\Support\Facades\DB;
use romanzipp\ColumnList\Models\Table;
use romanzipp\ColumnList\Models\TableCell;
use romanzipp\ColumnList\Models\TableColumn;

class ProcessingService
{
    protected $tableName = null;

    protected $databaseColumns;

    protected $table;

    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    public function fetch(): void
    {
        $this->table = new Table([
            'name' => $this->tableName,
        ]);

        $this->table->columns = collect([]);

        $this->fetchTableColumns();
    }

    private function fetchTableColumns(): void
    {
        $names = collect([]);

        $types = collect([]);

        foreach (DB::getSchemaBuilder()->getColumnListing($this->tableName) as $key => $name) {

            $type = DB::connection()->getDoctrineColumn($this->tableName, $name)->getType()->getName();

            $names->push(new TableCell([
                'value' => $name,
            ]));

            $types->push(new TableCell([
                'value' => $type,
            ]));
        }

        $this->table->columns->push(new TableColumn([
            'name'  => 'Column',
            'cells' => $names,
        ]));

        $this->table->columns->push(new TableColumn([
            'name'  => 'Type',
            'cells' => $types,
        ]));
    }

    public function output(): void
    {
        echo $this->table->generateHead();

        echo PHP_EOL;

        echo $this->table->generateBody();

        echo PHP_EOL;
    }
}
