<?php

namespace Tests\Feature\Category;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Category;

class CategoryDatabaseTest extends TestCase
{
    public function test_can_create_category() {
        $category = Category::factory()->create();

        $this->assertInstanceOf(Category::class, $category);
        $this->assertDatabaseHas('categories', ['title' => $category->title]);
    }

    public function test_can_update_category() {
        $category = Category::factory()->create();

        $data = [
            'title' => fake()->company(),
            'slug' => fake()->slug(),
        ];

        $category->update($data);

        $this->assertDatabaseHas('categories', $data);
    }

    public function test_can_delete_category() {
        $category = Category::factory()->create();

        $category->delete();

        $this->assertSoftDeleted('categories', [
            'id' => $category->id
        ]);
    }
}
