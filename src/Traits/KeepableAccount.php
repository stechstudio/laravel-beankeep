<?php

declare(strict_types=1);

namespace STS\Beankeep\Traits;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use STS\Beankeep\Models\Account;

trait KeepableAccount
{
    public function account(): MorphOne
    {
        return $this->morphOne(Account::class, 'keepable');
    }
}
