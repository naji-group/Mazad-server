<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginMarketerRequest;
use Illuminate\Http\Request;


use App\Http\Requests\Api\Client\StoreClientRequest;
use App\Models\Marketer;
use App\Http\Requests\Api\StoreMarketerRequest;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
//use Illuminate\Support\Facades\Auth;

// use Illuminate\Support\Facades\Hash;
// use Symfony\Component\HttpFoundation\Response;
//use App\Http\Controllers\Api\ClientController;
use Illuminate\Support\Facades\Validator;
//use App\Http\Middleware\Api\AuthenticateClient;
//use JWTAuth;
use Carbon\Carbon;
class MarketerController extends Controller
{

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $formdata = $request->all();
        $storrequest = new LoginMarketerRequest();
        //  $storrequest->request()=$formdata ;
        //   $storrequest=  $formdata ;
        $validator = Validator::make(
            $formdata,
            $storrequest->rules(),
            $storrequest->messages()
        );
        if ($validator->fails()) {

            return response()->json($validator->errors(), 422);

        } else {
            if (isset($formdata['username']) && isset($formdata['password'])) {
                //login by username & password
                $password = $formdata['password'];
                $user = Marketer::where('username', $formdata['username'])
                    ->where('is_active', 1)->first();
                if (!$user) {
                    return response()->json(['error' => 'notexist'], 401);
                }
                if (!Hash::check($password, $user->password)) {
                    return response()->json(['error' => 'notexist'], 401);
                }
                //ok 

                if (!$token = auth('api_marketers')->fromUser($user)) {
                    return response()->json(['error' => 'notexist'], 401);
                }
                // auth('api_marketers')->login($user);
                return response()->json([
                    'token' => $token,
                    // 'user'=> $user,   
                ]);

            }
            // elseif (isset($request['email'])) {

            //     return response()->json('gmaillogin');
            // }
            else {
                return response()->json('not valid');
            }
               }

    }
    //provider

    public function provider_redirect($provider)
    {
        if ($provider == 'google') {
            
            return Socialite::driver($provider)->redirect();
        } else {
            return response()->json(['error' => $provider . 'not allowed'], 422);
        }
    }
    function callback_provider($provider)
    {
        if ($provider == 'google') {
            $googleUser = Socialite::driver($provider)->stateless()->user();
            $dbuser = Marketer::where('email', $googleUser->getEmail())->where('is_active', 1)->first();
            if ($dbuser) {
                $user = Marketer::find($dbuser->id)->update(
                    [
                        'first_name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'provider_token' => $googleUser->token,
                        'provider_refresh_token' => $googleUser->refreshToken,
                        'provider' => 'google',
                        'image' => $googleUser->getAvatar()
                    ]
                );
                if (!$token = auth('api_marketers')->fromUser($user)) {
                    return response()->json(['error' => 'notexist'], 401);
                }
                return response()->json([
                    'token' => $token,
                    // 'user'=> $user,   
                ]);
            } else {
                return response()->json(['error' => $provider . 'notexist'], 401);
            }
        } else {
            return response()->json(['error' => $provider . 'not allowed'], 422);
        }

    }
    // public function register(Request $request)
    // {

    //     $formdata = $request->all();
    //     // $cnum ="";
    //     // $mnum = "";
    //     // if(isset($formdata["country_num"])){
    //     //   $cnum = $formdata["country_num"];
    //     // }
    //     // if(isset($formdata["mobile_num"])){
    //     //   $mnum = $formdata["mobile_num"];
    //     // }  
    //     //  $file=  $formdata ->file('image');
    //     $storrequest = new StoreMarketerRequest();
    //     //  $storrequest->request()=$formdata ;
    //     //   $storrequest=  $formdata ;
    //     $validator = Validator::make(
    //         $formdata,
    //         $storrequest->rules(),
    //         $storrequest->messages()
    //     );
    //     if ($validator->fails()) {

    //         return response()->json($validator->errors());


    //     } else {


    //         $newObj = new Marketer();
    //         //    $birthdate= Carbon::create($formdata["birthdate"])->format('Y-m-d');

    //         $newObj->username = $formdata["username"];

    //         $newObj->email = $formdata["email"];

    //         // $newObj->first_name=$formdata['first_name'] ;
    //         // $newObj->last_name=$formdata['last_name'] ;
    //         // $newObj->username=$formdata['username'] ;
    //         // $newObj->password=$formdata['password'] ;
    //         $newObj->is_active = 1;

    //         // $newObj->image=$formdata['image'] ;
    //         // $newObj->provider=$formdata['provider'] ;
    //         // $newObj->provider_user_id=$formdata['provider_user_id'] ;
    //         // $newObj->social_id=$formdata['social_id'] ;

    //         $newObj->save();
    //         //  $newObj->is_active = 0;

    //         // $newObj= $clintCont->addUser( $newObj);
    //         //  if( isset($formdata["image"]))
    //         //  {
    //         // if ($filerequest->hasFile('image')) {
    //         //     $file= $filerequest->file('image');
    //         //     $clintCont->storeImage( $file,$newObj->id);
    //         // }
    //         //    }
    //         // return response()->json(['formdata' => $formdata ]);
    //         // return response()->json(['userName' => $formdata["userName"]]);
    //         return response()->json($newObj->id);
    //     }





    // }


    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth('api_marketers')->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout_client()
    {
        $user_id = auth('api_marketers')->user()->id;
        Marketer::find($user_id)->update([
            'token' => '',
        ]);
        auth('api_marketers')->logout();

        return response()->json('ok');
    }
    public function logout()
    {
        auth('api_marketers')->logout();

        return response()->json('Success');
    }



    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api_marketers')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api_marketers')->factory()->getTTL() * 60
        ]);
    }
    protected function respondTokenwithExpire($token)
    {
        return response()->json([
            ' token' => $token,
            'expires_in' => auth('api_marketers')->factory()->getTTL() * 60
        ]);
    }


}
