<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature;

use STS\Beankeep\Models\LineItem;
use STS\Beankeep\Tests\TestCase;
use STS\Beankeep\Tests\TestSupport\Traits\BeanConstructors;

final class GeneralLedgerTest extends TestCase
{
    use BeanConstructors;

    public function testItCanModelAChartOfAccounts(): void
    {
        $accounts = array_values($this->createAccounts());

        foreach ($this->accountAttributes() as $index => [$number, $name, $type]) {
            $this->assertEquals($number, $accounts[$index]->number);
            $this->assertEquals($type, $accounts[$index]->type);
            $this->assertEquals($name, $accounts[$index]->name);
        }
    }

    public function testItCanRecordATransactionToTheJournal(): void
    {
        $accounts = $this->createAccounts();

        $transaction = $this->transaction(
            'initial owner contribution',
            '2022-01-01',
        );

        $debit = $this->debit($accounts['cash'], $transaction, 1000000);
        $credit = $this->credit($accounts['capital'], $transaction, 1000000);
        $sourceDoc = $this->doc($transaction, 'contribution-moa.pdf');

        $transaction->refresh();

        $this->assertEquals('initial owner contribution', $transaction->memo);
        $this->assertEquals($this->date('2022-01-01'), $transaction->date);
        $this->assertFalse($transaction->posted);

        $this->assertEquals(2, $transaction->lineItems()->count());
        $this->assertEquals(1000000, $transaction->lineItems[0]->debit);
        $this->assertEquals($accounts['cash'], $transaction->lineItems[0]->account);
        $this->assertEquals(1000000, $transaction->lineItems[1]->credit);
        $this->assertEquals($accounts['capital'], $transaction->lineItems[1]->account);

        $this->assertEquals(1, $transaction->sourceDocuments()->count());
        $this->assertEquals(
            $sourceDoc->attachment,
            $transaction->sourceDocuments->first()->attachment,
        );
        $this->assertEquals(
            $sourceDoc->filename,
            $transaction->sourceDocuments->first()->filename,
        );
        $this->assertEquals(
            $sourceDoc->mime_type,
            $transaction->sourceDocuments->first()->mime_type,
        );
    }

    public function testItCanModelAJournalWithManyTransactions(): void
    {
        $transact = $this->simpleTransactor();

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
        $transact('2022-01-01', 'initial owner contribution', 10000.00, dr: 'cash', cr: 'capital');
        $transact('2022-10-15', '2 computers from computers-r-us', 5000.00, dr: 'equipment', cr: 'accounts-payable');
        $transact('2022-10-16', 'ck no. 1337', 5000.00, dr: 'accounts-payable', cr: 'cash');

        // NOTE(zmd): later we'll *also* check individual account balances here,
        //   once we have created helpers for doing such in the package.
        $this->assertEquals(0, LineItem::sum('debit') - LineItem::sum('credit'));
    }
}
