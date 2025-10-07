<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\UrlMapping;
use Illuminate\Database\Eloquent\Factories\Factory;

class UrlMappingFactory extends Factory
{
    protected $model = UrlMapping::class;

    // Generates fake UrlMapping records for URL shortener test data.
    public function definition(): array
    {
        return [
            'slug' => bin2hex(random_bytes(4)),
            'url' => fake()->url,
            'admin_hash' => hash_hmac('sha256', bin2hex(random_bytes(4)), config('app.key')),
            'hit_count' => 0,
        ];
    }
}
