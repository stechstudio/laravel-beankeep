<?php

declare(strict_types=1);

namespace STS\Beankeep\Models;

use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use STS\Beankeep\Database\Factories\AccountFactory;
use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Enums\JournalPeriod;
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

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(LineItem::class);
    }

    public function ledger(CarbonPeriod $period = null): Ledger
    {
        return new Ledger(
            account: $this,
            startingBalance: $this->openingBalance($period),
            ledgerEntries: $this->lineItems()->ledgerEntries($period)->get(),
        );
    }

    public function balance(CarbonPeriod $period = null): int
    {
        $debitSum = $this->lineItems()->ledgerEntries($period)->sum('debit');
        $creditSum = $this->lineItems()->ledgerEntries($period)->sum('credit');

        return Ledger::computeBalance(
            $this,
            $this->openingBalance($period),
            $debitSum,
            $creditSum,
        );
    }

    public function openingBalance(CarbonPeriod $period = null): int
    {
        $debitSum = $this->lineItems()
            ->ledgerEntries(priorTo: $period)
            ->sum('debit');

        $creditSum = $this->lineItems()
            ->ledgerEntries(priorTo: $period)
            ->sum('credit');

        return Ledger::computeBalance($this, 0, $debitSum, $creditSum);
    }

    public function debitPositive(): bool
    {
        return $this->type->debitPositive();
    }

    public function creditPositive(): bool
    {
        return $this->type->creditPositive();
    }
}
