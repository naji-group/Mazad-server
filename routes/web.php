<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;
//use App\Http\Controllers\Web\MarketerAuthController;
//use Laravel\Socialite\Facades\Socialite;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/storagelink', function () {
    $exitCode = Artisan::call('storage:link');
    return 'ok';
});
Route::get('/', [HomeController::class, 'index'])->name('site.home') ;
/*
Route::prefix('marketer')->group(function () {
    Route::get('/login', [MarketerAuthController::class, 'showLoginForm'])->name('marketer.login');
    Route::post('/login', [MarketerAuthController::class, 'login']);
    Route::get('/register', [MarketerAuthController::class, 'showRegisterForm'])->name('marketer.register');
    Route::post('/register', [MarketerAuthController::class, 'register']);
    Route::post('/logout', [MarketerAuthController::class, 'logout'])->name('marketer.logout');
    
    
    Route::middleware('auth_web_marketer:web_marketers')->group(function () {

        Route::get('/dashboard', [MarketerAuthController::class, 'show_profile'])->name('marketer.dashboard');
        
    });
});
*/
/*
Route::get('/auth/redirect', function () {
    return Socialite::driver('google')->redirect();
});
Route::get('/auth/callback', [MarketerAuthController::class, 'callback_provider'])->name('callback_provider');
 */