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

    protected function verify(int $id, string $hash) : CollectionResource {
        $user = User::findOrFail($id);

        // if user does not exist
        if( !$user) {
            abort(404, 'User not found.');
        }

        // if user is already verified
        if($user->email_verified_at) {
            abort(403, 'User already verified.');
        }

        // validate hash
        $expectedHash =  sha1($user->email);

        if( !hash_equals($hash, $expectedHash)) {
            abort(403, 'Invalid hash.');
        }
        
        $user->update([
            'email_verified_at' => now()
        ]);

        return new CollectionResource($user);
    }
    
    public function index(IndexRequest $request)  {        
        $users = User::filter($request->validated())->get();

        return new CollectionResource($users);
    }

    public function update(StoreRequest $request, User $user) : CollectionResource {
        $user->update($request->validated());

        return new CollectionResource($user);
    }

    public function destroy(User $user) : CollectionResource {
        $user->delete();

        return new CollectionResource([]);
    }
}
