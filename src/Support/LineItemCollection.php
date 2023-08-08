<?php

declare(strict_types=1);

namespace STS\Beankeep\Support;

use Illuminate\Database\Eloquent\Collection;
use STS\Beankeep\Models\Account;

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
    public function getSums(): array
    {
        return [
            $this->sumDebits(),
            $this->sumCredits(),
        ];
    }

    // TODO(zmd): test me
    public function subDebits(): int
    {
        // TODO(zmd): implement me
    }

    // TODO(zmd): test me
    public function subCredits(): int
    {
        // TODO(zmd): implement me
    }
}
