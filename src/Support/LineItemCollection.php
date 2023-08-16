<?php

declare(strict_types=1);

namespace STS\Beankeep\Support;

use Illuminate\Database\Eloquent\Collection;

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

    public function sumDebits(): int
    {
        return $this->debits()->sum('debit');
    }

    public function sumCredits(): int
    {
        return $this->credits()->sum('credit');
    }
}
