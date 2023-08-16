<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\TestSupport\Traits;

use STS\Beankeep\Models\LineItem;

trait CanMakeLineItems
{
    protected function debit(float $amount): LineItem
    {
        return new LineItem(['debit' => $this->floatToInt($amount)]);
    }

    protected function credit(float $amount): LineItem
    {
        return new LineItem(['credit' => $this->floatToInt($amount)]);
    }

    protected function floatToInt(float $amount): int
    {
        return (int) ($amount * 100);
    }
}
