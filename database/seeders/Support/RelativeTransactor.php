<?php

declare(strict_types=1);

namespace STS\Beankeep\Database\Seeders\Support;

use ArrayAccess;

/**
 * Relative to dates within a given year, build transactions (with line items
 * and source documents) to (maybe) post to accounts.
 */
final class RelativeTransactor implements ArrayAccess
{
    public function __construct(
        private RelativeToYear $dates,
        private AccountLookup $accounts,
    ) {
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->dates->offsetExists($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return new Transactor(
            date: $this->dates->offsetGet($offset),
            accounts: $this->accounts,
        );
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
    }

    public function offsetUnset(mixed $offset): void
    {
    }
}
