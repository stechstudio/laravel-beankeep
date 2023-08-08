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

    // TODO(zmd): get default period when no period passed in
    public function ledger(?iterable $period = null): Ledger
    {
        return new Ledger(
            account: $this,
            startingBalance: $this->openingBalance($period),
            ledgerEntries: $this->lineItems()->ledger($period)->get(),
        );
    }

    // TODO(zmd): get default period when no period passed in
    public function balance(?iterable $period = null): int
    {
        $balanceMethod = $this->debitPositive()
            ? 'debitPositiveBalance'
            : 'creditPositiveBalance';

        $debitSum = $this->lineItems()->ledger($period)->sum('debit');
        $creditSum = $this->lineItems()->ledger($period)->sum('credit');

        return Ledger::$balanceMethod(
            $this->openingBalance($period),
            $debitSum,
            $creditSum,
        );
    }

    // TODO(zmd): test me
    public function openingBalance(
        string|Carbon|CarbonImmutable|iterable $date,
    ): int {
        $balanceMethod = $this->debitPositive()
            ? 'debitPositiveBalance'
            : 'creditPositiveBalance';

        $debitSum = $this->lineItems()->priorTo($date)->sum('debit');
        $creditSum = $this->lineItems()->priorTo($date)->sum('credit');

        return Ledger::$balanceMethod(0, $debitSum, $creditSum);
    }

    // TODO(zmd): test me
    public function debitPositive(): bool
    {
        return match ($this->type) {
            AccountType::Asset => true,
            AccountType::Expense => true,
            default => false,
        };
    }

    // TODO(zmd): test me
    public function creditPositive(): bool
    {
        return !$this->isDebitPositive();
    }
}
