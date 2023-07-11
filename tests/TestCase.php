<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as BaseTestCase;
use STS\Beankeep\BeankeepServiceProvider;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function getPackageProviders($app): array
    {
        return [
            BeankeepServiceProvider::class,
        ];
    }
}
