<?php

declare(strict_types=1);

namespace STS\Beankeep\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use STS\Beankeep\Contracts\Keepable;
use STS\Beankeep\Models\Transaction;

trait KeepAsTransaction
{
    public function keeper(): MorphOne
    {
        return $this->morphOne(Transaction::class, 'keepable');
    }

    public function keepAttributes(): array
    {
        return [
            'date' => $this->getKeepableDate(),
            'posted' => $this->getKeepablePosted(),
            'memo' => $this->getKeepableMemo(),
        ];
    }

    public static function bootKeepAsTransaction(): void
    {
        self::created(function (Keepable $model) {
            $transaction = new Transaction($model->keepAttributes());
            $transaction->keepable()->associate($model);

            $transaction->save();
        });
    }
}
