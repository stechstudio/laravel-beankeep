<?php

namespace STS\Beankeep;

use Illuminate\Support\ServiceProvider;

class BeankeepServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/beankeep.php' => config_path('beankeep.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
