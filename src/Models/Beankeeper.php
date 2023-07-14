<?php

declare(strict_types=1);

namespace STS\Beankeep\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

abstract class Beankeeper extends Model
{
    public function keepable(): MorphTo
    {
        return $this->morphTo();
    }
}
