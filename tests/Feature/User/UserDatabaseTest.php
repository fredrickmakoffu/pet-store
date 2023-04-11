<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_can_create_user()
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

        $user = User::factory()->create($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', $data);
    }

    public function test_can_update_user()
    {
        $user = User::factory()->create();

        $data = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => fake()->unique()->safeEmail(),
            'phone_number' => fake()->phoneNumber(),
            'is_admin' => true,
        ];

        $user->update($data);

        $this->assertDatabaseHas('users', $data);
    }
    
    
    public function test_can_delete_user()
    {
        $user = User::factory()->create();

        $user->delete();

        $this->assertSoftDeleted('users', [
            'id' => $user->id
        ]);
    }

    public function test_can_get_user()
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas('users', [
            'id' => $user->id
        ]);
    }
}
