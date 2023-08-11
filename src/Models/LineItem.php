<?php

declare(strict_types=1);

namespace STS\Beankeep\Models;

use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use STS\Beankeep\Database\Factories\LineItemFactory;
use STS\Beankeep\Support\BeankeepPeriod;
use STS\Beankeep\Support\LineItemCollection;
use STS\Beankeep\Support\PriorToDateNormalizer;
use ValueError;

final class LineItem extends Beankeeper
{
    use HasFactory;

    protected $table = 'beankeep_line_items';

    protected $fillable = [
        'account_id',
        'transaction_id',
        'debit',
        'credit',
    ];

    protected $attributes = [
        'debit' => 0,
        'credit' => 0,
    ];

    protected static function booted(): void
    {
        static::saving(function (LineItem $lineItem) {
            return $lineItem->isDebit() xor $lineItem->isCredit();
        });
    }

    protected static function newFactory()
    {
        return LineItemFactory::new();
    }

    /**
     * @param LineItem[] $models
     */
    public function newCollection(array $models = []): LineItemCollection
    {
        return new LineItemCollection($models);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function scopeLedgerEntries(
        Builder $query,
        ?CarbonPeriod $period = null,
        null|string|Carbon|CarbonImmutable|CarbonPeriod $priorTo = null,
    ): void {
        if ($period && $priorTo) {
            throw new ValueError('You cannot specify both a period and '
                . 'priorTo argument.');
        }

        if ($priorTo) {
            self::scopeLedgerEntriesPriorTo($query, $priorTo);

            return;
        }

        self::scopeLedgerEntriesForPeriod($query, $period);
    }

    public function scopeLedgerEntriesForPeriod(
        Builder $query,
        ?CarbonPeriod $period = null,
    ): void {
        $period = BeankeepPeriod::from($period);

        $query->whereHas('transaction', fn (Builder $query) => $query
            ->whereBetween('date', $period)
            ->where('posted', true));
    }

    public function scopeLedgerEntriesPriorTo(
        Builder $query,
        string|Carbon|CarbonImmutable|CarbonPeriod $date,
    ) {
        $date = PriorToDateNormalizer::normalize($date);

        $query->whereHas('transaction', fn (Builder $query) => $query
            ->where('date', '<', $date)
            ->where('posted', true));
    }

    public function scopePeriod(
        Builder $query,
        ?CarbonPeriod $period = null,
    ): void {
        $period = BeankeepPeriod::from($period);

        $query->whereHas('transaction', fn (Builder $query) => $query
            ->whereBetween('date', $period));
    }

    public function scopePriorTo(
        Builder $query,
        string|Carbon|CarbonImmutable|CarbonPeriod $date,
    ): void {
        $date = PriorToDateNormalizer::normalize($date);

        $query->whereHas('transaction', fn (Builder $query) => $query
            ->where('date', '<', $date));
    }

    public function scopePosted(Builder $query): void {
        $query->whereHas('transaction', fn (Builder $query) => $query
            ->where('posted', true));
    }

    public function scopePending(Builder $query): void
    {
        $query->whereHas('transaction', fn (Builder $query) => $query
            ->where('posted', false));
    }

    public function scopeDebits(Builder $query): void
    {
        $query->where('debit', '>', 0)
            ->where('credit', 0);
    }

    public function scopeCredits(Builder $query): void
    {
        $query->where('credit', '>', 0)
            ->where('debit', 0);
    }

    public function isDebit(): bool
    {
        return $this->debit > 0;
    }

    public function isCredit(): bool
    {
        return $this->credit > 0;
    }

    public function debitInDollars(): float
    {
        return $this->debit / 100;
    }

    public function creditInDollars(): float
    {
        return $this->credit / 100;
    }
}
