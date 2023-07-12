<?php

declare(strict_types=1);

use STS\Beankeep\Models\Account as BeankeepAccount;
use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Tests\TestSupport\Models\SimpleAccountsReceivable as AccountsReceivable;

it("can be associated with a package consumer's account model", function () {
    $accountsReceivable = AccountsReceivable::create([
        'number' => '1110',
        'name' => 'Accounts Receivable',
    ]);

    $beankeepAccount = BeankeepAccount::create([
        'number' => '1110',
        'type' => AccountType::Asset,
        'name' => 'Accounts Receivable',
    ]);

    $accountsReceivable->keeper()->save($beankeepAccount);

    expect($beankeepAccount->keepable->name)->toBe('Accounts Receivable');

    expect($accountsReceivable->keeper->number)->toBe('1110');
    expect($accountsReceivable->keeper->type)->toBe(AccountType::Asset);
    expect($accountsReceivable->keeper->name)->toBe('Accounts Receivable');
});
