<?php

declare(strict_types=1);

namespace STS\Beankeep\Database\Seeders\Support;

use ArrayAccess;
use STS\Beankeep\Models\Account;

final class AccountLookup implements ArrayAccess
{
    private readonly array $accounts;

    public function __construct()
    {
        $this->accounts = self::lookupTable();
    }

    public static function lookupTable(): array
    {
        // TODO(zmd): implement me
    }

    public function offsetExists(mixed $offset): bool
    {
        // TODO(zmd): implement me
    }

    public function offsetGet(mixed $offset): mixed
    {
        // TODO(zmd): implement me
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
    }

    public function offsetUnset(mixed $offset): void
    {
    }
}
