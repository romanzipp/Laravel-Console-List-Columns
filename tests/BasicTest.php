<?php

namespace romanzipp\ColumnList\Test;

class BasicTest extends TestCase
{
    public $mockConsoleOutput = true;

    public function testBasicCommandExecution()
    {
        $this->artisan('db:cols table_one')->assertExitCode(0);
        $this->artisan('db:cols table_one --connection')->assertExitCode(0);
        $this->artisan('db:cols table_one --no-colors')->assertExitCode(0);
        $this->artisan('db:cols table_one --no-emojis')->assertExitCode(0);
    }

    public function testBasicAliasCommandExecution()
    {
        $this->artisan('db:columns table_one')->assertExitCode(0);
        $this->artisan('db:columns table_one --connection')->assertExitCode(0);
        $this->artisan('db:columns table_one --no-colors')->assertExitCode(0);
        $this->artisan('db:columns table_one --no-emojis')->assertExitCode(0);
    }

    public function testMissingTable()
    {
        $this->artisan('db:cols missing_table')->assertExitCode(0);
        $this->artisan('db:columns missing_table')->assertExitCode(0);
    }
}
