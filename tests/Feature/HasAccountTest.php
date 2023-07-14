<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature;

use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Models\Account as BeankeepAccount;
use STS\Beankeep\Tests\TestCase;
use STS\Beankeep\Tests\TestSupport\Models\Augmented\Account;

final class HasAccountTest extends TestCase
{
    public function testItCanBeAssociatedWithAPackageConsumersAccountModel(): void
    {
        $accountsReceivable = BeankeepAccount::create([
            'number' => '1110',
            'type' => AccountType::Asset,
            'name' => 'Accounts Receivable',
        ]);

        $arDescription = "It's an account, where other people owe us monies! \o/";
        $accountsReceivable->keep(Account::create(['description' => $arDescription]));

        $accountsPayable = BeankeepAccount::create([
            'number' => '2100',
            'type' => AccountType::Liability,
            'name' => 'Accounts Payable',
        ]);

        $apDescription = "It's an account, where we owe other people monies. :(";
        $accountsPayable->keep(Account::create(['description' => $apDescription]));

        $this->assertEquals(
            $arDescription,
            $accountsReceivable->keepable->description,
        );

        $this->assertEquals(
            $apDescription,
            $accountsPayable->keepable->description,
        );
    }
}
