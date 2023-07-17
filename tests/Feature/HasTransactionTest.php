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
}
