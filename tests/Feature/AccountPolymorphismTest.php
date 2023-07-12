<?php

declare(strict_types=1);

use STS\Beankeep\Models\Account as BeankeepAccount;
use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Tests\TestSupport\Models\AccountsReceivable;

// TODO(zmd): write a useful test (once we get the plumbing figured out)
it('will eventually test something useful', function () {
    //
    // TODO(zmd): just checking that we can use the package models as well as
    //   the test models which simulate models created by the users of our packages.
    //   Not yet actually testing the polymorphic relations at all.
    //

    $beankeepAccount = BeankeepAccount::create([
        'number' => '1000',
        'type' => AccountType::Asset,
        'name' => 'Assets',
    ]);

    expect($beankeepAccount->number)->toBe('1000');
    expect($beankeepAccount->type)->toBe(AccountType::Asset);
    expect($beankeepAccount->name)->toBe('Assets');

    $ar = AccountsReceivable::create(['number' => '1000']);

    expect($ar->number)->toBe('1000');
});
