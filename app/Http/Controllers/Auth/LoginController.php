<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\JwtToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\CollectionResource;

class LoginController extends Controller
{
    public function store(LoginRequest $request) : CollectionResource {
      $data = $request->validated();

     	// login user
			Auth::guard('jwt')->attempt($data);

			$token = Auth::guard('jwt')->getToken();

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
