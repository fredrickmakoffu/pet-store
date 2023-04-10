<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ManageJwtTokens;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\StoreRequest;

class LoginController extends Controller
{
    public function login(StoreRequest $request) {
        $data = $request->validated();
        // check if user exists
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => ['These credentials do not match our records.']
            ], 404);
        }

        // create token
        $jwt_token = new ManageJwtTokens();
        $token = $jwt_token->createToken($user);

        return response([
            'message' => 'Logged in successfully',
            'token' => $token->toString(),
            'expiration_date' => $token->claims()->all()['exp']
        ], 200);
    }
}
