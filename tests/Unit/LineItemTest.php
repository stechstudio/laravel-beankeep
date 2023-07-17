<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use STS\Beankeep\Models\LineItem;

final class LineItemTest extends TestCase
{
    public function testItCanCorrectlyDetermineWhenItIsADebit(): void
    {
        $lineItem = new LineItem(['debit' => 100, 'credit' => 0]);

        $this->assertTrue($lineItem->isDebit());
    }

    public function testItCanCorrectlyDetermineWhenItIsNotADebit(): void
    {
        $lineItem = new LineItem(['debit' => 0, 'credit' => 100]);

        $this->assertFalse($lineItem->isDebit());
    }

    public function testItCanCorrectlyDetermineWhenItIsACredit(): void
    {
        $lineItem = new LineItem(['debit' => 0, 'credit' => 100]);

        $this->assertTrue($lineItem->isCredit());
    }

    public function testItCanCorrectlyDetermineWhenItIsNotACredit(): void
    {
        $lineItem = new LineItem(['debit' => 100, 'credit' => 0]);

        $this->assertFalse($lineItem->isCredit());
    }

    #[DataProvider('centToDollarAmountsProducer')]
    public function testItCanConvertDebitAmountFromCentsToDollars(
        int $amountInCents,
        float $amountInDollars,
    ): void {
        $lineItem = new LineItem(['debit' => $amountInCents, 'credit' => 0]);

        $this->assertEquals($amountInDollars, $lineItem->debitInDollars());
    }

    #[DataProvider('centToDollarAmountsProducer')]
    public function testItCanConvertCreditAmountFromCentsToDollars(
        int $amountInCents,
        float $amountInDollars,
    ): void {
        $lineItem = new LineItem(['debit' => 0, 'credit' => $amountInCents]);

        $this->assertEquals($amountInDollars, $lineItem->creditInDollars());
    }

    // ------------------------------------------------------------------------

    public static function centToDollarAmountsProducer(): array
    {
        return [
            [100, 1.0],
            [133742, 1337.42],
            [0, 0.0],
        ];
    }
}
