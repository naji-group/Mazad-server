<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MarketerController;
 
use Laravel\Socialite\Facades\Socialite;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

//Route::post('registermarketer', [MarketerController::class, 'register']);
Route::post('loginmarketer', [MarketerController::class, 'login']);
//Route::get('loginmarketer/{provider}', 'SocialController@redirect');
Route::get('loginmarketerprovider/{provider}', [MarketerController::class, 'provider_redirect'])->name('api_provider_redirect')->middleware('web');
 
Route::get('loginmarketerprovider/callback/{provider}', [MarketerController::class, 'callback_provider'])->name('callback_provider');
 

Route::middleware('auth_marketer:api_marketers')->prefix('marketer')->group(function () {
    Route::post('/profile', function () {
       return response()->json("ok");
    }) ;
}); 