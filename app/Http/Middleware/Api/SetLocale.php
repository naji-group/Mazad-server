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

        $locale = $request->header('lang');
     //   $locale = $request->route('lang') ?? config('app.fallback_locale');
    //
   
        if (in_array($locale, config('app.available_locales'))) {
            app()->setLocale($locale);
           
        } 
       // return response()->json("not found",404);
        else {
            // return redirect()->route('marketer.login');
            app()->setLocale(env('APP_LOCALE','ar'));
        }
        return $next($request);
      
    }
    // public function handle(Request $request, Closure $next): Response
    // {
    //     return $next($request);
    // }
}
