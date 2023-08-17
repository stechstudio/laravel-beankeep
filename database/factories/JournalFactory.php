<?php

declare(strict_types=1);

namespace STS\Beankeep\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use STS\Beankeep\Models\Journal;

class JournalFactory extends Factory
{
    protected $model = Journal::class;

    public function definition(): array
    {
        return [];
    }
}
