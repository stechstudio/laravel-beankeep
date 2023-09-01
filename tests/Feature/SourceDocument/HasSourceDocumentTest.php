<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature\SourceDocument;

use STS\Beankeep\Models\SourceDocument as BeankeepSourceDocument;
use STS\Beankeep\Tests\TestCase;
use STS\Beankeep\Tests\TestSupport\Models\Augmented\SourceDocument;
use STS\Beankeep\Tests\TestSupport\Traits\GeneratesJournalData;

final class HasSourceDocumentTest extends TestCase
{
    use GeneratesJournalData;

    public function testItKnowsItsBeankeepClass(): void
    {
        $this->assertEquals(
            BeankeepSourceDocument::class,
            SourceDocument::beankeeperClass(),
        );
    }

    public function testItCanBeAssociatedWithAnEndUserSourceDocumentModel(): void
    {
        $transaction = $this->txn(
            '10/15/2023',
            dr: ['equipment', 5000.00],
            cr: ['accounts-payable', 5000.00],
        );

        $transaction->sourceDocuments()->save(
            BeankeepSourceDocument::factory()->make([
                'filename' => 'computers-receipt.pdf',
            ]),
        );

        $sourceDocument = $transaction->sourceDocuments()->first();
        $sourceDocument->keep(tap(SourceDocument::create()->twoStars())->save());

        $this->assertEquals(SourceDocument::RATING_TWO_STARS, $sourceDocument->keepable->rating);
    }
}
