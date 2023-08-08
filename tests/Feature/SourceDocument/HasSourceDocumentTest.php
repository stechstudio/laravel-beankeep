<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature\SourceDocument;

use STS\Beankeep\Database\Factories\Support\HasRelativeTransactor;
use STS\Beankeep\Models\SourceDocument as BeankeepSourceDocument;
use STS\Beankeep\Tests\TestCase;
use STS\Beankeep\Tests\TestSupport\Models\Augmented\SourceDocument;
use STS\Beankeep\Tests\TestSupport\Traits\CanCreateAccounts;

final class HasSourceDocumentTest extends TestCase
{
    use CanCreateAccounts;
    use HasRelativeTransactor;

    public function testItKnowsItsBeankeepClass(): void
    {
        $this->assertEquals(
            BeankeepSourceDocument::class,
            SourceDocument::beankeeperClass(),
        );
    }

    public function testItCanBeAssociatedWithAnEndUserSourceDocumentModel(): void
    {
        $this->createAccounts();
        $transaction = $this->thisYear('10/15')
            ->transact('2 computers from computers-r-us')
            ->line('equipment', dr: 5000.00)
            ->line('accounts-payable', cr: 5000.00)
            ->doc('computers-receipt.pdf')
            ->post();

        $sourceDocument = $transaction->sourceDocuments()->first();
        $sourceDocument->keep(tap(SourceDocument::create()->twoStars())->save());

        $this->assertEquals(SourceDocument::RATING_TWO_STARS, $sourceDocument->keepable->rating);
    }
}
