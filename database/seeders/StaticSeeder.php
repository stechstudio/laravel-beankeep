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
        // TODO(zmd): implement me
        dump([
            'last year start' => (string) $this->lastYearRange()->startDate,
            'last year end' => (string) $this->lastYearRange()->endDate,
        ]);
    }

    protected function seedThisYearIfNeeded(): void
    {
        // TODO(zmd): implement me
        dump([
            'this year start' => (string) $this->thisYearRange()->startDate,
            'this year end' => (string) $this->thisYearRange()->endDate,
        ]);
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
