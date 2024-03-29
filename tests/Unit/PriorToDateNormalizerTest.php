<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Unit;

use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\TestCase;
use STS\Beankeep\Support\PriorToDateNormalizer;

final class PriorToDateNormalizerTest extends TestCase
{
    public function testNormalizeReturnsTheGivenMutableCarbonDateJustAsItWasPassed(): void
    {
        $input = Carbon::now();

        $actual = PriorToDateNormalizer::normalize($input);

        $this->assertEquals($input, $actual);
        $this->assertInstanceOf(Carbon::class, $actual);
    }

    public function testNormalizeReturnsTheGivenImmutableCarbonDateJustAsItWasPassed(): void
    {
        $input = CarbonImmutable::now();

        $actual = PriorToDateNormalizer::normalize($input);

        $this->assertEquals($input, $actual);
        $this->assertInstanceOf(CarbonImmutable::class, $actual);
    }

    public function testNormalizeReturnsImmutableDateParsedFromGivenString(): void
    {
        $input = '1/2/2003';
        $expected = CarbonImmutable::parse($input);

        $actual = PriorToDateNormalizer::normalize($input);

        $this->assertEquals($expected, $actual);
        $this->assertInstanceOf(CarbonImmutable::class, $actual);
    }

    public function testNormalizeReturnsMutableStartDateOfGivenPeriod(): void
    {
        $startDate = Carbon::now();
        $endDate = Carbon::now()->addDays(5);
        $period = $startDate->daysUntil($endDate);

        $normalized = PriorToDateNormalizer::normalize($period);

        $this->assertEquals($startDate, $normalized);
        $this->assertInstanceOf(Carbon::class, $normalized);
    }

    public function testNormalizeReturnsImmutableStartDateOfGivenPeriod(): void
    {
        $startDate = CarbonImmutable::now();
        $endDate = CarbonImmutable::now()->addDays(5);
        $period = $startDate->daysUntil($endDate);

        $normalized = PriorToDateNormalizer::normalize($period);

        $this->assertEquals($startDate, $normalized);
        $this->assertInstanceOf(CarbonImmutable::class, $normalized);
    }
}
