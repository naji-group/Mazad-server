<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        $params = http_build_query([
            'client_id' => config('services.google.client_id'),
            'redirect_uri' =>  config('services.google.redirect_uri')  ,
            'response_type' => 'code',
            'access_type' => 'offline',
            'prompt' => 'consent',
            'scope' => 'https://www.googleapis.com/auth/youtube.force-ssl'
        ]);

        return redirect()->away("https://accounts.google.com/o/oauth2/v2/auth?$params");
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

    $access = $tokens['access_token'];
    $refresh = $tokens['refresh_token'] ?? null;
    $expires_in = $tokens['expires_in'];

    // ✅ Correct deep link format (single slash)
    $deepLink = "com.ae.zawed://oauthcallback?" . http_build_query([
        "access_token" => $access,
        "refresh_token" => $refresh,
        "expires_in" => $expires_in,
    ]);
    \Log::info('youtube', [
        'data' => $tokens,
    ]);
    return redirect()->away($deepLink);
}

}
