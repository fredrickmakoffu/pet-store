<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserAPITest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function test_can_create_user_via_api()
    {
        // create user
        $email = fake()->unique()->safeEmail();
        $phone_number = fake()->phoneNumber();
        
        $data = [
            'uuid' => Str::uuid(),
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => $email,
            'phone_number' => $phone_number,
            'is_admin' => true,
            'password' => Hash::make('password'),
        ];

        User::factory()->create($data);

        // create access token
        $token = $this->post('api/v1/login', [
            'email' => $email,
            'password' => 'password'
        ]);

        $headers = [
            'Authorization' => 'Bearer ' . $token['data']['token'],
            'Accept' => 'application/json'
        ];

        // send API
        $response = $this->post('api/v1/admin/create', [
            'first_name' => 'Test Admin',
            'last_name' => 'User',
            'email' =>  fake()->unique()->safeEmail(),
            'phone_number' => fake()->phoneNumber(),
            'password' => 'password'
        ], $headers);

        $response->assertStatus(201);
    }

    public function test_can_login() {
        $email = fake()->unique()->safeEmail();
        $phone_number = fake()->phoneNumber();
        
        $data = [
            'uuid' => Str::uuid(),
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => $email,
            'phone_number' => $phone_number,
            'is_admin' => true,
            'password' => Hash::make('password'),
        ];

        User::factory()->create($data);

        $response = $this->post('api/v1/login', [
            'email' => $email,
            'password' => 'password'
        ]);

        $response->assertStatus(200);
    }

    public function test_cannot_login_bad_email() {
        $email = fake()->unique()->safeEmail();
        $phone_number = fake()->phoneNumber();
        
        $data = [
            'uuid' => Str::uuid(),
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => $email,
            'phone_number' => $phone_number,
            'is_admin' => true,
            'password' => Hash::make('password'),
        ];

        User::factory()->create($data);
        
        //  test any wrong email gives error 401
        $response = $this->post('api/v1/login', [
            'email' => $email
        ]);

        $response->assertJsonValidationErrorFor('password');

        // test any wrong password | email gives error 401
        $response = $this->post('api/v1/login', [
            'email' => $email,
            'password' => 'badpassword'
        ]);

        $response->assertStatus(401);
    }

    public function test_can_update_user_via_api() {
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

        $phone_number = fake()->phoneNumber();

        // send API, update phone number
        $response = $this->put('api/v1/users/edit/' . $user->uuid, [
            'first_name' => $user->first_name,
            'last_name' =>  $user->last_name,
            'email' =>  $user->email,
            'phone_number' => $phone_number
        ], $headers);

        $response->assertStatus(200);
        $this->assertEquals($phone_number, $response['data']['phone_number']);
    }
}
