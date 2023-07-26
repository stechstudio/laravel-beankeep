<?php

declare(strict_types=1);

namespace STS\Beankeep\Models;

use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use STS\Beankeep\Database\Factories\LineItemFactory;

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

    public function scopeLedger(
        Builder $query,
        ?iterable $period = null,
    ): void {
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
