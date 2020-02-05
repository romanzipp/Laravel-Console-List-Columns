<?php

namespace romanzipp\ColumnList\Services;

use Illuminate\Support\Facades\DB;
use LucidFrame\Console\ConsoleTable;
use Spatie\Emoji\Emoji;
use Wujunze\Colors;

class ProcessingService
{
    const LARAVEL_COLUMNS = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Table name.
     *
     * @var string|null
     */
    protected $tableName = null;

    /**
     * The database connection to be used.
     *
     * @var mixed
     */
    protected $connection;

    /**
     * Printable cli table.
     *
     * @var \LucidFrame\Console\ConsoleTable
     */
    protected $table;

    /**
     * The package config.
     *
     * @var array
     */
    protected $config;

    /**
     * Available sets of printable columns.
     *
     * @var array
     */
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

    /**
     * Enabled columns for output.
     *
     * @var array
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
        $this->table = new ConsoleTable;

        $this->table->setPadding($this->config['padding'] ?? 1);
        $this->table->setIndent($this->config['indent'] ?? 0);

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
        echo ' ' . $this->tableName;

        echo PHP_EOL;

        $this->table->display();

        echo PHP_EOL;
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

        $this->table->setHeaders($headers);
    }

    /**
     * Wether a specified column is enabled via config.
     *
     * @param string $key Column key
     * @return boolean
     */
    private function columnEnabled(string $key): bool
    {
        return config('column-list.display_columns.' . $key) == true;
    }

    /**
     * Fetch the table columns.
     *
     * @return void
     */
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

    /**
     * Skip disabled columns.
     *
     * @param array $values All values
     * @return array Array values for output
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
     * @return array                        Formatted array of column information
     */
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
            'nullable'       => $this->beautifyBooleanValue(! $column->getNotnull()),
            'auto_increment' => $this->beautifyBooleanValue($column->getAutoincrement()),
            'default'        => $column->getDefault() ?? $none,
            'comment'        => $column->getComment() ?? $none,
        ];
    }

    /**
     * Pretty print a boolean value.
     *
     * @param bool $value Boolean value
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
     * @return string Output string
     */
    private function coloredString(string $string, string $color): string
    {
        if ( ! $this->config['colors']) {
            return $string;
        }

        return (new Colors)->getColoredString($string, $color);
    }

    /**
     * Get the emoji for specified column type.
     *
     * @param string $type Column Type
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
