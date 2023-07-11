<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use STS\Beankeep\BeankeepServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    protected function getPackageProviders($app): array
    {
        return [
            BeankeepServiceProvider::class,
        ];
    }

    protected function setUpDatabase(): void
    {
        // our library's migrations
        $this->artisan('migrate')->run();
    }
}
