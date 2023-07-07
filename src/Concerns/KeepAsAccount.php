<?php

declare(strict_types=1);

namespace STS\Beankeep\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use STS\Beankeep\Models\Account;

trait KeepAsAccount
{
    public function keeper(): MorphOne
    {
        return $this->morphOne(Account::class, 'keepable');
    }

    public function keepAttributes(): array
    {
        return [
            'type' => $this->getKeepableType(),
            'name' => $this->getKeepableName(),
            'number' => $this->getKeepableNumber(),
        ];
    }
}
