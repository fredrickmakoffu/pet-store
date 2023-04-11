<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Promotion>
 */
class PromotionFactory extends Factory
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
            'content' => fake()->paragraph(),
            'metadata' => json_encode([
                'type' => fake()->randomElement(['small', 'medium', 'large'])
            ])
        ];
    }
}
