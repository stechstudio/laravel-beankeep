<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature;

use Illuminate\Support\Carbon;
use STS\Beankeep\Database\Factories\Support\HasRelativeTransactor;
use STS\Beankeep\Database\Seeders\AccountLookup;
use STS\Beankeep\Database\Seeders\AccountSeeder;
use STS\Beankeep\Models\LineItem;
use STS\Beankeep\Tests\TestCase;
use STS\Beankeep\Tests\TestSupport\Traits\BeanConstructors;

final class GeneralLedgerTest extends TestCase
{
    use BeanConstructors;
    use HasRelativeTransactor;

    public function testItCanModelAChartOfAccounts(): void
    {
        // TODO(zmd): come back to this one, still need BeanConstructors for
        //   this?
        $accounts = array_values($this->createAccounts());

        foreach (AccountSeeder::accountsAttributes() as $index => $attributes) {
            $this->assertEquals($attributes['number'], $accounts[$index]->number);
            $this->assertEquals($attributes['type'], $accounts[$index]->type);
            $this->assertEquals($attributes['name'], $accounts[$index]->name);
        }
    }

    public function testItCanRecordATransactionToTheJournal(): void
    {
        // TODO(zmd): revisit this $accounts array; do we want to do this in
        //   setUp?
        $accounts = $this->createAccounts();

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
        $this->assertEquals($accounts['cash'], $transaction->lineItems[0]->account);
        $this->assertEquals(1000000, $transaction->lineItems[1]->credit);
        $this->assertEquals($accounts['capital'], $transaction->lineItems[1]->account);

        $this->assertEquals(1, $transaction->sourceDocuments()->count());
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
