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

    public function testTableSimilarMatches()
    {
        config(['column-list.match_similar' => true]);

        $processing = new ProcessingService('two');
        $processing->fetch();

        self::assertTrue($processing->exists());
    }
}
