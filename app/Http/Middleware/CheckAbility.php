<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // تحقق من أن الـ user يمتلك الـ ability
        if (!$user->hasAbility($ability)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
