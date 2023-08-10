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

    // -- ::balance() ---------------------------------------------------------

    public function testItCanReportDebitPositiveBalanceForGivenPeriod(): void
    {
        $this->assertEquals(998500, $this->account('cash')->balance($this->janPeriod()));
    }

    // TODO(zmd): public function testItCanReportDebitPositiveBalanceForDefaultPeriod(): void {}

    // TODO(zmd): public function testItCanReportDebitPositiveBalanceForConfiguredDefaultPeriod(): void {}

    public function testThatItCorrectlyExcludesNonPostedTransactionsInDebitPositiveBalanceCalculations(): void
    {
        $this->lastYear('12/27')
            ->transact('buy office supplies')
            ->line('supplies-expense', dr: 50.00)
            ->line('cash', cr: 50.00)
            ->doc('office-smacks-receipt.pdf')
            ->draft();

        $this->assertEquals(998500, $this->account('cash')->balance($this->janPeriod()));
    }

    public function testItCanReportCreditPositiveBalanceForGivenPeriod(): void
    {
        $this->assertEquals(500000, $this->account('accounts-payable')->balance($this->janPeriod()));
    }

    // TODO(zmd): public function testItCanReportCreditPositiveBalanceForDefaultPeriod(): void {}

    // TODO(zmd): public function testItCanReportCreditPositiveBalanceForConfiguredDefaultPeriod(): void {}

    public function testThatItCorrectlyExcludesNonPostedTransactionsInCreditPositiveBalanceCalculations(): void
    {
        $this->lastYear('12/28')
            ->transact('1 optical mouse from computers-ᴙ-us')
            ->line('equipment', dr: 25.00)
            ->line('accounts-payable', cr: 25.00)
            ->doc('computers-ᴙ-us-receipt.pdf')
            ->draft();

        $this->assertEquals(500000, $this->account('accounts-payable')->balance($this->janPeriod()));
    }

    // -- ::openingBalance() --------------------------------------------------

    // TODO(zmd): public function testThatItCorrectlyReportsDebitPositiveOpeningBalanceForGivenPeriod(): void {}

    // TODO(zmd): public function testThatItCorrectlyReportsDebitPositiveOpeningBalanceForDefaultPeriod(): void {}

    // TODO(zmd): public function testThatItCorrectlyReportsDebitPositiveOpeningBalanceForConfiguredDefaultPeriod(): void {}

    public function testThatItCorrectlyExcludesNonPostedTransactionsInDebitPositiveOpeningBalanceCalculations(): void
    {
        $this->lastYear('12/27')
            ->transact('buy office supplies')
            ->line('supplies-expense', dr: 50.00)
            ->line('cash', cr: 50.00)
            ->doc('office-smacks-receipt.pdf')
            ->draft();

        $this->assertEquals(998500, $this->account('cash')->openingBalance($this->febPeriod()));
    }

    // TODO(zmd): public function testThatItCorrectlyReportsCreditPositiveOpeningBalanceForGivenPeriod(): void {}

    // TODO(zmd): public function testThatItCorrectlyReportsCreditPositiveOpeningBalanceForDefaultPeriod(): void {}

    // TODO(zmd): public function testThatItCorrectlyReportsCreditPositiveOpeningBalanceForConfiguredDefaultPeriod(): void {}

    public function testThatItCorrectlyExcludesNonPostedTransactionsInCreditPositiveOpeningBalanceCalculations(): void
    {
        $this->lastYear('12/28')
            ->transact('1 optical mouse from computers-ᴙ-us')
            ->line('equipment', dr: 25.00)
            ->line('accounts-payable', cr: 25.00)
            ->doc('computers-ᴙ-us-receipt.pdf')
            ->draft();

        $this->assertEquals(500000, $this->account('accounts-payable')->balance($this->febPeriod()));
    }
}
