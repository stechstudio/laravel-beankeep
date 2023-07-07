<?php

declare(strict_types=1);

namespace STS\Beankeep\Traits;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use STS\Beankeep\Models\LineItem;

trait KeepableLineItem
{
    public function lineItem(): MorphOne
    {
        return $this->morphOne(LineItem::class, 'keepable');
    }
}
