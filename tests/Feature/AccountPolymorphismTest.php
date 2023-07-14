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
        $keepable = AccountsReceivable::create([
            'number' => '1110',
            'name' => 'Accounts Receivable',
        ]);

        $keeper = BeankeepAccount::create([
            'number' => '1110',
            'type' => AccountType::Asset,
            'name' => 'Accounts Receivable',
        ]);

        $keepable->keeper()->save($keeper);

        $this->assertEquals('Accounts Receivable', $keeper->keepable->name);

        $this->assertEquals('1110', $keepable->keeper->number);
        $this->assertEquals(AccountType::Asset, $keepable->keeper->type);
        $this->assertEquals('Accounts Receivable', $keepable->keeper->name);
    }
}
