<?php

namespace romanzipp\ColumnList\Models;

use romanzipp\ColumnList\Models\Model;

class TableCell extends Model
{
    public $value;

    public function getLength(): int
    {
        return strlen($this->value);
    }

    public function formatOnLength(int $length): string
    {
        return $this->value . str_repeat(' ', $length - $this->getLength());
    }
}
