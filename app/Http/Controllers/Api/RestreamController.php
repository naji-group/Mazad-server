<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
class RestreamController extends Controller
{
    //
 //   https://api.restream.io/login?response_type=code&client_id=[your client id]&redirect_uri=[your redirect URI]&state=[random opaque token]
 public function login($marketer_id)
 {
 Http::asForm()->get('https://api.restream.io/login', [
    'response_type' => 'code',
    'client_id' => "najyms@gmail.com",
   // 'client_secret' => config('services.restream.client_secret'),
    'redirect_uri' =>'https://zawed.ae/restream/redirect/',
    'state' =>$marketer_id,
]);
 }
 
 
   public function redirectfromReastream(Request $request)
 {
    $state= $request->state;
    \Log::info('restream redirect', [
        'data' => $request->all(),
    ]);
   
  

     return redirect()->with("ok");
 }
}
