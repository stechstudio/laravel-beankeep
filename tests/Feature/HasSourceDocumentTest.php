<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature;

use STS\Beankeep\Models\SourceDocument as BeankeepSourceDocument;
use STS\Beankeep\Tests\TestCase;
use STS\Beankeep\Tests\TestSupport\Models\Augmented\SourceDocument;
use STS\Beankeep\Tests\TestSupport\Traits\BeanConstructors;

final class HasSourceDocumentTest extends TestCase
{
    use BeanConstructors;

    public function testItKnowsItsBeankeepClass(): void
    {
        $this->assertEquals(
            BeankeepSourceDocument::class,
            SourceDocument::beankeeperClass(),
        );
    }
}
