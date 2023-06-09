<?php

namespace Tests\Feature\Post;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Post;

class PostAPITest extends TestCase
{
    public function test_api_can_create_post() {
        $user = User::factory()->create();

        // create access token
        $token = $this->post('api/v1/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $headers = [
            'Authorization' => 'Bearer ' . $token['data']['token'],
            'Accept' => 'application/json'
        ];

        // send API
        $response = $this->post('api/v1/posts', [
            'title' => fake()->company(),
            'slug' => fake()->slug(),
        ], $headers);

        $response->assertStatus(201);
    }

    public function test_api_cannot_create_duplicate_post() {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        // create access token
        $token = $this->post('api/v1/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $headers = [
            'Authorization' => 'Bearer ' . $token['data']['token'],
            'Accept' => 'application/json'
        ];

        // send API
        $response = $this->post('api/v1/posts', [
            'title' => $post->title,
            'slug' => fake()->slug(),
        ], $headers);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title']);
    }
    
    public function test_api_can_update_post() {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        // create access token
        $token = $this->post('api/v1/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $headers = [
            'Authorization' => 'Bearer ' . $token['data']['token'],
            'Accept' => 'application/json'
        ];

        // send API
        $response = $this->put('api/v1/posts/edit/' . $post->uuid, [
            'title' => fake()->company(),
            'slug' => fake()->slug(),
        ], $headers);

        $response->assertStatus(200);
    }

    public function test_api_can_delete_post() {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        // create access token
        $token = $this->post('api/v1/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $headers = [
            'Authorization' => 'Bearer ' . $token['data']['token'],
            'Accept' => 'application/json'
        ];

        // send API
        $response = $this->delete('api/v1/posts/delete/' . $post->uuid, [], $headers);

        $response->assertStatus(200);
    }

    public function test_cannot_access_without_token() {
        // get without token
        $response = $this->get('api/v1/posts');
        $response->assertStatus(401);

        // create without token
        $headers = [
            'Accept' => 'application/json'
        ];

        $create_response = $this->post('api/v1/posts', [
            'title' => fake()->company(),
            'slug' => fake()->slug(),
        ], $headers);

        $create_response->assertStatus(401);
    }
}
