<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginMarketerRequest;
use App\Http\Requests\Api\LoginMarketerProviderRequest;
use App\Http\Requests\Api\ResetPassRequest;
use App\Http\Controllers\Api\HelpController;
use App\Http\Requests\Api\MarketerSocialRequest;
use App\Http\Requests\Api\ProfileMarketerRequest;
use App\Http\Requests\Api\UpdateSocialMarketerRequest;
use App\Http\Resources\MarketerSocialResource;
use App\Models\MarketerSocial;
use App\Models\Social;
use Illuminate\Http\Request;
use App\Models\Marketer;
use App\Http\Requests\Api\StoreMarketerRequest;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Requests\Api\UpdateMarketerRequest;

use Illuminate\Support\Facades\Storage;
use File;
use Illuminate\Support\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
//use Illuminate\Support\Facades\Auth;

// use Illuminate\Support\Facades\Hash;
// use Symfony\Component\HttpFoundation\Response;
//use App\Http\Controllers\Api\ClientController;
use Illuminate\Support\Facades\Validator;
//use App\Http\Middleware\Api\AuthenticateClient;
//use JWTAuth;
use App\Http\Resources\MarketerProfileResource;
use App\Http\Requests\Api\DeleteMarketerRequest;
use Illuminate\Support\Arr;
use App\Http\Controllers\Api\MailController;
use phpDocumentor\Reflection\Types\Object_;
class MarketerController extends Controller
{

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        //$lang=app()->getLocale();
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

