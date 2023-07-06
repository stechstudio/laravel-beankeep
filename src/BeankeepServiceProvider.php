<?php

namespace STS\Beankeep;

use Illuminate\Support\ServiceProvider;

class BeankeepServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // TODO(zmd): dynamically generate MorphOne relations for any
        //   consumer's models which have used the keeptable traits back to the
        //   appropriate models
    }
}
