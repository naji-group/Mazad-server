<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MarketerSocial;
use App\Models\Social;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle($marketer_id)
    {
        $state=$marketer_id;
        $params = http_build_query([
            'client_id' => config('services.google.client_id'),
            'redirect_uri' =>  config('services.google.redirect_uri')  ,
            'response_type' => 'code',
            'access_type' => 'offline',
            'prompt' => 'consent',
            'scope' => 'https://www.googleapis.com/auth/youtube.force-ssl',
            'state' => $state
        ]);

        return redirect("https://accounts.google.com/o/oauth2/v2/auth?$params");
    }

    public function handleGoogleCallback(Request $request)
{
    if (!$request->code) {
        return response()->json(['error' => 'Missing code']);
    }

    $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
        'code' => $request->code,
        'client_id' => config('services.google.client_id'),
        'client_secret' => config('services.google.client_secret'),
        'redirect_uri' => config('services.google.redirect_uri'),
        'grant_type' => 'authorization_code',
    ]);

    $tokens = $response->json();

    if (!isset($tokens['access_token'])) {
        \Log::error('access_token error', ['error' => $tokens]);
        return response()->json(['error' => $tokens]);
    }

    //$marketerId = $state['marketer_id']; // ← هذا هو المسوق الذي طلب التوكن

    $access = $tokens['access_token'];
    $refresh = $tokens['refresh_token'] ?? null;
    $expires_in = $tokens['expires_in'];
   // $state=null;
    \Log::info('youtube state ', [
        'data' =>$request->state,
    ]);
    $marketerId =null;

    if (isset($request->state)) {
 $marketerId=$request->state;
        // $state = json_decode(base64_decode($request->state), true);
        // \Log::info('youtube state decode', [
        //     'data' => $state,
        // ]);
        // $marketerId =  $state;
        // \Log::info('youtube marketer id', [
        //     'data' => $marketerId,
        // ]);
        
        $social = Social::where('code', 'youtube')->first();
        if ($social) {
            $record = MarketerSocial::firstOrNew([
                'marketer_id' => $marketerId,
                'social_id' => $social->id,
            ]);
            $record->access_token = $access;
            $record->refresh_token = $refresh?? null;
            $record->expires_in = $expires_in;
            $record->expires_in_date =now()->addSeconds($expires_in);
             $record->save();          
        }


        }

    // // ✅ Correct deep link format (single slash)
    $deepLink = "com.ae.zawed://oauthcallback?" . http_build_query([
        "access_token" => $access,
        "refresh_token" => $refresh,
        "expires_in" => $expires_in,
    ]);
    \Log::info('youtube response', [
        'data' => $tokens,
    ]);
   // return redirect('com.ae.zawed://oauthcallback?success=1');
 //   return redirect()->away($deepLink);
 return view('site.app-pages.success-auth');
    //   return response()->json([
    //         'success' => true,
    //         'access_token' => $tokens['access_token'],
    //         'refresh_token' => $tokens['refresh_token'] ?? null,
    //         'expires_in' => $tokens['expires_in'],
    //         'state'=>$marketerId,  
    //     ]) ;
}

public function refreshTokenIfNeeded($marketersocial)
{
    // إذا لم يقل عن 5 دقائق على الانتهاء → نجدد
    if ($marketersocial->expires_in_date && $marketersocial->expires_in_date->diffInMinutes(now()) <= 10) {

        $clientId     = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type'    => 'refresh_token',
            'refresh_token' => $marketersocial->refresh_token,
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
        ]);

        if ($response->failed()) {
            \Log::error("Google Token Refresh FAILED", $response->json());
            return false;
        }
        $data = $response->json();
        // حدث التوكين ووقت الانتهاء
        $marketersocial->access_token = $data['access_token'];
        $marketersocial->expires_in = $data['expires_in'];
        $marketersocial->expires_in_date = now()->addSeconds($data['expires_in']);
        $marketersocial->save();
        \Log::info("Google Access Token Refreshed Successfully");
        return true;
    }

    return false;
}


}
