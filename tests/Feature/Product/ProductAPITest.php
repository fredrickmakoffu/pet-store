<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Str;
use App\Models\Category;

class ProductAPITest extends TestCase
{
    public function test_api_can_create_product() {
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
        $response = $this->post('api/v1/products', [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 1, 1000),
            'category_id' => $category->id,
            'metadata' => [
                'color' => fake()->colorName(),
                'size' => fake()->randomElement(['small', 'medium', 'large']),
                'weight' => fake()->randomFloat(2, 1, 1000),
                'dimensions' => [
                    'width' => fake()->randomFloat(2, 1, 1000),
                    'height' => fake()->randomFloat(2, 1, 1000),
                    'depth' => fake()->randomFloat(2, 1, 1000),
                ],
            ]
        ], $headers);

        $response->assertStatus(201);
    }

    public function test_api_cannot_create_duplicate_product() {
        $user = User::factory()->create();
        $product = Product::factory()->create();
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
        $response = $this->post('api/v1/products', [
            'title' => $product->title,
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 1, 1000),
            'category_id' => $category->id,
            'metadata' => json_encode([
                'color' => fake()->colorName(),
                'size' => fake()->randomElement(['small', 'medium', 'large']),
                'weight' => fake()->randomFloat(2, 1, 1000),
                'dimensions' => [
                    'width' => fake()->randomFloat(2, 1, 1000),
                    'height' => fake()->randomFloat(2, 1, 1000),
                    'depth' => fake()->randomFloat(2, 1, 1000),
                ],
            ])
        ], $headers);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title']);
    }
    
    public function test_api_can_update_product() {
        $user = User::factory()->create();
        $product = Product::factory()->create();

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
        $response = $this->put('api/v1/products/edit/' . $product->uuid, [
            'title' => fake()->company(),
            'slug' => fake()->slug(),
        ], $headers);

        $response->assertStatus(200);
    }

    public function test_api_can_delete_product() {
        $user = User::factory()->create();
        $product = Product::factory()->create();

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
        $response = $this->delete('api/v1/products/delete/' . $product->uuid, [], $headers);

        $response->assertStatus(200);
    }

    public function test_cannot_access_without_token() {
        // get without token
        $response = $this->get('api/v1/products');
        $response->assertStatus(401);

        // create without token
        $headers = [
            'Accept' => 'application/json'
        ];

        $create_response = $this->post('api/v1/products', [
            'title' => fake()->company(),
            'slug' => fake()->slug(),
        ], $headers);

        $create_response->assertStatus(401);
    }
}
