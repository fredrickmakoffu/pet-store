<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\IndexRequest;
use App\Http\Resources\CollectionResource;
use Illuminate\Auth\Events\Registered;

class AdminController extends Controller
{
    public function store(StoreRequest $request) : CollectionResource {
        $user = User::create(array_merge($request->validated(), [
            'uuid' => Uuid::uuid4(),
            'password' => Hash::make(Str::random(16))
        ]));

        // send verification email
        event(new Registered($user));

        return new CollectionResource($user);
    }    
    
    public function index(IndexRequest $request)  {        
        $users = User::filter($request->validated())->get();

        return new CollectionResource($users);
    }

    public function update(StoreRequest $request, $uuid) : CollectionResource {
        $user = User::where('uuid', $uuid)->update($request->validated());

        return new CollectionResource($user);
    }

    public function destroy(string $uuid) : CollectionResource {
        $user = User::where('uuid', $uuid)->first();
        $user->delete();

        return new CollectionResource([]);
    }
}
