<?php

declare(strict_types=1);

namespace STS\Beankeep\Database\Seeders;

use Carbon\CarbonPeriod;
use Illuminate\Support\Str;
use STS\Beankeep\Models\Account;
use STS\Beankeep\Models\LineItem;
use STS\Beankeep\Models\SourceDocument;
use STS\Beankeep\Models\Transaction;
use STS\Beankeep\Database\Seeders\Support\RelativeDate;
use STS\Beankeep\Database\Seeders\Support\RelativeToYear;
use STS\Beankeep\Database\Seeders\Support\AccountLookup;

class StaticSeeder extends Seeder
{
    protected readonly RelativeToYear $lastYear;

    protected readonly RelativeToYear $thisYear;

    protected readonly CarbonPeriod $lastYearRange;

    protected readonly CarbonPeriod $thisYearRange;

    protected readonly AccountLookup $accounts;

    public function __construct()
    {
        $date = new RelativeDate();

        $this->lastYear = $date->lastYear;
        $this->thisYear = $date->thisYear;

        $this->lastYearRange = $date->lastYear['1/1']->daysUntil(
            $date->lastYear['12/31'],
        );

        $this->thisYearRange = $date->thisYear['1/1']->daysUntil(
            $date->thisYear['12/31'],
        );

        $this->accounts = new AccountLookup();
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
            $this->accounts->refresh();
        }
    }

    protected function seedLastYearIfNeeded(): void
    {
        if (Transaction::whereBetween('date', $this->lastYearRange)->count() == 0) {
            // TODO(zmd): create DSL for making this easier to manage

            // Scenario: Owners start business with initial capital contribution
            $transaction = Transaction::factory()->create([
                'date' => $this->lastYear['1/1'],
                'memo' => 'initial owner contribution',
            ]);

            $transaction->lineItems()->save(LineItem::factory()->make([
                'account_id' => $this->accounts['cash']->id,
                'debit' => 1000000,
            ]));

            $transaction->lineItems()->save(LineItem::factory()->make([
                'account_id' => $this->accounts['capital']->id,
                'credit' => 1000000,
            ]));

            $transaction->sourceDocuments()->save(SourceDocument::factory()->make([
                'attachment' => Str::uuid()->toString(),
                'filename' => 'contribution-moa.pdf',
                'mime_type' => 'application/pdf',
            ]));

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
