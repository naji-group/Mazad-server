<?php

use App\Http\Controllers\Api\AuctionController;
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
        
        Route::post('generate_live_token', [LiveController::class, 'generate_live_token']); 
        Route::post('savefaceaccesstoken', [LiveController::class, 'savefaceaccesstoken']); 
        Route::post('facebook/create', [LiveController::class, 'create_facebook_live']);
        Route::post('facebook/end', [LiveController::class, 'end_facebook_live']);
        Route::post('instagram/create', [LiveController::class, 'create_instagram_live']);
        Route::post('instagram/end', [LiveController::class, 'end_instagram_live']);
        Route::post('youtube/start-push', [LiveController::class, 'youtube_push']);
        Route::post('youtube/stop-push', [LiveController::class, 'youtube_stop_push']);   
        Route::post('youtube/start-push-test', [LiveController::class, 'youtube_push_test']);
       // Route::post('youtube/refreshtoken-test', [LiveController::class, 'getrefreshToken']);
        Route::post('youtube/getvideoid', [LiveController::class, 'getYoutubeLiveVideoId']);  

        Route::post('tiktok/start-push', [LiveController::class, 'tiktok_push']);
        Route::post('tiktok/stop-push', [LiveController::class, 'tiktok_stop_push']); 
        
Route::post('/start', [LiveController::class, 'start']);    
Route::post('/end', [LiveController::class, 'endLiveStream']); 
    });
    Route::prefix('auction')->group(function () {
        Route::post('add', [AuctionController::class, 'store']);
        Route::post('view', [AuctionController::class, 'view']);
        Route::post('maxprice', [AuctionController::class, 'max_price']);
        Route::post('extrasocials', [AuctionController::class, 'extra_socials']);     
    });

}); 
}) ;