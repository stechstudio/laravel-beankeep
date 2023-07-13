<?php

declare(strict_types=1);

namespace STS\Beankeep\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use STS\Beankeep\Models\Account;

trait HasAccount
{
    public function kept(): MorphOne
    {
        return $this->morphOne(Account::class, 'keepable');
    }
}
