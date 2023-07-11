<?php

declare(strict_types=1);

use STS\Beankeep\Models\LineItem;

it('can correctly determine when it is a debit', function () {
    $lineItem = new LineItem(['debit' => 100, 'credit' => 0]);
    expect($lineItem->isDebit())->toBeTrue();
});
