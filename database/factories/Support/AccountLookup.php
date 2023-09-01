<?php

declare(strict_types=1);

namespace STS\Beankeep\Database\Factories\Support;

use ArrayAccess;
use Illuminate\Support\Str;
use STS\Beankeep\Models\Account;
use STS\Beankeep\Models\Journal;

final class AccountLookup implements ArrayAccess
{
    private array $accounts;

    public function __construct()
    {
        $this->refresh();
    }

    public function refresh(?Journal $journal = null): void
    {
        $this->accounts = self::lookupTable($journal);
    }

    public static function lookupTable(?Journal $journal = null): array
    {
        if (!$journal) {
            $journal = Journal::find(1);
        }

        return Account::all()
            ->mapWithKeys(fn (Account $account) =>
                [Str::kebab($account->name) => $account])
            ->all();
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->accounts[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->accounts[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
    }

    public function offsetUnset(mixed $offset): void
    {
    }
}
