<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Product;
use Illuminate\Support\Str;
use App\Models\Category;

class ProductDatabaseTest extends TestCase
{
    public function test_can_create_product() {
        $product = Product::factory()->create();

        $this->assertInstanceOf(Product::class, $product);
        $this->assertDatabaseHas('products', ['title' => $product->title]);
    }

    public function test_can_update_product() {
        $product = Product::factory()->create();

        $data = [
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 1, 1000)
        ];

        $product->update($data);

        $this->assertDatabaseHas('products', [
            'description' => $data['description'],
            'price' => $data['price'],
        ]);
    }

    public function test_can_delete_product() {
        $product = Product::factory()->create();

        $product->delete();

        $this->assertSoftDeleted('products', [
            'id' => $product->id
        ]);
    }
}
