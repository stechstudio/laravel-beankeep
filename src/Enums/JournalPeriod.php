<?php

declare(strict_types=1);

namespace STS\Beankeep\Enums;

use Carbon\CarbonPeriod;
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

    // TODO(zmd): bring in the static methods from BeankeepPeriod

    public function toCarbonPeriod(): CarbonPeriod
    {
        // TODO(zmd): test & implement me
    }

    public function expanded(): string
    {
        return match ($this) {
            self::Jan => 'January',
            self::Feb => 'February',
            // March, April, May, June and July are never abbreviated in text
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

    // TODO(zmd): test me
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
}