            return response()->json(
                ["success"=>0,"message"=>$validator->errors()?->first(),"data"=>$validator->errors()], 422);

        } else {
            if (isset($formdata['username']) && isset($formdata['password'])) {
                //login by username & password
                $password = $formdata['password'];
                $user = Marketer::where('username', $formdata['username'])
                    ->where('is_active', 1)->first();
                if (!$user) {
                    return response()->json(
                        ["success"=>0,"message"=> __('api_messages.auth.name.fail'),"data"=>[]]                       
                        , 401);
                }
                if (!Hash::check($password, $user->password)) {
                    return response()->json(  
                        ["success"=>0,"message"=> __('api_messages.auth.name.fail'),"data"=>[]] 
                         , 401);
                }
                //ok 
                $user->login_type = 'local';
                $user->save();
                // $user = Marketer::find($user->id)->update(
                //     [                       
                //        'login_type'=>'local',                       
                //     ]
                // );
                if (!$token = auth('api_marketers')->fromUser($user)) {
                    return response()->json( ["success"=>0,"message"=> __('api_messages.auth.fail login'),"data"=>[]], 401);
                }
                // auth('api_marketers')->login($user);
                return response()->json(                    
                    ["success"=>1,"message"=> __('api_messages.auth.login success'),"data"=>[ 'token' => $token]]                      
                 );

            }
            // elseif (isset($request['email'])) {

            //     return response()->json('gmaillogin');
            // }
            else {
                return response()->json(
                    ["success"=>0,"message"=> __('api_messages.auth.name.fail'),"data"=>[]]                       
                    , 401);
            }
        }

    }
    //provider
    function loginprovider(Request $request)
    {
       
        $formdata = $request->all();
        $storrequest = new LoginMarketerProviderRequest();
        //  $storrequest->request()=$formdata ;
        //   $storrequest=  $formdata ;
        $validator = Validator::make(
            $formdata,
            $storrequest->rules(),
            $storrequest->messages()
        );
        if ($validator->fails()) {
            $message="";
if($validator->errors()->keys()[0]=="email"){
 $message=$validator->errors()->first();
}else{
    $message=__('api_messages.auth.fail login');
}
            return response()->json(
                ["success"=>0,"message"=>$message,"data"=>$validator->errors()]
                , 422);
        } else {
            $user_email = $formdata['email'];
            // $googleUser = Socialite::driver($provider)->stateless()->user();
            $user = Marketer::where('email', $user_email)->where('is_active', 1)->first();

            if ($user) {
                $user->name = isset($formdata['name']) ? $formdata['name'] : "";
                $user->provider_token = isset($formdata['provider_token']) ? $formdata['provider_token'] : "";
                $user->provider_user_id = isset($formdata['provider_user_id']) ? $formdata['provider_user_id'] : "";
                $user->image = isset($formdata['image']) ? $formdata['image'] : "";
                $user->login_type = 'provider';
                $user->provider = 'google';
                $user->save();
                //  $user = Marketer::find($dbuser->id)->update(
                //      [
                //          'name' => $formdata['name'],                        
                //         // 'email' => $googleUser->getEmail(),
                //          'provider_token' =>  isset($formdata['provider_token'])?$formdata['provider_token']:"",  
                //          'provider_user_id' => isset( $formdata['provider_user_id'])?$formdata['provider_user_id']:"",                        
                //          'image' => isset($formdata['image'])?$formdata['image']:"",
                //          'login_type'=>'provider',
                //          'provider' => 'google',
                //      ]
                //  );
                if (!$token = auth('api_marketers')->fromUser($user)) {
                   
                        return response()->json( ["success"=>0,"message"=> __('api_messages.auth.fail login'),"data"=>[]], 401);       
                }
                return response()->json(
                    ["success"=>1,"message"=> __('api_messages.auth.login success'),"data"=>[ 'token' => $token]]                      
             
                     );
            } else {
                return response()->json( ["success"=>0,"message"=> __('api_messages.auth.name.fail'),"data"=>[]]                       
                , 401);
            }
        }
    }

    //Profile
    public function updateprofile(Request $request)
    {
        $formdata = $request->all();
        $storrequest = new UpdateMarketerRequest();

        $validator = Validator::make(
            $formdata,
            $storrequest->rules(),
            $storrequest->messages()
        );
        if ($validator->fails()) {
           
            return response()->json( ["success"=>0,"message"=>__('api_messages.form.fill required'),"data"=>$validator->errors()], 422);
        } else {
            $authuser = auth('api_marketers')->user();
            // if (!($authuser->id == $formdata["id"])) {
                
            //     return response()->json('notexist', 401);
            // } else {
                $authuser->full_name = $formdata["full_name"];


                if (isset($formdata['password'])) {
                    $password = trim($formdata['password']);
                    $authuser->password = bcrypt($password);

                }
                if ($request->hasFile('local_image')) {
                    $file = $request->file('local_image');
                    $this->storeImage($file, $authuser);
                }
                $authuser->save();
                return response()->json(
                    ["success"=>1,"message"=> __('api_messages.form.success save'),"data"=>$authuser->id]                      
                    );
            
        }
    }

    public function storeImage($file, $user)
    {

        $oldimage = $user->local_image;
        //  $oldimagename = basename($oldimage);
        $strgCtrlr = new HelpController();
        $path = $strgCtrlr->path['marketers'];
        //  $oldimagepath =  $oldimagename;
        //save photo

        if ($file !== null) {

            $filename = rand(10000, 99999) . $user->id . ".webp";
            //  $filename = rand(10000, 99999) . $user->id . '.'.$file->getClientOriginalExtension();
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file);
            $image = $image->toWebp(75);
            if (!File::isDirectory(Storage::url('/' . $path))) {
                Storage::makeDirectory('public/' . $path);
            }
            $newpath = $path . '/' . $filename;
            $image->save(storage_path('app/public') . '/' . $newpath);
            //   $url = url('storage/app/public' . '/' . $this->path . '/' . $filename);
            // Expert::find($id)->update([
            //     "image" => $filename
            // ]);
            // Storage::delete("public/" . $oldimage);

            $pathImg = storage_path('app/public/' . $oldimage);
            if (File::exists($pathImg)) {
                File::delete($pathImg);
            }
            //  \Log::debug('pathImg', [
            //         'pathImg' => $pathImg ,


            //     ]);

        }
        $user->local_image = $newpath;
        $user->save();
        //     $filePath = storage_path('app/public/' .$newpath);
        //    $strgCtrlr->changemod($filePath);
        return 1;
    }

    public function getprofile(Request $request)
    {     
        //     $lang=$request->header('lang')?$request->header('lang'):env('APP_LOCALE','ar');
      
        // app()->setLocale($lang);
        // $lang=app()->getLocale();
        $formdata = $request->all();
        $storrequest = new ProfileMarketerRequest();
     
        $validator = Validator::make(
            $formdata,
            $storrequest->rules(),
            $storrequest->messages()
        );
        if ($validator->fails()) {
            // app()->setLocale('ar');
            return response()->json(
                ["success"=>0,"message"=>$validator->errors()?->first(),"data"=>$validator->errors()]
                , 422);
        } else {
            $authuser = auth('api_marketers')->user();
            // if (!($authuser->id == $formdata["id"])) {
            //     return response()->json('notexist', 401);
            // } else {
                $user = Marketer::where('is_active', 1)->where('id', $authuser->id)
                    ->select(
                        'id',
                        'full_name',
                        'login_type',
                        'is_active',
                        //'email',
                        'local_image',
                    )->first();
                if (!$user) {
                    return response()->json( ["success"=>0,"message"=> __('api_messages.user not found'),"data"=>[]]                       
                    , 401);               

                }
                $resuser = new MarketerProfileResource($user);
                return response()->json( 
                    ["success"=>1,"data"=>$resuser,"message"=> __('api_messages.profile sent')]
                    );            
        }
    }
    //social setting

    public function updatesocials(Request $request)
    {
        $formdata = $request->all();
        $storrequest = new UpdateSocialMarketerRequest();

        $validator = Validator::make(
            $formdata,
            $storrequest->rules(),
            $storrequest->messages()
        );
        if ($validator->fails()) {
            return response()->json(
               ["success"=>0,"message"=> __('api_messages.user not found'),"data"=>$validator->errors()]
                , 422);
        } else {
            $authuser = auth('api_marketers')->user();
            // if (!($authuser->id == $formdata["id"])) {
            //     return response()->json('notexist', 401);
            // } else {

                $data = json_decode($request->getContent(), true);
                $dataArr = Arr::except($data, ['id']);
                $socials = Social::where('is_active', 1)->get();
                foreach ($dataArr as $key => $link) {
                    $social = $socials->where('code', $key)->first();
                    if ($social) {
                        $record = MarketerSocial::firstOrNew([
                            'marketer_id' => $authuser->id,
                            'social_id' => $social->id,
                        ]);
                        $record->fill([
                            'link' => $link,
                            'is_active' => $record->exists ? $record->is_active : 1,
                        ])->save();
                    }
                }

                return response()->json(
                    ["success"=>1,"message"=> __('api_messages.form.success save'),"data"=>$authuser->id]                      
                   );
          //  }
        }
    }
    public function getsocials(Request $request)
    {
        $formdata = $request->all();
        $storrequest = new MarketerSocialRequest();

        $validator = Validator::make(
            $formdata,
            $storrequest->rules(),
            $storrequest->messages()
        );
        if ($validator->fails()) {

            return response()->json(
                ["success"=>0,"message"=> __('api_messages.user not found'),"data"=>$validator->errors()] 
                , 422);
        } else {
            $authuser = auth('api_marketers')->user();
            // if (!($authuser->id == $formdata["id"])) {
            //     return response()->json('notexist', 401);
            // } else {
                $msocial = MarketerSocial::with([
                    'social' => function ($q) {
                        $q->where('is_active', 1)
                            ->select(
                                'id',
                                'code',
                                'link',
                                'is_active',
                                'name',
                                'sequence'
                            )->orderBy('sequence');
                    }
                ])->where('marketer_id', $authuser->id) ->get()
                ->sortBy(fn($item) => $item->social->sequence)  
    ->values();
                return response()->json(
                    ["success"=>1,"data"=>MarketerSocialResource::collection($msocial),"message"=> __('api_messages.social info')]
                    
                
                );
            // }
        }
    }
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
    public function logout_marketer()
    {
        $user_id = auth('api_marketers')->user()->id;
        // Marketer::find($user_id)->update([
        //     'token' => '',
        // ]);
        auth('api_marketers')->logout();

        return response()->json(["success"=>1,"message"=>__('api_messages.logout success'),"data"=>[] ]);
    }
    public function logout()
    {
        auth('api_marketers')->logout();

        return response()->json('Success');
    }


    public function deleteaccount(Request $filerequest)
    {
        $formdata = $filerequest->all();
        $storrequest = new DeleteMarketerRequest();
        $validator = Validator::make(
            $formdata,
            $storrequest->rules(),
            $storrequest->messages()
        );
        if ($validator->fails()) {
            return response()->json(
                ["success"=>0,"message"=> __('api_messages.user not found'),"data"=> $validator->errors()]
                , 422);
        } else {

            $id = $formdata['id'];
            $authuser = auth()->user();
            // if (!($authuser->id == $id)) {
            //     return response()->json('notexist', 401);
            // } else {
                // ClientDelOrder::where('client_id', $id)->delete();
                // $client = Client::find($id);
                // $setctrlr = new SettingController();
                // $mailctrlr = new MailController();
                // $delorder = new ClientDelOrder();
                // $delorder->client_id = $id;
                // $delorder->email = $formdata['email'];
                // $delorder->mobile = $formdata['mobile'];
                // $delorder->reason =isset($formdata['reason'])?$formdata['reason']:"-";
                // $delorder->state = 'w';
                // $delorder->save();
                $authuser->is_active = 0;
                $authuser->update([
                    'is_active' => 0,
                ]);

                // Client::find($id)->update([
                //     'is_active' => 0,
                // ]);

                auth('api_marketers')->logout();

                // $admin_email = $setctrlr->findbyname('admin_email')->value;
                // $data = [
                //     'com_title' => config('app.name', 'Rouh'),
                //     'client_name' => $client->user_name,
                //     'client_email' => $delorder->email,
                //     'client_mobile' => $delorder->mobile,
                //     'reason' => $delorder->reason,
                //     'order_id' => $delorder->id,
                // ];
                // //admin
                // if ($admin_email) {
                //     $mailctrlr->send_del_mail($admin_email, $data, 'admin');
                // }
                // // client
                // $mailctrlr->send_del_mail($delorder->email, $data, 'client');
                return response()->json(["success"=>1,"message"=>__('api_messages.delete account success'),"data"=>$id ]);
           // }
        }
    }

    public function resetpassword(Request $request)
    {
        $formdata = $request->all();
        $storrequest = new ResetPassRequest();
        $validator = Validator::make(
            $formdata,
            $storrequest->rules(),
            $storrequest->messages()
        );
        if ($validator->fails()) {

            if($validator->errors()->keys()[0]=="email"){
                $message=$validator->errors()->first();
               }else{
                   $message=__('api_messages.user not found');
               }
            return response()->json(
                ["success"=>0,"message"=>$message,"data"=>$validator->errors()]                 
                , 422);
        } else {

            $id = $formdata['id'];
            $authuser = auth()->user();
            // if (!($authuser->id == $id)) {
            //     return response()->json('notexist', 401);
            // } else {               
                $mailctrlr  =new MailController();
               $res=   $mailctrlr->send_reset_mail($formdata['email'], $id );
                return response()->json(
                    ["success"=>1,"message"=> __('api_messages.reset order'),"data"=>$id]              
                    );
           // }
        }
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
