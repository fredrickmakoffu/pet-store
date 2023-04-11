<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Post;

class PostDatabaseTest extends TestCase
{
    public function test_can_create_brand() {
        $post = Post::factory()->create();

        $this->assertInstanceOf(Post::class, $post);
        $this->assertDatabaseHas('posts', ['title' => $post->title]);
    }

    public function test_can_update_brand() {
        $post = Post::factory()->create();

        $data = [
            'title' => fake()->company(),
            'slug' => fake()->slug(),
        ];

        $post->update($data);

        $this->assertDatabaseHas('posts', $data);
    }

    public function test_can_delete_brand() {
        $post = Post::factory()->create();

        $post->delete();

        $this->assertSoftDeleted('posts', [
            'id' => $post->id
        ]);
    }
}
