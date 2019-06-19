<?php

namespace Just\Warehouse\Nova\Scan\Tests;

use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as Orchestra;
use Just\Warehouse\Nova\Scan\ToolServiceProvider;

abstract class TestCase extends Orchestra
{
    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        Route::middlewareGroup('nova', []);
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            ToolServiceProvider::class,
        ];
    }
}
