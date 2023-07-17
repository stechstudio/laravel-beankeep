<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature;

use STS\Beankeep\Models\Transaction as BeankeepTransaction;
use STS\Beankeep\Tests\TestCase;
use STS\Beankeep\Tests\TestSupport\Models\Augmented\Transaction;
use STS\Beankeep\Tests\TestSupport\Traits\BeanConstructors;

final class HasTransactionTest extends TestCase
{
    use BeanConstructors;

    public function testItKnowsItsBeankeepClass(): void
    {
        $this->assertEquals(
            BeankeepTransaction::class,
            Transaction::beankeeperClass(),
        );
    }

    public function testItCanBeAssociatedWithAnEndUserTransactionModel(): void
    {
        $transaction = $this->simpleTransactor()(
            '2022-10-15',
            '2 computers from computers-r-us',
            5000.00,
            dr: 'equipment',
            cr: 'accounts-payable',
        );

        $transaction->keep(Transaction::create(['flag_for_review' => true]));

        $this->assertTrue($transaction->keepable->flag_for_review);
    }
}
