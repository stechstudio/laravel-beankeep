<?php

declare(strict_types=1);

namespace STS\Beankeep\Support;

use Illuminate\Database\Eloquent\Collection;
use STS\Beankeep\Models\Account;

// TODO(zmd): test me
class LineItemCollection extends Collection
{
    public function debits(): Collection
    {
        return $this->filter->isDebit();
    }

    public function credits(): Collection
    {
        return $this->filter->isCredit();
    }
}
