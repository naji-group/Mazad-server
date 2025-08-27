<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
class AuthenticateMarketer extends Middleware
{

    protected function unauthenticated($request, array $guards)
    {
        abort(response()->json(['error' => 'Unauthenticated'], 401));
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
     protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null :  abort(response()->json(['error' => 'Unauthenticated'], 401));
    }
}
