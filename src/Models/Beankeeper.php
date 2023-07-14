<?php

declare(strict_types=1);

namespace STS\Beankeep\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use STS\Beankeep\Contracts\Savable;

abstract class Beankeeper extends Model
{
    public function keepable(): MorphTo
    {
        return $this->morphTo();
    }

    // TODO(zmd): refactor around stand-alone event and event-handler classes
    public static function keepableCreated(Savable $model): void
    {
        $account = static::create($model->serializeToBeankeep());

        // TODO(zmd): save, or attach, or ?
        $account->keepable()->save($model);
    }
}
