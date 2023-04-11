<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Promotion;

class PromotionAPITest extends TestCase
{
    public function test_api_can_create_promotion() {
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
        $response = $this->post('api/v1/promotions', [
            'title' => fake()->company(),
            'content' => fake()->paragraph(),
            'metadata' => [
                'type' => fake()->randomElement(['small', 'medium', 'large'])
            ]
        ], $headers);

        $response->assertStatus(201);
    }

    public function test_api_cannot_create_duplicate_promotion() {
        $user = User::factory()->create();
        $promotion = Promotion::factory()->create();

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
        $response = $this->post('api/v1/promotions', [
            'title' => $promotion->title,
            'content' => fake()->paragraph(),
            'metadata' => [
                'type' => fake()->randomElement(['small', 'medium', 'large'])
            ]
        ], $headers);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title']);
    }
    
    public function test_api_can_update_promotion() {
        $user = User::factory()->create();
        $promotion = Promotion::factory()->create();

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
        $response = $this->put('api/v1/promotions/edit/' . $promotion->uuid, [
            'title' => fake()->company(),
            'content' => fake()->paragraph(),
            'metadata' => [
                'type' => fake()->randomElement(['small', 'medium', 'large'])
            ]
        ], $headers);

        $response->assertStatus(200);
    }

    public function test_api_can_delete_promotion() {
        $user = User::factory()->create();
        $promotion = Promotion::factory()->create();

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
        $response = $this->delete('api/v1/promotions/delete/' . $promotion->uuid, [], $headers);

        $response->assertStatus(200);
    }

    public function test_cannot_access_without_token() {
        // get without token
        $response = $this->get('api/v1/promotions');
        $response->assertStatus(401);

        // create without token
        $headers = [
            'Accept' => 'application/json'
        ];

        $create_response = $this->post('api/v1/promotions', [
            'title' => fake()->company(),
            'content' => fake()->paragraph(),
            'metadata' => [
                'type' => fake()->randomElement(['small', 'medium', 'large'])
            ]
        ], $headers);

        $create_response->assertStatus(401);
    }
}
