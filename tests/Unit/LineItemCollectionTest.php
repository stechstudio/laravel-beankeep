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

    public function testCreditsReturnsJustTheCredits(): void
    {
        $lineItems = new LineItemCollection($this->lineItems);

        $credits = $lineItems->credits();

        $this->assertEquals(4, $credits->count());

        foreach ($credits as $credit) {
            $this->assertTrue($credit->isCredit());
        }
    }

    public function testSumDebitsSumsTheDebitAmounts(): void
    {
        $lineItems = new LineItemCollection($this->lineItems);

        $this->assertEquals(
            $this->floatToInt(100.00 + 10.00 + 5.00 + 18.00),
            $lineItems->sumDebits(),
        );
    }

    public function testSumCreditsSumsTheCreditAmounts(): void
    {
        $lineItems = new LineItemCollection($this->lineItems);

        $this->assertEquals(
            $this->floatToInt(20.00 + 40.00 + 4.00 + 7.00),
            $lineItems->sumCredits(),
        );
    }
}
