<?php

declare(strict_types=1);

namespace STS\Beankeep\Concerns;

use STS\Beankeep\Contracts\Serializable;

trait SavesBeankeeper
{
    public static function bootSavesBeankeeper(): void
    {
        static::saved(function (Serializable $model) {
            if ($keeper = $model->keeper) {
                $keeper->update($model->serializeToBeankeep());

                return;
            }

            $keeperAttributes = $model->serializeToBeankeep();
            $keeper = static::beankeeperClass()::create($keeperAttributes);
            $keeper->keepable()->save($model);
        });
    }
}
