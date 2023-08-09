<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature\LineItem;

use Illuminate\Support\Carbon;
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
        $this->travelTo($this->thisYear('5/4'));
        config(['beankeep.default-period' => ['1-dec', '30-nov']]);

        $this->assertEquals(12, LineItem::ledgerEntries()->count());
        $this->assertEquals(1690500, LineItem::ledgerEntries()->sum('debit'));
        $this->assertEquals(1690500, LineItem::ledgerEntries()->sum('credit'));
    }

    public function testLedgerEntriesIncludesOnlyPostedEntriesForSpecifiedPeriod(): void
    {
        $this->assertEquals(4, LineItem::ledgerEntries($this->febPeriod())->count());
        $this->assertEquals(69000, LineItem::ledgerEntries($this->febPeriod())->sum('debit'));
        $this->assertEquals(69000, LineItem::ledgerEntries($this->febPeriod())->sum('credit'));
    }

    // -- ::scopePeriod() -----------------------------------------------------

    public function testPeriodIncludesAllItemsForDefaultPeriod(): void
    {
        $this->assertEquals(14, LineItem::period()->count());
        $this->assertEquals(1238500, LineItem::period()->sum('debit'));
        $this->assertEquals(1238500, LineItem::period()->sum('credit'));
    }

    public function testPeriodIncludesAllItemsForConfiguredDefaultPeriod(): void
    {
        $this->travelTo($this->thisYear('5/4'));
        config(['beankeep.default-period' => ['1-dec', '30-nov']]);

        $this->assertEquals(16, LineItem::period()->count());
        $this->assertEquals(2238500, LineItem::period()->sum('debit'));
        $this->assertEquals(2238500, LineItem::period()->sum('credit'));
    }

    public function testPeriodIncludesAllItemsForSpecifiedPeriod(): void
    {
        $this->assertEquals(8, LineItem::period($this->febPeriod())->count());
        $this->assertEquals(617000, LineItem::period($this->febPeriod())->sum('debit'));
        $this->assertEquals(617000, LineItem::period($this->febPeriod())->sum('credit'));
    }

    // -- ::scopePriorTo() ----------------------------------------------------

    // TODO(zmd): public function testPriorToWithDatePassedAsString(): void {}

    // TODO(zmd): public function testPriorToWithDatePassedAsCarbon(): void {}

    // TODO(zmd): public function testPriorToWithDatePassedAsCarbonImmutable(): void {}

    // TODO(zmd): public function testPriorToWithDatePassedAsCarbonPeriod(): void {}

    // -- ::scopePosted() -----------------------------------------------------

    // -- ::scopePending() ----------------------------------------------------

    // -- ::scopeDebits() -----------------------------------------------------

    // -- ::scopeCredits() ----------------------------------------------------
}
