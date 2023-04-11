<?php

namespace Tests\Feature\Brand;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Brand;

class BrandDatabaseTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_can_create_brand() {
        $brand = Brand::factory()->create();

        $this->assertInstanceOf(Brand::class, $brand);
        $this->assertDatabaseHas('brands', ['title' => $brand->title]);
    }

    public function test_can_update_brand() {
        $brand = Brand::factory()->create();

        $data = [
            'title' => fake()->company(),
            'slug' => fake()->slug(),
        ];

        $brand->update($data);

        $this->assertDatabaseHas('brands', $data);
    }

    public function test_can_delete_brand() {
        $brand = Brand::factory()->create();

        $brand->delete();

        $this->assertSoftDeleted('brands', [
            'id' => $brand->id
        ]);
    }
}
