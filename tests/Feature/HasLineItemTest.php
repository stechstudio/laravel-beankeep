<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature;

use STS\Beankeep\Models\LineItem as BeankeepLineItem;
use STS\Beankeep\Tests\TestCase;
use STS\Beankeep\Tests\TestSupport\Models\Augmented\LineItem;
use STS\Beankeep\Tests\TestSupport\Traits\BeanConstructors;

final class HasLineItemTest extends TestCase
{
    use BeanConstructors;

    public function testItKnowsItsBeankeepClass(): void
    {
        $this->assertEquals(
            BeankeepLineItem::class,
            LineItem::beankeeperClass(),
        );
    }

    public function testItCanBeAssociatedWithAnEndUserLineItemModel(): void
    {
        $accounts = $this->createAccounts();

        $this->assertTrue(false, 'TODO(zmd): finish me!');
    }
}
