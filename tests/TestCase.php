<?php

namespace romanzipp\ColumnList\Test;

use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [];
    }

    protected function getPackageAliases($app)
    {
        return [];
    }
}
