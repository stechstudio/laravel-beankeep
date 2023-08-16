<?php

declare(strict_types=1);

namespace STS\Beankeep\Support;

use Illuminate\Database\Eloquent\Collection;

class LineItemCollection extends Collection
{
    // TODO(zmd): test me
    public function debits(): Collection
    {
        return $this->filter->isDebit();
    }

    // TODO(zmd): test me
    public function credits(): Collection
    {
        return $this->filter->isCredit();
    }

    // TODO(zmd): test me
    public function sumDebits(): int
    {
        return $this->debits()->sum('debit');
    }

    // TODO(zmd): test me
    public function sumCredits(): int
    {
        return $this->credits()->sum('credit');
    }
}
