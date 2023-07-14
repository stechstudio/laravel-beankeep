<?php

declare(strict_types=1);

namespace STS\Beankeep\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use STS\Beankeep\Contracts\Savable;

trait SaveToBeankeep
{
    public static function bootSaveToBeankeep(): void
    {
        // TODO(zmd): refactor around stand-alone event and event-handler
        //   classes
        self::created(function (Savable $model) {
            $this->getBeankeeperClass()::keepableCreated($model);
        });
    }
}
