<?php

declare(strict_types=1);

namespace STS\Beankeep\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use STS\Beankeep\Models\LineItem;

trait KeepAsLineItem
{
    public function keeper(): MorphOne
    {
        return $this->morphOne(LineItem::class, 'keepable');
    }

    public function keepAttributes(): array
    {
        return [
            'debit' => $this->getKeepableDebit(),
            'credit' => $this->getKeepableCredit(),
        ];
    }
}
