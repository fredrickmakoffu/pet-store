<?php

namespace Tests\Feature;

use App\Models\Promotion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PromotionDatabaseTest extends TestCase
{
    public function test_can_create_promotion() {
        $promotion = Promotion::factory()->create();

        $this->assertInstanceOf(Promotion::class, $promotion);
        $this->assertDatabaseHas('promotions', ['title' => $promotion->title]);
    }

    public function test_can_update_promotion() {
        $promotion = Promotion::factory()->create();

        $data = [
            'title' => fake()->company(),
            'content' => fake()->slug(),
            'metadata' => json_encode([
                'type' => fake()->randomElement(['small', 'medium', 'large'])
            ])
        ];

        $promotion->update($data);

        $this->assertDatabaseHas('promotions', $data);
    }

    public function test_can_delete_promotions() {
        $promotion = Promotion::factory()->create();

        $promotion->delete();

        $this->assertSoftDeleted('promotions', [
            'id' => $promotion->id
        ]);
    }
}
