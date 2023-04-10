<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\ManageJwtTokens;
use App\Services\GenerateJwtViolationErrorMessage;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
        $uuid = (new ManageJwtTokens())->getUuidFromToken($token);

        if( !$uuid || !(new ManageJwtTokens())->validateToken($token, $uuid)) {
            abort(401, 'Invalid or expired token');
        }

        return $next($request);
    }
}
