<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $locale = $request->route('lang');
     //   $locale = $request->route('lang') ?? config('app.fallback_locale');
    //
        if (in_array($locale, config('app.available_locales'))) {
            app()->setLocale($locale);
            return $next($request);
        } 
        return response()->json("not found",404);
        // else {
        //     // return redirect()->route('marketer.login');
        //     app()->setLocale(config('app.fallback_locale'));
        // }
        
      
    }
    // public function handle(Request $request, Closure $next): Response
    // {
    //     return $next($request);
    // }
}
