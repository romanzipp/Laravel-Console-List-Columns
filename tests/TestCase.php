<?php

namespace romanzipp\ColumnList\Test;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as BaseTestCase;
use romanzipp\ColumnList\Providers\ColumnListProvider;

abstract class TestCase extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->setupDatabase($this->app);
    }

    protected function getPackageProviders($app)
    {
        return [
            ColumnListProvider::class,
        ];
    }

    protected function setupDatabase(Application $app)
    {
        $app['db']->connection()->getSchemaBuilder()->create('table_one', function (Blueprint $table) {
            $table->integer('integer')->autoIncrement();
            $table->integer('string');
        });

        $app['db']->connection()->getSchemaBuilder()->create('table_two', function (Blueprint $table) {
            $table->integer('integer')->autoIncrement();
            $table->integer('string');
        });
    }
}
