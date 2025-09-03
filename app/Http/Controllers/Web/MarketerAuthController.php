<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

 
use App\Models\Marketer;
 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
 
class MarketerAuthController extends Controller
{

    function callback_provider() {
        $googleUser = Socialite::driver('google')->user();
     
        $user = Marketer::updateOrCreate([
            'provider_user_id' => $googleUser->getId(),
        ], [
            'username' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
            'provider_token' => $googleUser->token,
            'provider_refresh_token' => $googleUser->refreshToken,
            'provider'=>'google',
          'image'=>  $googleUser->getAvatar()
        ]);
      
        Auth::guard('web_marketers')->login($user);
        return redirect()->route('marketer.dashboard');
    }

    public function showLoginForm()
    {
        return view('site.auth.marketer-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('web_marketers')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            return redirect()->intended('/marketer/dashboard');///////////
        }

        return back()->withErrors([
            'email' => 'بيانات الاعتماد هذه لا تتطابق مع سجلاتنا.',
        ]);
    }
    public function show_profile()
    {
        return view('site.auth.marketer-profile');
    }
   
    public function showRegisterForm()
    {
        return view('site.auth.marketer-register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:marketers',
            'password' => 'required|string|min:8|confirmed',
           // 'phone' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $marketer = Marketer::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
          //  'phone' => $request->phone,
        ]);

        Auth::guard('web_marketers')->login($marketer);

        return redirect('/marketer/dashboard');//////////
    }

    public function logout(Request $request)
    {
        Auth::guard('web_marketers')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/marketer/login');
    }
}
