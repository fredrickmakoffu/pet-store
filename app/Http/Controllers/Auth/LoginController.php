<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\JwtToken;
use Illuminate\Http\Request;
use App\Services\ManageJwtTokens;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\CollectionResource;

class LoginController extends Controller
{
    public function store(LoginRequest $request) : CollectionResource {
        $data = $request->validated();

        // check if user exists
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            abort(401, 'Invalid credentials');
        }

        // create token
        $token = (new ManageJwtTokens())->createToken($user);

        return new CollectionResource([
            'message' => 'Logged in successfully',
            'token' => $token->toString(),
            'expiration_date' => $token->claims()->all()['exp']
        ]);
    }

    public function logout(Request $request) : CollectionResource {
        // delete token from database
        JwtToken::where('token', $request->bearerToken())->delete();

        return new CollectionResource([]);
    }

}
