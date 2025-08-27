<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MarketerController;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('registermarketer', [MarketerController::class, 'register']);
Route::post('loginmarketer', [MarketerController::class, 'login']);

Route::middleware('auth_marketer:api_marketers')->prefix('marketer')->group(function () {
    Route::post('/profile', function () {
       return response()->json("ok");
    }) ;
}); 