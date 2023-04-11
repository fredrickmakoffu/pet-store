<?php

namespace Tests\Feature\Brand;

use Tests\TestCase;
use App\Models\User;
use App\Models\Brand;

class BrandAPITest extends TestCase
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

    public function test_api_can_create_brand() {
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
        $response = $this->post('api/v1/brands', [
            'title' => fake()->company(),
            'slug' => fake()->slug(),
        ], $headers);

        $response->assertStatus(201);
    }

    public function test_api_cannot_create_duplicate_brand() {
        $user = User::factory()->create();
        $brand = Brand::factory()->create();

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
        $response = $this->post('api/v1/brands', [
            'title' => $brand->title,
            'slug' => fake()->slug(),
        ], $headers);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title']);
    }
    
    public function test_api_can_update_brand() {
        $user = User::factory()->create();
        $brand = Brand::factory()->create();

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
        $response = $this->put('api/v1/brands/edit/' . $brand->uuid, [
            'title' => fake()->company(),
            'slug' => fake()->slug(),
        ], $headers);

        $response->assertStatus(200);
    }

    public function test_api_can_delete_brand() {
        $user = User::factory()->create();
        $brand = Brand::factory()->create();

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
        $response = $this->delete('api/v1/brands/delete/' . $brand->uuid, [], $headers);

        $response->assertStatus(200);
    }

    public function test_cannot_access_without_token() {
        // get without token
        $response = $this->get('api/v1/brands');
        $response->assertStatus(401);

        // create without token
        $headers = [
            'Accept' => 'application/json'
        ];

        $create_response = $this->post('api/v1/brands', [
            'title' => fake()->company(),
            'slug' => fake()->slug(),
        ], $headers);

        $create_response->assertStatus(401);
    }
}
