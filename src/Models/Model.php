<?php

namespace romanzipp\ColumnList\Models;

class Model
{
    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->$key = $value;
        }
    }
}
