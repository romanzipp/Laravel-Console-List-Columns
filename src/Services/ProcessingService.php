<?php

namespace romanzipp\ColumnList\Services;

use Illuminate\Support\Facades\DB;
use LucidFrame\Console\ConsoleTable;
use romanzipp\ColumnList\Models\Table;
use Spatie\Emoji\Emoji;
use Wujunze\Colors;

class ProcessingService
{
    const LARAVEL_COLUMNS = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $tableName = null;

    protected $connection;

    protected $table;

    protected $rows;

    protected $config;

    private static $availableColumns = [
        'name'           => 'Name',
        'laravel'        => 'Laravel',
        'type'           => 'Type',
        'length'         => 'Length',
        'nullable'       => 'Nullable',
        'auto_increment' => 'Auto Increment',
        'default'        => 'Default',
        'comment'        => 'Comment',
    ];

    private $enabledColumns = [];

    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;

        $this->connection = DB::connection($this->config['connection']);

        $this->config = config('column-list');

        foreach (self::$availableColumns as $key => $title) {

            if ( ! $this->columnEnabled($key)) {
                continue;
            }

            $this->enabledColumns[] = $key;
        }
    }

    public function fetch(): void
    {
        $this->table = new ConsoleTable;

        $this->table->setPadding($this->config['padding'] ?? 1);
        $this->table->setIndent($this->config['indent'] ?? 0);

        $this->setHeaders();

        $this->fetchTableColumns();
    }

    public function output(): void
    {
        echo ' ' . $this->tableName;

        echo PHP_EOL;

        $this->table->display();

        echo PHP_EOL;
    }

    private function setHeaders(): void
    {
        $headers = [];

        foreach ($this->enabledColumns as $key) {
            $headers[] = self::$availableColumns[$key];
        }

        $this->table->setHeaders($headers);
    }

    private function columnEnabled(string $key): bool
    {
        return config('column-list.display_columns.' . $key) == true;
    }

    private function fetchTableColumns(): void
    {
        foreach ($this->connection->getSchemaBuilder()->getColumnListing($this->tableName) as $key => $name) {

            $column = $this->connection->getDoctrineColumn(
                $this->tableName,
                $name
            );

            $values = $this->processColumnPreferences(
                $column
            );

            $row = $this->populateRow(
                $values
            );

            $this->table->addRow($row);
        }
    }

    private function populateRow(array $values): array
    {
        foreach ($values as $key => $value) {

            if (in_array($key, $this->enabledColumns)) {
                continue;
            }

            unset($values[$key]);
        }

        return array_values($values);
    }

    private function processColumnPreferences($column): array
    {
        $name = $column->getName();
        $type = $column->getType()->getName();

        $none = $this->coloredString('-', 'dark_gray');

        return [
            'name'           => $this->coloredString($name, 'cyan'),
            'laravel'        => $this->beautifyBooleanValue(in_array($name, self::LARAVEL_COLUMNS)),
            'type'           => ($this->config['emojis'] ? $this->getTypeColumnEmoji($type) . ' ' : '') . $type,
            'length'         => $column->getLength() ?? $none,
            'nullable'       => $this->beautifyBooleanValue( ! $column->getNotnull()),
            'auto_increment' => $this->beautifyBooleanValue($column->getAutoincrement()),
            'default'        => $column->getDefault() ?? $none,
            'comment'        => $column->getComment() ?? $none,
        ];
    }

    private function beautifyBooleanValue(bool $value): string
    {
        return $value ? $this->coloredString('y', 'green') : $this->coloredString('n', 'red');
    }

    private function coloredString(string $string, $color): string
    {
        if ( ! $this->config['colors']) {
            return $string;
        }

        return (new Colors)->getColoredString($string, $color);
    }

    private function getTypeColumnEmoji(string $type): string
    {
        switch ($type) {

            case 'integer':
                return Emoji::inputNumbers();

            case 'varchar':
            case 'string':
            case 'text':
                return Emoji::inputLatinLetters();

            case 'boolean':
                return Emoji::okButton();

            case 'timestamp':
            case 'datetime':
                return Emoji::twoOClock();

            default:
                return Emoji::whiteQuestionMark();
        }
    }
}
