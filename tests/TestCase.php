<?php

namespace Pawsmedz\JsonFilter\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Pawsmedz\JsonFilter\JsonFilterServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [JsonFilterServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Create in-memory test table
        $this->loadMigrations();
    }

    protected function loadMigrations(): void
    {
        $schema = $this->app['db']->connection()->getSchemaBuilder();
        $schema->create('users', function ($table) {
            $table->id();
            $table->json('meta')->nullable();
        });
    }
}
