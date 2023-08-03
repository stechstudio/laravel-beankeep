<?php

declare(strict_types=1);

namespace STS\Beankeep\Database\Seeders;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Database\Seeder as BaseSeeder;

class Seeder extends BaseSeeder
{
    protected Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }
}
