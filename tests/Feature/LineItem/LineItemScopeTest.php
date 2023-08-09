<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature\LineItem;

use STS\Beankeep\Models\LineItem;
use STS\Beankeep\Tests\TestCase;
use STS\Beankeep\Tests\TestSupport\Traits\HasDefaultTransactions;

final class LineItemScopeTest extends TestCase
{
    use HasDefaultTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->threeMonthsOfTransactions();
    }

    // -- ::scopeLedgerEntries() ----------------------------------------------

    public function testLedgerEntriesIncludesOnlyPostedEntriesForDefaultPeriod(): void
    {
        $this->assertEquals(10, LineItem::ledgerEntries()->count());
        $this->assertEquals(690500, LineItem::ledgerEntries()->sum('debit'));
        $this->assertEquals(690500, LineItem::ledgerEntries()->sum('credit'));
    }

    public function testLedgerEntriesIncludesOnlyPostedEntriesForConfiguredDefaultPeriod(): void
    {
        config(['beankeep.default-period' => ['1-dec', '30-nov']]);

        $this->assertEquals(12, LineItem::ledgerEntries()->count());
        $this->assertEquals(1690500, LineItem::ledgerEntries()->sum('debit'));
        $this->assertEquals(1690500, LineItem::ledgerEntries()->sum('credit'));
    }

    // TODO(zmd): public function testLedgerEntriesIncludesOnlyPostedEntriesForSpecifiedPeriod(): void {}

    // -- ::scopePeriod() -----------------------------------------------------

    // -- ::scopePriorTo() ----------------------------------------------------

    // -- ::scopePosted() -----------------------------------------------------

    // -- ::scopePending() ----------------------------------------------------

    // -- ::scopeDebits() -----------------------------------------------------

    // -- ::scopeCredits() ----------------------------------------------------
}
