<?php

namespace Tests\Feature\Post;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Post;

class PostDatabaseTest extends TestCase
{
    public function test_can_create_post() {
        $post = Post::factory()->create();

        $this->assertInstanceOf(Post::class, $post);
        $this->assertDatabaseHas('posts', ['title' => $post->title]);
    }

    public function test_can_update_post() {
        $post = Post::factory()->create();

        $data = [
            'title' => fake()->company(),
            'slug' => fake()->slug(),
        ];

        $post->update($data);

        $this->assertDatabaseHas('posts', $data);
    }

    public function test_can_delete_post() {
        $post = Post::factory()->create();

        $post->delete();

        $this->assertSoftDeleted('posts', [
            'id' => $post->id
        ]);
    }
}
