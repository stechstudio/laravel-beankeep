<?php

declare(strict_types=1);

namespace STS\Beankeep\Database\Seeders;

use Carbon\CarbonPeriod;
use STS\Beankeep\Database\Factories\Support\HasRelativeTransactor;
use STS\Beankeep\Models\Account;
use STS\Beankeep\Models\Transaction;

class StaticSeeder extends Seeder
{
    use HasRelativeTransactor;

    public function run(): void
    {
        $this->seedAccountsIfNeeded();
        $this->seedLastYearIfNeeded();
        $this->seedThisYearIfNeeded();
    }

    protected function seedAccountsIfNeeded(): void
    {
        if (!Account::count()) {
            $this->call([AccountSeeder::class]);
        }
    }

    protected function seedLastYearIfNeeded(): void
    {
        if (Transaction::whereBetween(
            'date',
            $this->lastYearRange(),
        )->count() == 0) {
            $this->lastYear('1/1')
                ->transact('initial owner contribution')
                ->line('cash', dr: 10000.00)
                ->line('capital', cr: 10000.00)
                ->doc('contribution-moa.pdf')
                ->post();

            $this->lastYear('1/10')
                ->transact('2 computers from computers-r-us')
                ->line('equipment', dr: 5000.00)
                ->line('accounts-payable', cr: 5000.00)
                ->doc('computers-r-us-receipt.pdf')
                ->post();

            // TODO(zmd): finish me
            $this->lastYear('1/20')
                ->transact('register domain')
                ->draft();

            // TODO(zmd): finish me
            $this->lastYear('2/12')
                ->transact('provide technical consulting services (30 days payable)')
                ->draft();

            $this->lastYear('2/16')
                ->transact('ck no. 1337 - pay computers-r-us invoice')
                ->line('accounts-payable', dr: 5000.00)
                ->line('cash', cr: 5000.00)
                ->doc('ck-no-1337-scan.pdf')
                ->doc('computers-r-us-invoice-no-4242.pdf')
                ->post();

            // TODO(zmd): finish me
            $this->lastYear('2/26')
                ->transact('bill for 4 hours design services')
                ->draft();

            // TODO(zmd): finish me
            $this->lastYear('3/8')
                ->transact('bill for 12 hours development services')
                ->draft();

            // TODO(zmd): finish me
            $this->lastYear('3/1')
                ->transact('receive invoice for web hosting')
                ->draft();
        }
    }

    protected function seedThisYearIfNeeded(): void
    {
        if (Transaction::whereBetween(
            'date',
            $this->thisYearRange(),
        )->count() == 0) {
            // TODO(zmd): implement me
            echo "NOTHING FOR THIS YEAR, YET.\n";
        }
    }
}
