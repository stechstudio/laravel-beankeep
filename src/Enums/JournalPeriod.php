<?php

declare(strict_types=1);

namespace STS\Beankeep\Enums;

use Carbon\CarbonPeriod;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;

enum JournalPeriod: int
{
    case Jan = 1;
    case Feb = 2;
    case March = 3;
    case April = 4;
    case May = 5;
    case June = 6;
    case July = 7;
    case Aug = 8;
    case Sept = 9;
    case Oct = 10;
    case Nov = 11;
    case Dec = 12;

    // TODO(zmd): test me (may be unit test)
    public function toCarbonPeriod(): CarbonPeriod
    {
        $now = CarbonImmutable::now();

        $startDate = $now
            ->setMonth($this->value)
            ->startOfMonth();

        if ($startDate->greaterThan($now)) {
            $startDate = $startDate->subYear();
        }

        $endDate = $startDate->addMonths(11)->endOfMonth();

        return $startDate->daysUntil($endDate);
    }

    // TODO(zmd): test me (may be unit test)
    public function expanded(): string
    {
        // Note: March, April, May, June and July are never abbreviated in text
        return match ($this) {
            self::Jan => 'January',
            self::Feb => 'February',
            self::March => 'March',
            self::April => 'April',
            self::May => 'May',
            self::June => 'June',
            self::July => 'July',
            self::Aug => 'August',
            self::Sept => 'September',
            self::Oct => 'October',
            self::Nov => 'November',
            self::Dec => 'December',
        };
    }

    // TODO(zmd): test me (may be unit test)
    public static function fromString(string $value): static
    {
        return match(Str::lower($value)) {
            'jan', 'january'           => static::Jan,
            'feb', 'february'          => static::Feb,
            'mar', 'march'             => static::March,
            'apr', 'april'             => static::April,
            'may'                      => static::May,
            'jun', 'june'              => static::June,
            'jul', 'july'              => static::July,
            'aug', 'august'            => static::Aug,
            'sep', 'sept', 'september' => static::Sept,
            'oct', 'october'           => static::Oct,
            'nov', 'november'          => static::Nov,
            'dec', 'december'          => static::Dec,
        };
    }

    // TODO(zmd): test me (must be feature test)
    public static function get(?CarbonPeriod $period): CarbonPeriod
    {
        if (is_null($period)) {
            return static::defaultPeriod();
        }

        return $period;
    }

    // TODO(zmd): test me (must be feature test)
    public static function defaultPeriod(): CarbonPeriod
    {
        if (config('beankeep.default-period')) {
            return static::defaultPeriodFromConfig();
        }

        return static::defaultPeriodThisYear();
    }

    private static function defaultPeriodFromConfig(): CarbonPeriod {
        $startMonthStr = config('beankeep.default-period');

        $journalPeriod = JournalPeriod::fromString(
            config('beankeep.default-period'),
        );

        return $journalPeriod->toCarbonPeriod();
    }

    private static function defaultPeriodThisYear(): CarbonPeriod
    {
        $startOfYear = CarbonImmutable::now()->startOfYear();
        $endOfYear = CarbonImmutable::now()->endOfYear();

        return $startOfYear->daysUntil($endOfYear);
    }
}
