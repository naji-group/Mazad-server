<?php

namespace App\Http\Middleware\Web;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class AuthWebMarketer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('web_marketers')->check()) {
            return redirect()->route('marketer.login');
        }

        return $next($request);
    }
}
