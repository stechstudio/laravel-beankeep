<?php

declare(strict_types=1);

namespace STS\Beankeep\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use STS\Beankeep\Contracts\Savable;

trait SaveToBeankeep
{
    public function getBeankeepSavableAttributes(): array;

    public static function bootSaveToBeankeep(): void
    {
        self::created(function (Savable $model) {
            $this->getBeankeeperClass()::keepableCreated($model);
        });

        self::updated(function (Savable $model) {
            $this->getBeankeeperClass()::keepableUpdated($model);
        });

        self::saved(function (Savable $model) {
            $this->getBeankeeperClass()::keepableSaved($model);
        });
    }
}
