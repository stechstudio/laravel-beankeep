<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature;

use STS\Beankeep\Database\Factories\Support\HasRelativeTransactor;
use STS\Beankeep\Models\Transaction as BeankeepTransaction;
use STS\Beankeep\Tests\TestCase;
use STS\Beankeep\Tests\TestSupport\Models\Augmented\Transaction;
use STS\Beankeep\Tests\TestSupport\Traits\CanCreateAccounts;

final class HasTransactionTest extends TestCase
{
    use CanCreateAccounts;
    use HasRelativeTransactor;

    public function testItKnowsItsBeankeepClass(): void
    {
        $this->assertEquals(
            BeankeepTransaction::class,
            Transaction::beankeeperClass(),
        );
    }

    public function testItCanBeAssociatedWithAnEndUserTransactionModel(): void
    {
        $this->createAccounts();
        $transaction = $this->thisYear('10/15')
            ->transact('2 computers from computers-r-us')
            ->line('equipment', dr: 5000.00)
            ->line('accounts-payable', cr: 5000.00)
            ->post();

        $transaction->keep(Transaction::create(['flag_for_review' => true]));

        $this->assertTrue($transaction->keepable->flag_for_review);
    }
}
