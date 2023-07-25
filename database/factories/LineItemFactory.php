<?php

declare(strict_types=1);

namespace STS\Beankeep\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use STS\Beankeep\Models\LineItem;

class LineItemFactory extends Factory
{
    protected $model = LineItem::class;

    public function definition(): array
    {
        return [];
    }
}
