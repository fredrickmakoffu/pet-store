<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        if( !User::where('email', 'admin@buckhill.co.uk')->exists()) {
            User::factory()->updateOrCreate([
                'uuid' => Str::uuid(),
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@buckhill.co.uk',
                'email_verified_at' => now(),
                'phone_number' => '+01234567890',
                'is_admin' => true,
                'password' => Hash::make('admin'),
            ]);
        }
    }
}
