<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\CollectionResource;
use Illuminate\Auth\Events\Registered;
use App\Models\User;
use Ramsey\Uuid\Uuid;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request) : CollectionResource {
        $user = User::create(array_merge($request->validated(), [
            'uuid' => Uuid::uuid4(),
            'password' => Hash::make($request->validated()['password'])
        ]));

        // send verification email
        event(new Registered($user));

        return new CollectionResource($user);
    }
}
