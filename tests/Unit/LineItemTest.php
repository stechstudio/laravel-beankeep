<?php

declare(strict_types=1);

use STS\Beankeep\Models\LineItem;

it('can correctly determine when it is a debit', function () {
    $lineItem = new LineItem(['debit' => 100, 'credit' => 0]);

    expect($lineItem->isDebit())->toBeTrue();
});

it('can correctly determine when it is not a debit', function () {
    $lineItem = new LineItem(['debit' => 0, 'credit' => 100]);

    expect($lineItem->isDebit())->toBeFalse();
});

it('can correctly determine when it is a credit', function () {
    $lineItem = new LineItem(['debit' => 0, 'credit' => 100]);

    expect($lineItem->isCredit())->toBeTrue();
});

it('can correctly determine when it is not a credit', function () {
    $lineItem = new LineItem(['debit' => 100, 'credit' => 0]);

    expect($lineItem->isCredit())->toBeFalse();
});
