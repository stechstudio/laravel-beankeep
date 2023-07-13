<?php

declare(strict_types=1);

namespace STS\Beankeep\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use STS\Beankeep\Models\LineItem;

trait HasLineItem
{
    public function kept(): MorphOne
    {
        return $this->morphOne(LineItem::class, 'keepable');
    }
}
