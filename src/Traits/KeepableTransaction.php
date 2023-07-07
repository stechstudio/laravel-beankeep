<?php

declare(strict_types=1);

namespace STS\Beankeep\Traits;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use STS\Beankeep\Models\Transaction;

trait KeepableTransaction
{
    public function transaction(): MorphOne
    {
        return $this->morphOne(Transaction::class, 'keepable');
    }
}
