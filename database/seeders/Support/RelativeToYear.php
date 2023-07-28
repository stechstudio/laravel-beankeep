<?php

declare(strict_types=1);

namespace STS\Beankeep\Database\Seeders\Support;

use ArrayAccess;
use Carbon\CarbonImmutable;

final class RelativeToYear implements ArrayAccess
{
    private string $year;

    public function __construct(CarbonImmutable $date)
    {
        $this->year = (string) $date->year;
    }

    public function offsetExists(mixed $offset): bool
    {
        return true;
    }

    public function offsetGet(mixed $offset): mixed
    {
        return CarbonImmutable::parse($offset . '/' . $this->year);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
    }

    public function offsetUnset(mixed $offset): void
    {
    }
}
