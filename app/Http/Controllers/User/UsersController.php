<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\CollectionResource;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{

    public function index(Request $request) : CollectionResource {
        $users = User::filter()->get();

        return new CollectionResource($users);
    }

    public function show(string $uuid) : CollectionResource {
        $user = User::where('uuid', $uuid)->first();

        return new CollectionResource($user);
    }

    public function update(UpdateRequest $request, string $uuid) : CollectionResource {
        $user = User::where('uuid', $uuid)->first();
        $user->update($request->validated());

        return new CollectionResource($user);
    }

    public function destroy(string $uuid) : CollectionResource {
        $user = User::where('uuid', $uuid)->first();
        $user->delete();

        return new CollectionResource([]);
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
}
