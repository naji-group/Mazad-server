<?php

use App\Http\Controllers\Api\LiveController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MarketerController;
 
use Laravel\Socialite\Facades\Socialite;
use App\Http\Middleware\Api\SetLocale;
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

//Route::post('registermarketer', [MarketerController::class, 'register']);//prefix('{lang}')->
Route::middleware(SetLocale::class)->group(function ()  {
Route::post('loginmarketer', [MarketerController::class, 'login']);
Route::post('loginmarketerbyprovider', [MarketerController::class, 'loginprovider']);
Route::post('resetpassword', [MarketerController::class, 'resetpassword']); 
//Route::get('loginmarketer/{provider}', 'SocialController@redirect');
// Route::get('loginmarketerprovider/{provider}', [MarketerController::class, 'provider_redirect'])->name('api_provider_redirect')->middleware('web');
 
// Route::get('loginmarketerprovider/callback/{provider}', [MarketerController::class, 'callback_provider'])->name('callback_provider');
 

Route::middleware('auth_marketer:api_marketers')->group(function () {
    Route::prefix('marketer')->group(function () {
        Route::post('updateprofile', [MarketerController::class, 'updateprofile']);
        Route::post('getprofile', [MarketerController::class, 'getprofile']);
        Route::post('/logout', [MarketerController::class, 'logout_marketer']);
        Route::post('/deleteaccount', [MarketerController::class, 'deleteaccount']);
        Route::post('getsocials', [MarketerController::class, 'getsocials']);
        Route::post('updatesocials', [MarketerController::class, 'updatesocials']);
        Route::post('savefirebasetoken', [MarketerController::class, 'savetoken']); 
        Route::post('sendnotify', [MarketerController::class, 'sendnotify']); 

    });
    Route::prefix('live')->group(function () {
 
        Route::post('savefaceaccesstoken', [LiveController::class, 'savefaceaccesstoken']); 
        Route::post('facebook/create', [LiveController::class, 'create_facebook_live']);
    });

}); 
}) ;