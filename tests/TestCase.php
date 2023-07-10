<?php

namespace STS\Beankeep\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use STS\Beankeep\BeankeepServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            BeankeepServiceProvider::class,
        ];
    }
}
