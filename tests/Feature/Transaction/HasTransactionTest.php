<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature\Transaction;

use STS\Beankeep\Models\Transaction as BeankeepTransaction;
use STS\Beankeep\Tests\TestCase;
use STS\Beankeep\Tests\TestSupport\Models\Augmented\Transaction;
use STS\Beankeep\Tests\TestSupport\Traits\GeneratesJournalData;

final class HasTransactionTest extends TestCase
{
    use GeneratesJournalData;

    public function testItKnowsItsBeankeepClass(): void
    {
        $this->assertEquals(
            BeankeepTransaction::class,
            Transaction::beankeeperClass(),
        );
    }

    public function testItCanBeAssociatedWithAnEndUserTransactionModel(): void
    {
        $transaction = $this->txn(
            '10/15/2023',
            dr: ['equipment', 5000.00],
            cr: ['accounts-payable', 5000.00],
        );

        $transaction->keep(Transaction::create(['flag_for_review' => true]));

        $this->assertTrue($transaction->keepable->flag_for_review);
    }
}
