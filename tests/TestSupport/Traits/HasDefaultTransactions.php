<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\TestSupport\Traits;

use Carbon\CarbonPeriod;
use STS\Beankeep\Database\Factories\Support\HasRelativeTransactor;
use STS\Beankeep\Models\Account;

trait HasDefaultTransactions
{
    use CanCreateAccounts;
    use HasRelativeTransactor;

    protected function janPeriod(): CarbonPeriod
    {
        $start = $this->getDate(thisYear: '1/1');
        $end = $start->endOfMonth();

        return $start->daysUntil($end);
    }

    protected function febPeriod(): CarbonPeriod
    {
        $start = $this->getDate(thisYear: '2/1');
        $end = $start->endOfMonth();

        return $start->dayUntil($end);
    }

    protected function threeMonthsOfTransactions(): void
    {
        $this->createAccountsIfMissing();

        $this->lastYear('12/25')
            ->transact('initial owner contribution')
            ->line('cash', dr: 10000.00)
            ->line('capital', cr: 10000.00)
            ->doc('contribution-moa.pdf')
            ->post();

        $this->thisYear('1/5')
            ->transact('develpment services')
            ->line('accounts-receivable', dr: 1200.00)
            ->line('services-revenue', cr: 1200.00)
            ->doc("invoice-99.pdf")
            ->post();

        $this->thisYear('1/10')
            ->transact('register domain')
            ->line('cost-of-services', dr: 15.00)
            ->line('cash', cr: 15.00)
            ->doc('namecheap-receipt.pdf')
            ->post();

        $this->thisYear('1/20')
            ->transact('2 computers from computers-ᴙ-us')
            ->line('equipment', dr: 5000.00)
            ->line('accounts-payable', cr: 5000.00)
            ->doc('computers-ᴙ-us-receipt.pdf')
            ->post();

        $this->thisYear('2/1')
            ->transact("pay office space rent - feb")
            ->line('rent-expense', dr: 450.00)
            ->line('cash', cr: 450.00)
            ->doc("ck-no-1337-scan.pdf")
            ->post();

        $this->thisYear('2/12')
            ->transact('technical consulting services')
            ->line('accounts-receivable', dr: 240.00)
            ->line('services-revenue', cr: 240.00)
            ->doc("invoice-100.pdf")
            ->post();

        $this->thisYear('2/16')
            ->transact('ck no. 1338 - pay computers-ᴙ-us invoice')
            ->line('accounts-payable', dr: 5000.00)
            ->line('cash', cr: 5000.00)
            ->doc('ck-no-1338-scan.pdf')
            ->doc('computers-ᴙ-us-invoice-no-42.pdf')
            ->draft();

        $this->thisYear('2/26')
            ->transact('design services')
            ->line('accounts-receivable', dr: 480.00)
            ->line('services-revenue', cr: 480.00)
            ->doc("invoice-101.pdf")
            ->draft();
    }

    protected function unpostedTransactionLastYear(): void
    {
        $this->createAccountsIfMissing();

        $this->lastYear('12/27')
            ->transact('buy office supplies')
            ->line('cash', dr: 50.00)
            ->line('supplies-expense', cr: 50.00)
            ->doc('office-smacks-receipt.pdf')
            ->draft();
    }

    protected function createAccountsIfMissing(): void
    {
        if (!Account::count()) {
            $this->createAccounts();
        }
    }
}
