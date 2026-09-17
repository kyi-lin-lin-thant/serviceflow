<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'category_id' => ServiceCategory::factory(),
            'price' => fake()->randomFloat(2, 20, 200),
            'duration_minutes' => fake()->numberBetween(30, 180),
            'status' => 'active',
        ];
    }
}
