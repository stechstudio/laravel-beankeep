<?php

declare(strict_types=1);

namespace STS\Beankeep\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use STS\Beankeep\Models\Account;

trait HasAccount
{
    public static function beankeeperClass(): string
    {
        return Account::class;
    }

    public function keeper(): MorphOne
    {
        return $this->morphOne(static::beankeeperClass(), 'keepable');
    }
}
