<?php

namespace STS\Beankeep;

use Illuminate\Support\ServiceProvider;

class BeankeepServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
