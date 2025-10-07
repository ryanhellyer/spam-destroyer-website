<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\PathCounter;
use Illuminate\Database\Eloquent\Factories\Factory;

class PathCounterFactory extends Factory
{
    protected $model = PathCounter::class;

    // Generates fake PathCounter records with a random path and hit count.
    public function definition(): array
    {
        return [
            'path' => '/'.fake()->unique()->word,
            'hit_count' => fake()->numberBetween(1, 100),
        ];
    }
}
