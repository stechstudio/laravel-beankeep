<?php

declare(strict_types=1);

namespace STS\Beankeep\Database\Factories\Support;

use ArrayAccess;

final class RelativeTransactor implements ArrayAccess
{
    public function __construct(
        private RelativeToYear $dates,
    ) {
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->dates->offsetExists($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return new Transactor($this->dates->offsetGet($offset));
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
    }

    public function offsetUnset(mixed $offset): void
    {
    }
}
