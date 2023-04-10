<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\ManageJwtTokens;
use Illuminate\Support\Facades\Log;

class JwtAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        
        // check if token is provided
        if ( !$token) {
            return response()->json(['error' => 'Unauthorized'], 400);
        }

        // check if token saved
        $uuid = (new ManageJwtTokens())->getUuidFromToken($token);

        if( !$uuid) {
            return response()->json(['error' => 'Token does not exist in our database'], 401);
        }

        // check if token is valid
        $validated_token = (new ManageJwtTokens())->validateToken($token, $uuid);
        
        if( !$validated_token['status']) {
            return response()->json([
                'error' => $validated_token['message'],
                'message' => 'Token is invalid'
            ], 401);
        }

        return $next($request);
    }
}
