<?php

namespace romanzipp\ColumnList\Models;

use romanzipp\ColumnList\Models\Model;

class Table extends Model
{
    const VERTICAL = '|'; // │
    const HORIZONTAL = '-'; // ─

    const CORNER_TOP_LEFT = '|'; // ┌
    const CORNER_TOP_RIGHT = '|'; // ┐
    const CORNER_BOTTOM_LEFT = '|'; // └
    const CORNER_BOTTOM_RIGHT = '|'; // ┘

    const BETWEEN = '-'; // ┼
    const BETWEEN_LEFT = '|'; // ├
    const BETWEEN_RIGHT = '|'; // ┤
    const BETWEEN_TOP = '|'; // ┬
    const BETWEEN_BOTTOM = '|'; // ┴

    public $columns;

    public $namne;

    public $columnLengthValues = [];

    public function getMaxLength(): int
    {
        $length = 0;

        foreach ($this->columns as $column) {
            $length += $column->getMaxLength();
        }

        return $length;
    }

    public function generateHead(): string
    {
        $output = [];

        $tableLength = $this->getMaxLength();

        $output[] = self::CORNER_TOP_LEFT . str_repeat(self::HORIZONTAL, $tableLength);
        $output[] = self::VERTICAL . ' Table: ' . $this->name;

        $splittedSpacer = self::BETWEEN_LEFT . str_repeat(self::HORIZONTAL, $tableLength);

        $incSplitterInsert = 0;

        foreach ($this->columns as $key => $column) {

            if (count($this->columns) - 1 == $key) {
                continue;
            }

            $incSplitterInsert += $column->getMaxLength() + 2;

            $splittedSpacer = substr_replace($splittedSpacer, self::HORIZONTAL, $incSplitterInsert, 1);
        }

        $output[] = $splittedSpacer;

        $columnHead = '';

        foreach ($this->columns as $column) {
            $columnHead .= self::VERTICAL . ' ' . $column->formatName();
        }

        $output[] = $columnHead;
        $output[] = $splittedSpacer;

        return implode(PHP_EOL, $output);
    }

    public function generateBody(): string
    {
        $output = [];

        foreach ($this->columns as $column) {

            $columnLength = $column->getMaxLength();

            foreach ($column->cells as $key => $cell) {

                if ( ! isset($output[$key])) {
                    $output[$key] = '';
                }

                $output[$key] .= self::VERTICAL . ' ' . $cell->formatOnLength($columnLength);
            }
        }

        return implode(PHP_EOL, $output);
    }

    private function getTypeColumnEmoji(string $type): string
    {
        switch ($type) {

            case 'integer':
                return '1⃣';

            case 'varchar':
            case 'text':
                return '🅰️';

            case 'boolean':
                return '🆗';

            case 'datetime':
                return '🕑';

            default:
                return '❔';
        }
    }
}
