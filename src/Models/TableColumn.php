<?php

namespace romanzipp\ColumnList\Models;

use romanzipp\ColumnList\Models\Model;

class TableColumn extends Model
{
    public $name;

    public $cells;

    public function getMaxLength(): int
    {
        $max = 0;

        foreach ($this->cells as $cell) {

            $cellLength = $cell->getLength() + 2;

            if ($cellLength > $max) {
                $max = $cellLength;
            }
        }

        return $max;
    }

    public function formatName(): string
    {
        return $this->name . str_repeat(' ', $this->getMaxLength() - strlen($this->name));
    }
}
