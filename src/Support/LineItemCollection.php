<?php

declare(strict_types=1);

namespace STS\Beankeep\Support;

use Illuminate\Database\Eloquent\Collection;
use STS\Beankeep\Models\Account;

// TODO(zmd): test me
final class LineItemCollection extends Collection
{
    public function debits(): Collection
    {
        $this->filter->isDebit();
    }

    public function credits(): Collection
    {
        $this->filter->isCredit();
    }
}
