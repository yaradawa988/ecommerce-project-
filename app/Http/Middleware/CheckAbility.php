<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

class CheckAbility
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $ability
     */
    public function handle(Request $request, Closure $next, string $ability)
    {
       $user = $request->user();

        if (!$user) {
            return ApiResponse::error('Unauthorized', 401);
        }

        if (!$user->tokenCan($ability)) {
            return ApiResponse::error('Forbidden: Missing ability: ' . $ability, 403);
        }

    
        return $next($request);
    }
}
