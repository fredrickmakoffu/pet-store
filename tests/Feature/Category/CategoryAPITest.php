<?php

namespace Tests\Feature\Category;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Category;
use App\Models\User;

class CategoryAPITest extends TestCase
{
    public function test_api_can_create_category() {
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
        $response = $this->post('api/v1/category', [
            'title' => fake()->company(),
            'slug' => fake()->slug(),
        ], $headers);

        $response->assertStatus(201);
    }

    public function test_api_cannot_create_duplicate_category() {
        $user = User::factory()->create();
        $category = Category::factory()->create();

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
        $response = $this->post('api/v1/category', [
            'title' => $category->title,
            'slug' => fake()->slug(),
        ], $headers);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title']);
    }
    
    public function test_api_can_update_category() {
        $user = User::factory()->create();
        $category = Category::factory()->create();

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
        $response = $this->put('api/v1/category/edit/' . $category->uuid, [
            'title' => fake()->company(),
            'slug' => fake()->slug(),
        ], $headers);

        $response->assertStatus(200);
    }

    public function test_api_can_delete_category() {
        $user = User::factory()->create();
        $category = Category::factory()->create();

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
        $response = $this->delete('api/v1/category/delete/' . $category->uuid, [], $headers);

        $response->assertStatus(200);
    }

    public function test_cannot_access_without_token() {
        // get without token
        $response = $this->get('api/v1/category');
        $response->assertStatus(401);

        // create without token
        $headers = [
            'Accept' => 'application/json'
        ];

        $create_response = $this->post('api/v1/category', [
            'title' => fake()->company(),
            'slug' => fake()->slug(),
        ], $headers);

        $create_response->assertStatus(401);
    }
}
