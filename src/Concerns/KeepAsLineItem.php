<?php

declare(strict_types=1);

namespace STS\Beankeep\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use STS\Beankeep\Contracts\Keepable;
use STS\Beankeep\Models\LineItem;

trait KeepAsLineItem
{
    public function keeper(): MorphOne
    {
        return $this->morphOne(LineItem::class, 'keepable');
    }

    public function keepAttributes(): array
    {
        // TODO(zmd): required account foreign id
        // TODO(zmd): required transaction foreign id
        return [
            'debit' => $this->getKeepableDebit(),
            'credit' => $this->getKeepableCredit(),
        ];
    }

    public static function bootKeepAsLineItem(): void
    {
        self::created(function (Keepable $model) {
            $lineItem = new LineItem($model->keepAttributes());
            $lineItem->keepable()->associate($model);

            $lineItem->save();
        });
    }
}
