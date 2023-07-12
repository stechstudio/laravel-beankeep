<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature;

use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Models\Account as BeankeepAccount;
use STS\Beankeep\Tests\TestCase;
use STS\Beankeep\Tests\TestSupport\Models\SimpleAccountsReceivable as AccountsReceivable;

final class AccountPolymorphismTest extends TestCase
{
    public function testItCanBeAssociatedWithAPackageConsumersAccountModel(): void
    {
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

        $this->assertEquals('Accounts Receivable', $beankeepAccount->keepable->name);

        $this->assertEquals('1110', $accountsReceivable->keeper->number);
        $this->assertEquals(AccountType::Asset, $accountsReceivable->keeper->type);
        $this->assertEquals('Accounts Receivable', $accountsReceivable->keeper->name);
    }
}
