<?php

namespace App\Http\Controllers\Auth;

use App\Http\Resources\ErrorResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\CollectionResource;
use Illuminate\Auth\Events\Registered;
use Ramsey\Uuid\Uuid;
use App\Actions\User\CreateUser;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function __construct(private readonly CreateUser $createUser) {}
    /**
     * Register a new user.
     *
     * @param RegisterRequest $request
     * @return CollectionResource
     */

    public function store(RegisterRequest $request): CollectionResource|ErrorResource
    {
        try {
            DB::beginTransaction();

            $data = array_merge($request->validated(), [
                'uuid' => Uuid::uuid4(),
                'password' => Hash::make($request->validated()['password'])
            ]);

            // create user
            $user = (new CreateUser)($data);

            // send verification email
            // event(new Registered($user));
        } catch (\Exception $e) {
            DB::rollBack();

            // return error response
            return new ErrorResource($e);
        }

        DB::commit();

        // return response
        return new CollectionResource(collect($user)->toArray());
    }
}
