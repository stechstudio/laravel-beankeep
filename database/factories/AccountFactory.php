<?php

declare(strict_types=1);

namespace STS\Beankeep\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Models\Account;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition(): array
    {
        return [];
    }
}
