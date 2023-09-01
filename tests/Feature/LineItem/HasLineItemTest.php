<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature\LineItem;

use STS\Beankeep\Models\LineItem as BeankeepLineItem;
use STS\Beankeep\Tests\TestCase;
use STS\Beankeep\Tests\TestSupport\Models\Augmented\LineItem;
use STS\Beankeep\Tests\TestSupport\Traits\GeneratesJournalData;

final class HasLineItemTest extends TestCase
{
    use GeneratesJournalData;

    public function testItKnowsItsBeankeepClass(): void
    {
        $this->assertEquals(
            BeankeepLineItem::class,
            LineItem::beankeeperClass(),
        );
    }

    public function testItCanBeAssociatedWithAnEndUserLineItemModel(): void
    {
        $transaction = $this->txn(
            '10/15/2023',
            dr: ['equipment', 5000.00],
            cr: ['accounts-payable', 5000.00],
        );

        $debit = $transaction->lineItems()->where('debit', '>', 0)->first();
        $debitNarration = "On the 15th of October we did debit our Equipment account in the amount of $5000.00 for the acquisition of 2 new computers from Computers-R-Us";
        $debit->keep(LineItem::create(['narration' => $debitNarration]));

        $credit = $transaction->lineItems()->where('credit', '>', 0)->first();
        $creditNarration = "On the 15th of October we did credit our Accounts Payable account in the amount of $5000.00 for the acquisition of equipment from Computers-R-Us on 30 day terms; we await the invoice.";
        $credit->keep(LineItem::create(['narration' => $creditNarration]));

        $this->assertEquals($debitNarration, $debit->keepable->narration);
        $this->assertEquals($creditNarration, $credit->keepable->narration);
    }
}
