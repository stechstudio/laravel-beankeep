<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature;

use STS\Beankeep\Database\Factories\Support\CanLookupAccounts;
use STS\Beankeep\Models\Account as BeankeepAccount;
use STS\Beankeep\Tests\TestCase;
use STS\Beankeep\Tests\TestSupport\Models\Augmented\Account;
use STS\Beankeep\Tests\TestSupport\Traits\BeanConstructors;

final class HasAccountTest extends TestCase
{
    use BeanConstructors;
    use CanLookupAccounts;

    public function testItKnowsItsBeankeepClass(): void
    {
        $this->assertEquals(
            BeankeepAccount::class,
            Account::beankeeperClass(),
        );
    }

    public function testItCanBeAssociatedWithAnEndUserAccountModel(): void
    {
        $this->createAccounts();

        $ar = $this->account('accounts-receivable');
        $ap = $this->account('accounts-payable');

        $arDescription = "It's an account, where other people owe us monies! \o/";
        $ar->keep(Account::create(['description' => $arDescription]));

        $apDescription = "It's an account, where we owe other people monies. :(";
        $ap->keep(Account::create(['description' => $apDescription]));

        $this->assertEquals($arDescription, $ar->keepable->description);
        $this->assertEquals($apDescription, $ap->keepable->description);
    }
}
