<?php

declare(strict_types=1);

namespace STS\Beankeep\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use STS\Beankeep\Models\Transaction;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    protected Carbon $startOfYear;

    public function definition(): array
    {
        return [
            'memo' => $this->memo(),
            'date' => $this->date(),
        ];
    }

    protected function memo(): string
    {
        $numWords = $this->faker->numberBetween(3, 7);

        return implode(' ', $this->faker->words($numWords));
    }

    protected function date(): Carbon
    {
        $dayOfYear = $this->faker->numberBetween(0, 364);

        return $this->startOfYear()->addDays($dayOfYear);
    }

    protected function startOfYear(): Carbon
    {
        return (
            $this->startOfYear ??= Carbon::now()->startOfYear()
        )->copy();
    }
}
