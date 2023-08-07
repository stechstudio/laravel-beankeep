<?php

declare(strict_types=1);

namespace STS\Beankeep\Models;

use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use STS\Beankeep\Database\Factories\LineItemFactory;
use STS\Beankeep\Support\LineItemCollection;
use STS\Beankeep\Support\LedgerCollection;

final class LineItem extends Beankeeper
{
    use HasFactory;

    protected $table = 'beankeep_line_items';

    private string $collectionClass = LineItemCollection::class;

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
        if (count($models) > 0 && $this->collectionClass == LedgerCollection::class) {
            $accountId = $models[0]->account_id;

            foreach ($models as $model) {
                if ($accountId != $model->account_id) {
                    return new LineItemCollection($models);
                }
            }

            return new LedgerCollection($models);
        }

        return new LineItemCollection($models);
    }

    public static function defaultPeriod(): CarbonPeriod
    {
        $startOfYear = Carbon::now()->startOfYear();
        $endOfYear = Carbon::now()->endOfYear();

        return $startOfYear->daysUntil($endOfYear);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    // TODO(zmd): we should require the account to scope to as well, for our
    //   purposes ledger's are account-specific
    public function scopeLedger(
        Builder $query,
        ?iterable $period = null,
    ): void {
        $this->collectionClass = LedgerCollection::class;

        $query->whereHas('transaction', fn (Builder $query) => $query
            ->whereBetween('date', $period ?? self::defaultPeriod())
            ->where('posted', true));
    }

    public function scopePeriod(
        Builder $query,
        ?iterable $period = null,
    ): void {
        $query->whereHas('transaction', fn (Builder $query) => $query
            ->whereBetween('date', $period ?? self::defaultPeriod()));
    }

    // TODO(zmd): test me:
    public function scopePriorTo(
        Builder $query,
        string|Carbon|CarbonImmutable|iterable $date,
    ): void {
        if (is_iterable($date)) {
            $date = iterator_to_array($date)[0];
        }

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
