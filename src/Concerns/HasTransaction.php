<?php

declare(strict_types=1);

namespace STS\Beankeep\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use STS\Beankeep\Models\Transaction;

trait HasTransaction
{
    public function keeper(): MorphOne
    {
        return $this->morphOne(Transaction::class, 'keepable');
    }
}
