<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature\Account;

use STS\Beankeep\Tests\TestCase;
use STS\Beankeep\Tests\TestSupport\Traits\HasDefaultTransactions;

final class BalanceTest extends TestCase
{
    use HasDefaultTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->threeMonthsOfTransactions();
    }

    public function testItCanReportDebitPositiveBalanceForGivenPeriod(): void
    {
        $this->assertEquals(
            998500,
            $this->account('cash')->balance($this->janPeriod()),
        );
    }

    public function testThatItCorrectlyExcludesNonPostedTransactionsInDebitPositiveBalanceCalculations(): void
    {
        $this->lastYear('12/27')
            ->transact('buy office supplies')
            ->line('supplies-expense', dr: 50.00)
            ->line('cash', cr: 50.00)
            ->doc('office-smacks-receipt.pdf')
            ->draft();

        $this->assertEquals(
            998500,
            $this->account('cash')->balance($this->janPeriod()),
        );
    }

    public function testItCanReportCreditPositiveBalanceForGivenPeriod(): void
    {
        $this->assertEquals(
            500000,
            $this->account('accounts-payable')->balance($this->janPeriod()),
        );
    }

    /*
    public function testThatItCorrectlyExcludesNonPostedTransactionsInCreditPositiveBalanceCalculations(): void
    {
        // TODO(zmd): implement me
    }
    */
}
