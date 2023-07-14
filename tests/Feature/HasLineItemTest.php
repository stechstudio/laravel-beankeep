<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature;

use STS\Beankeep\Models\LineItem as BeankeepLineItem;
use STS\Beankeep\Tests\TestCase;
use STS\Beankeep\Tests\TestSupport\Models\Augmented\LineItem;

final class HasLineItemTest extends TestCase
{
    public function testItKnowsItsBeankeepClass(): void
    {
        $this->assertEquals(
            BeankeepLineItem::class,
            LineItem::beankeeperClass(),
        );
    }
}
