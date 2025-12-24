<?php

use App\Http\Controllers\Api\RestreamController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\NotifyController;
use App\Http\Controllers\Web\GoogleAuthController;
//use App\Http\Controllers\Web\MarketerAuthController;
//use Laravel\Socialite\Facades\Socialite;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/google/auth/{marketer_id}', [GoogleAuthController::class, 'redirectToGoogle']);
Route::get('/google/oauth/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
//Route::get('/livepush/redirect', [GoogleAuthController::class, 'livepush_redirect']);
Route::get('/storagelink', function () {
    $exitCode = Artisan::call('storage:link');
    return 'ok';
});
Route::get('/routeclear', function () {
    $exitCode = Artisan::call('route:cache'); 
    return 'ok';
  });
  Route::get('/cashclear', function () {
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('config:cache');
    $exitCode = Artisan::call('config:clear');
    $exitCode = Artisan::call('view:clear');
    $exitCode = Artisan::call('optimize');
    
    
    // $exitCode = Artisan::call('icons:cache');
  
    return 'ok';
  });
  Route::get('/migtables', function () {
    $exitCode = Artisan::call('migrate'); 
    return 'ok';
  });
Route::get('/', [HomeController::class, 'index'])->name('site.home') ;
Route::get('/pages/{slug}', [HomeController::class, 'static_page'])->name('site.pages') ;
Route::get('testnot', [NotifyController::class, 'index']);
Route::post('saveToken', [NotifyController::class, 'savetoken'])->name('saveToken');
 Route::post('sendNotification', [NotifyController::class, 'sendNotification']);
 Route::post('sendbytoken', [NotifyController::class, 'sendbytoken']);
 Route::get('testnotify', [NotifyController::class, 'testnotify']);

//  Route::prefix('restream')->group(function () {
//   Route::get('login/{marketer_id}', [RestreamController::class, 'login']);
//   Route::get('redirect', [RestreamController::class, 'redirectfromReastream']);
//   //Route::get('tokenredirect', [RestreamController::class, 'tokenRedirect']);
  
//  });
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