<?php

namespace App\Actions\User;

use App\Models\User;

class CreateUser
{
    /**a
     * Create a new user.
     *
     * @param array<string> $data
     * @return User
     * @throws \Exception
     */
    public function __invoke(array $data)
    {
        \Log::info('Creating user with data:', $data);

        return User::create($data);
    }
}
