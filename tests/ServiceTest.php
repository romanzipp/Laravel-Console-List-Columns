<?php

namespace romanzipp\ColumnList\Test;

use romanzipp\ColumnList\Services\ProcessingService;

class ServiceTest extends TestCase
{
    public function testTableIsMissing()
    {
        $processing = new ProcessingService('missing');
        $processing->fetch();

        self::assertFalse($processing->exists());
    }

    public function testTableExists()
    {
        $processing = new ProcessingService('table_two');
        $processing->fetch();

        self::assertTrue($processing->exists());
    }
}
