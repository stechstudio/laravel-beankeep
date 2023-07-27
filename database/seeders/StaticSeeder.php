<?php

declare(strict_types=1);

namespace STS\Beankeep\Database\Seeders;

use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Models\Account;
use STS\Beankeep\Models\LineItem;
use STS\Beankeep\Models\SourceDocument;
use STS\Beankeep\Models\Transaction;

class StaticSeeder extends Seeder
{
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
            // TODO(zmd): implement me
            echo "NOTHING FOR LAST YEAR, YET.\n";
        }
    }

    protected function seedThisYearIfNeeded(): void
    {
        if (Transaction::whereBetween('date', $this->thisYearRange())->count() == 0) {
            // TODO(zmd): implement me
            echo "NOTHING FOR THIS YEAR, YET.\n";
        }
    }

    protected function lastYearRange(): CarbonPeriod
    {
        return $this->lastYearRange ??= (function () {
            $start = Carbon::now()->startOfYear()->subYear();
            $end = $start->copy()->endOfYear();

            return $start->daysUntil($end);
        })();
    }

    protected function thisYearRange(): CarbonPeriod
    {
        return $this->thisYearRange ??= (function () {
            $start = Carbon::now()->startOfYear();
            $end = $start->copy()->endOfYear();

            return $start->daysUntil($end);
        })();
    }
}
