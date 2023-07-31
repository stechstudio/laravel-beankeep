<?php

declare(strict_types=1);

namespace STS\Beankeep\Database\Seeders;

use Carbon\CarbonPeriod;
use STS\Beankeep\Models\Account;
use STS\Beankeep\Models\Transaction;
use STS\Beankeep\Database\Factories\Support\RelativeDate;
use STS\Beankeep\Database\Factories\Support\RelativeTransactor;

class StaticSeeder extends Seeder
{
    protected readonly RelativeTransactor $lastYear;

    protected readonly RelativeTransactor $thisYear;

    protected readonly CarbonPeriod $lastYearRange;

    protected readonly CarbonPeriod $thisYearRange;

    public function __construct()
    {
        $date = new RelativeDate();

        $this->lastYear = new RelativeTransactor($date->lastYear);
        $this->thisYear = new RelativeTransactor($date->thisYear);

        $this->lastYearRange = $date->lastYear['1/1']->daysUntil(
            $date->lastYear['12/31'],
        );

        $this->thisYearRange = $date->thisYear['1/1']->daysUntil(
            $date->thisYear['12/31'],
        );
    }

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
        if (Transaction::whereBetween('date', $this->lastYearRange)->count() == 0) {
            // Scenario: Owners start business with initial capital contribution
            // TODO(zmd): implement below DSL for making this easier to manage
            $this->lastYear['1/1']->transact('initial owner contribution')
                ->line('cash', dr: 10000.00)
                ->line('capital', cr: 10000.00)
                ->doc('contribution-moa.pdf')
                ->post();

            // TODO(zmd): Scenario: We buy 2 computers from Computers-R-Us on credit for $5,000.00,
            /********************************************
             *                  |       Dr |       Cr   *
             *  ----------------+----------+----------  *
             *  Equipment       |   500000 |            *
             *    Accounts Pay. |          |   500000   *
             *                                          *
             ********************************************/

            // TODO(zmd): Scenario: We pay the Computers-R-Us invoice in full ($5,000.00)
            /********************************************
             *                  |       Dr |       Cr   *
             *  ----------------+----------+----------  *
             *  Accounts Payab. |   500000 |            *
             *    Cash          |          |   500000   *
             *                                          *
             ********************************************/
        }
    }

    protected function seedThisYearIfNeeded(): void
    {
        if (Transaction::whereBetween('date', $this->thisYearRange)->count() == 0) {
            // TODO(zmd): implement me
            echo "NOTHING FOR THIS YEAR, YET.\n";
        }
    }
}
