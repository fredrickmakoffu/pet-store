<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\ManageJwtTokens;
use App\Models\User;

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
            abort(401, 'Invalid or expired token');
        }

        // check if token saved
        $token_details = (new ManageJwtTokens())->getDetailsFromToken($token);

        if( !$token_details->uuid || !(new ManageJwtTokens())->validateToken(User::find($token_details->user_id), $token, $token_details->uuid)) {
            abort(401, 'Invalid or expired token');
        }

        return $next($request);
    }
}
