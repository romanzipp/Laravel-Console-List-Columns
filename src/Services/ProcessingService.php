<?php

namespace romanzipp\ColumnList\Services;

use Illuminate\Support\Facades\DB;
use Spatie\Emoji\Emoji;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;
use Wujunze\Colors;

class ProcessingService
{
    private const LARAVEL_COLUMNS = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Table name.
     *
     * @var string|null
     */
    protected $tableName;

    /**
     * The database connection to be used.
     *
     * @var \Illuminate\Database\Connection
     */
    protected $connection;

    /**
     * Printable cli table.
     *
     * @var Table
     */
    protected $table;

    /**
     * The package config.
     *
     * @var array<string, mixed>
     */
    protected $config = [];

    /**
     * Determine if the table is missing.
     *
     * @var bool
     */
    protected $missing = false;

    /**
     * Available sets of printable columns.
     *
     * @var array<string, mixed>
     */
    private static $availableColumns = [
        'name' => 'Name',
        'type' => 'Type',
        'length' => 'Length',
        'nullable' => 'Nullable',
        'auto_increment' => 'Auto Increment',
        'default' => 'Default',
        'comment' => 'Comment',
    ];

    /**
     * Enabled columns for output.
     *
     * @var string[]
     */
    private $enabledColumns = [];

    /**
     * Constructor.
     *
     * @param string $tableName Table name
     */
    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;

        $this->config = config('column-list');

        $this->connection = DB::connection($this->config['connection']);

        foreach (self::$availableColumns as $key => $title) {
            if ( ! $this->columnEnabled($key)) {
                continue;
            }

            $this->enabledColumns[] = $key;
        }
    }

    /**
     * Fetch table & column information.
     *
     * @return void
     */
    public function fetch(): void
    {
        $this->table = new Table(
            new ConsoleOutput()
        );

        $this->table->setStyle($this->config['style'] ?? 'box');

        $this->setHeaders();

        $this->fetchTableColumns();
    }

    /**
     * Output table information to console.
     *
     * @return void
     */
    public function output(): void
    {
        $this->table->render();
    }

    /**
     * @return bool
     */
    public function exists(): bool
    {
        return ! $this->missing;
    }

    /**
     * Set the table headers.
     *
     * @return void
     */
    private function setHeaders(): void
    {
        $headers = [];

        foreach ($this->enabledColumns as $key) {
            $headers[] = self::$availableColumns[$key];
        }

        $this->table->setHeaderTitle($this->tableName);
        $this->table->setHeaders($headers);
    }

    /**
     * Weather a specified column is enabled via config.
     *
     * @param string $key Column key
     *
     * @return bool
     */
    private function columnEnabled(string $key): bool
    {
        return true === config('column-list.display_columns.' . $key);
    }

    /**
     * Fetch the table columns.
     *
     * @return void
     */
    private function fetchTableColumns(): void
    {
        $columnListing = $this->connection->getSchemaBuilder()->getColumnListing($this->tableName);

        if (empty($columnListing)) {
            $this->missing = true;

            return;
        }

        foreach ($columnListing as $key => $name) {
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

    /**
     * Skip disabled columns.
     *
     * @param array<string, string> $values All values
     *
     * @return string[] Array values for output
     */
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

    /**
     * Process the column information.
     *
     * @param \Doctrine\DBAL\Schema\Column $column column
     *
     * @return array<string, string> Formatted array of column information
     */
    private function processColumnPreferences($column): array
    {
        $name = $this->coloredString($column->getName(), 'cyan');

        if ($this->config['mark_laravel_columns'] && in_array($column->getName(), self::LARAVEL_COLUMNS)) {
            $name .= $this->coloredString(' [L]', 'light_red');
        }

        $type = $column->getType()->getName();

        $none = $this->coloredString('-', 'dark_gray');

        return [
            'name' => $name,
            'laravel' => $this->beautifyBooleanValue(in_array($name, self::LARAVEL_COLUMNS)),
            'type' => ($this->config['emojis'] ? $this->getTypeColumnEmoji($type) . ' ' : '') . $type,
            'length' => $column->getLength() ?? $none,
            'nullable' => $this->beautifyBooleanValue( ! $column->getNotnull()),
            'auto_increment' => $this->beautifyBooleanValue($column->getAutoincrement()),
            'default' => $column->getDefault() ?? $none,
            'comment' => $column->getComment() ?? $none,
        ];
    }

    /**
     * Pretty print a boolean value.
     *
     * @param bool $value Boolean value
     *
     * @return string Formatted string
     */
    private function beautifyBooleanValue(bool $value): string
    {
        return $value ? $this->coloredString('y', 'green') : $this->coloredString('n', 'red');
    }

    /**
     * May print a string in colors.
     *
     * @param string $string String
     * @param string $color Color
     *
     * @return string Output string
     */
    private function coloredString(string $string, string $color): string
    {
        if ( ! $this->config['colors']) {
            return $string;
        }

        return (new Colors())->getColoredString($string, $color);
    }

    /**
     * Get the emoji for specified column type.
     *
     * @param string $type Column Type
     *
     * @return string Unicode emoji
     */
    private function getTypeColumnEmoji(string $type): string
    {
        switch ($type) {
            case 'integer':
            case 'bigint':
            case 'tinyint':
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
