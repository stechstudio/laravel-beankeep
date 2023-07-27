<?php

declare(strict_types=1);

namespace STS\Beankeep\Database\Seeders;

use Carbon\CarbonPeriod;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Models\Account;
use STS\Beankeep\Models\LineItem;
use STS\Beankeep\Models\SourceDocument;
use STS\Beankeep\Models\Transaction;

class StaticSeeder extends Seeder
{
    protected $lastYear;

    protected $thisYear;

    protected CarbonPeriod $lastYearRange;

    protected CarbonPeriod $thisYearRange;

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
        if (Transaction::whereBetween('date', $this->lastYearRange())->count() == 0) {
            // TODO(zmd): compute a lookup table to get accounts symbolically
            // TODO(zmd): create DSL for making this easier to manage

            // Scenario: Owners start business with initial capital contribution
            $transaction = Transaction::factory()->create([
                'date' => $this->lastYear()->get('1/1'),
                'memo' => 'initial owner contribution',
            ]);

            $transaction->lineItems()->save(LineItem::factory()->make([
                'account_id' => Account::firstWhere(['number' => '1100'])->id,
                'debit' => 1000000,
            ]));

            $transaction->lineItems()->save(LineItem::factory()->make([
                'account_id' => Account::firstWhere(['number' => '3100'])->id,
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
        if (Transaction::whereBetween('date', $this->thisYearRange())->count() == 0) {
            // TODO(zmd): implement me
            echo "NOTHING FOR THIS YEAR, YET.\n";
        }
    }

    protected function lastYear()
    {
        // TODO(zmd): extract this prototype to something more usable and more
        //   reusable
        return $this->lastYear ??= (new class {
            private string $year;

            public function __construct()
            {
                $this->year = CarbonImmutable::now()->startOfYear()->subYear()->format('Y');
            }

            public function get(string $monthAndDay): CarbonImmutable
            {
                return CarbonImmutable::parse($monthAndDay . '/' . $this->year);
            }
        });
    }

    protected function thisYear(): CarbonImmutable
    {
        // TODO(zmd): extract this prototype to something more usable and more
        //   reusable
        return $this->thisYear ??= (new class {
            private string $year;

            public function __construct()
            {
                $this->year = CarbonImmutable::now()->startOfYear()->format('Y');
            }

            public function get(string $monthAndDay): CarbonImmutable
            {
                return CarbonImmutable::parse($monthAndDay . '/' . $this->year);
            }
        });
    }

    protected function lastYearRange(): CarbonPeriod
    {
        return $this->lastYearRange ??= (function () {
            $start = CarbonImmutable::now()->startOfYear()->subYear();
            $end = $start->endOfYear();

            return $start->daysUntil($end);
        })();
    }

    protected function thisYearRange(): CarbonPeriod
    {
        return $this->thisYearRange ??= (function () {
            $start = CarbonImmutable::now()->startOfYear();
            $end = $start->endOfYear();

            return $start->daysUntil($end);
        })();
    }
}
