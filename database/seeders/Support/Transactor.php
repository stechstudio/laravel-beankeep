<?php

declare(strict_types=1);

namespace STS\Beankeep\Database\Seeders\Support;

use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
use STS\Beankeep\Models\Transaction;

final class Transactor implements ArrayAccess
{
    public function __construct(
        private Carbon|CarbonImmutable $date,
        private AccountLookup $accounts,
    ) {
    }

    public function transact(string $memo): self
    {
        return $this->memo($memo);
    }

    public function memo(string $memo): self
    {
        // TODO(zmd): implement me
        return $this;
    }

    public function line(
        string $accountKey,
        float $dr = 0.0,
        float $cr = 0.0,
    ): self {
        // TODO(zmd): implement me
        return $this;
    }

    public function doc(
        string $filename,
        ?string $mime = null,
        ?string $attachment = null,
    ): self {
        // TODO(zmd): implement me
        return $this;
    }

    public function post(): Transaction
    {
        // TODO(zmd): implement me
    }

    public function draft(): Transaction
    {
        // TODO(zmd): implement me
    }
}
