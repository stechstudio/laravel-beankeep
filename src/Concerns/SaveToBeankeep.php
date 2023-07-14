<?php

declare(strict_types=1);

namespace STS\Beankeep\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use STS\Beankeep\Contracts\Savable;

trait SaveToBeankeep
{
    public static function bootSaveToBeankeep(): void
    {
        self::created(function (Savable $model) {
            $keeper = $model->convertToBeankeep();
            $model->keeper()->associate($keeper);

            $kept->save();
        });
    }
}
