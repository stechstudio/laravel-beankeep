<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Unit;

use PHPUnit\Framework\TestCase;
use STS\Beankeep\Support\LineItemCollection;
use STS\Beankeep\Tests\TestSupport\Traits\CanMakeLineItems;

final class LineItemCollectionTest extends TestCase
{
    use CanMakeLineItems;

    private array $lineItems;

    public function setUp(): void
    {
        parent::setUp();

        $this->lineItems = [
            $this->debit(100.00),
            $this->credit(20.00),
            $this->credit(40.00),
            $this->debit(10.00),
            $this->credit(4.00),
            $this->debit(5.00),
            $this->credit(7.00),
            $this->debit(18.00),
        ];
    }

    // -- ::debits() ----------------------------------------------------------

    public function testDebitsReturnsJustTheDebits(): void
    {
        $lineItems = new LineItemCollection($this->lineItems);

        $debits = $lineItems->debits();

        $this->assertEquals(4, $debits->count());

        foreach ($debits as $debit) {
            $this->assertTrue($debit->isDebit());
        }
    }

    // TODO(zmd): public function testCreditsReturnsJustTheCredits(): void {}

    // TODO(zmd): public function testSumDebitsSumsTheDebitAmounts(): void {}

    // TODO(zmd): public function testSumCreditsSumsTheCreditAmounts(): void {}
}
