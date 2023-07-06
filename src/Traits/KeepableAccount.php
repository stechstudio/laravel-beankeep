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

    public function bootKeepableAccount(): void
    {
        // TODO(zmd): boot the keepable account model (hooks to ensure data
        //   integrity, calculating checksum based on IsKeepable interface
        //   (TBD), // etc.)
    }
}
