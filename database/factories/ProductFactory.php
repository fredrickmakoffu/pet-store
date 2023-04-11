<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'uuid' => Str::uuid(),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 1, 1000),
            'category_id' => Category::factory(),
            'metadata' => json_encode([
                'color' => fake()->colorName(),
                'size' => fake()->randomElement(['small', 'medium', 'large']),
                'weight' => fake()->randomFloat(2, 1, 1000),
                'dimensions' => [
                    'width' => fake()->randomFloat(2, 1, 1000),
                    'height' => fake()->randomFloat(2, 1, 1000),
                    'depth' => fake()->randomFloat(2, 1, 1000),
                ],
            ])
        ];
    }
}
