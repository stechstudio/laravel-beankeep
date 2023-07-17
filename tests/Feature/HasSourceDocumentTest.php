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

    public function testItCanBeAssociatedWithAnEndUserSourceDocumentModel(): void
    {
        $transaction = $this->simpleTransactor()(
            '2022-10-15',
            '2 computers from computers-r-us',
            5000.00,
            dr: 'equipment',
            cr: 'accounts-payable',
        );

        $sourceDocument = $transaction->sourceDocuments()->first();
        $sourceDocument->keep(tap(SourceDocument::create()->twoStars())->save());

        $this->assertEquals(SourceDocument::RATING_TWO_STARS, $sourceDocument->keepable->rating);
    }
}
