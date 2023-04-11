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
    public function handle(Request $request, Closure $next, string $roles = null)
    {
        // check if token is provided
        $token = $request->bearerToken();
        if ( !$token) {
            abort(401, 'Invalid or expired token');
        }

        // check if token saved
        $token_details = (new ManageJwtTokens())->getDetailsFromToken($token);
        if( !$token_details || !(new ManageJwtTokens())->validateToken(User::find($token_details->user_id), $token, $token_details->uuid)) {
            abort(401, 'Invalid or expired token');
        }

        // check if role is valid
        if( $roles && !$this->hasRole(User::find($token_details->user_id), $roles)) {
            abort(401, 'You do not have the rights to access this API');
        }

        return $next($request);
    }


    public function hasRole(User $user, string $role) : bool
    {
        return $role == 'admin' && $user->is_admin
            ? true
            : false;
    }
}
