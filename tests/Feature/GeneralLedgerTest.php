<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature;

use STS\Beankeep\Database\Factories\Support\HasRelativeTransactor;
use STS\Beankeep\Models\LineItem;
use STS\Beankeep\Tests\TestCase;
use STS\Beankeep\Tests\TestSupport\Traits\CanCreateAccounts;

final class GeneralLedgerTest extends TestCase
{
    use CanCreateAccounts;
    use HasRelativeTransactor;

    public function setUp(): void
    {
        parent::setUp();
        $this->createAccounts();
    }

    public function testItCanRecordATransactionToTheJournal(): void
    {
        $transaction = $this->thisYear('1/1')->transact('initial owner contribution')
            ->line('cash', dr: 10000.00)
            ->line('capital', cr: 10000.00)
            ->doc('contribution-moa.pdf')
            ->draft();

        $this->assertEquals('initial owner contribution', $transaction->memo);
        $this->assertEquals($this->getDate(thisYear: '1/1'), $transaction->date);
        $this->assertFalse($transaction->posted);

        $this->assertEquals(2, $transaction->lineItems()->count());
        $this->assertEquals(1000000, $transaction->lineItems[0]->debit);
        $this->assertEquals($this->account('cash'), $transaction->lineItems[0]->account);
        $this->assertEquals(1000000, $transaction->lineItems[1]->credit);
        $this->assertEquals($this->account('capital'), $transaction->lineItems[1]->account);

        $this->assertEquals(1, $transaction->sourceDocuments()->count());
    }

    public function testItCanModelAJournalWithManyTransactions(): void
    {
        //
        //      Date | Account            |        Dr |        Cr | Memo
        // ==========+====================+===========+===========+======================
        //  01/01/22 | Cash               |  10000.00 |           | initial owner con...
        //           |   Capital          |           |  10000.00 |
        // ----------+--------------------+-----------+-----------+----------------------
        //  10/15/22 | Equipment          |   5000.00 |           | 2 computers from ...
        //           |   Accounts Payable |           |   5000.00 |
        // ----------+--------------------+-----------+-----------+----------------------
        //  10/16/22 | Accounts Payable   |   5000.00 |           | ck no. 1337
        //           |   Cash             |           |   5000.00 |
        // ==========+====================+===========+===========+======================
        //           | TOTAL (Dr)         |  20000.00 |           |
        //           |   TOTAL (Cr)       |           |  20000.00 |
        //
        $this->thisYear('1/1')->transact('initial owner contribution')
            ->line('cash', dr: 10000.00)
            ->line('capital', cr: 10000.00)
            ->post();

        $this->thisYear('10/15')->transact('2 computers from computers-r-us')
            ->line('equipment', dr: 5000.00)
            ->line('accounts-payable', cr: 5000.00)
            ->post();

        $this->thisYear('10/16')->transact('ck no. 1337')
            ->line('accounts-payable', dr: 5000.00)
            ->line('cash', cr: 5000.00)
            ->post();

        // NOTE(zmd): later we'll *also* check individual account balances here,
        //   once we have created helpers for doing such in the package.
        $this->assertEquals(0, LineItem::sum('debit') - LineItem::sum('credit'));
    }
}
