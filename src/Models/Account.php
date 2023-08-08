<?php

declare(strict_types=1);

namespace STS\Beankeep\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use STS\Beankeep\Database\Factories\AccountFactory;
use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Support\Ledger;
use STS\Beankeep\Support\LedgerCollection;

final class Account extends Beankeeper
{
    use HasFactory;

    protected $table = 'beankeep_accounts';

    protected $fillable = [
        'type',
        'number',
        'name',
    ];

    protected $casts = [
        'type' => AccountType::class,
    ];

    protected static function newFactory()
    {
        return AccountFactory::new();
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(LineItem::class);
    }

    public function ledger(?iterable $period = null): LedgerCollection
    {
        return $this->lineItems()->ledger()->get();
    }

    /*
    public function ledger(?iterable $period = null): Ledger
    {
        // TODO(zmd): get default period when no period passed in
        return new Ledger(
            account: $this,
            startingBalance: $this->openingBalance($period),
            ledgerEntries: $this->lineItems()->ledger($period)->get(),
        );
    }

    // TODO(zmd): test me
    public function openingBalance(string|Carbon|CarbonImmutable|iterable $date): int
    {
        return (new Ledger(
            account: $this,
            startingBalance: 0,
            ledgerEntries: $this->lineItems()->priorTo($date)->get(),
        ))->balance();
    }
    */
}
