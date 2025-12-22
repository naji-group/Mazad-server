<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RestreamStartRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
class RestreamController extends Controller
{
    //
    //   https://api.restream.io/login?response_type=code&client_id=[your client id]&redirect_uri=[your redirect URI]&state=[random opaque token]
    public function login($marketer_id)
    {

        $params = http_build_query([
            'response_type' => 'code',
            'client_id' => "6d7daf26-4ca7-40ed-9dfd-c99761cc6c95",
            'redirect_uri' => 'https://zawed.ae/restream/redirect',
            'state' => $marketer_id,
        ]);

        // ✅ التوجيه مباشرة للرابط الخارجي
        return redirect('https://api.restream.io/login?' . $params);


        /*
        if ($response->failed()) {
            \Log::error("restream Token Refresh FAILED", $response->json());
            return response()->json($response->json());
        }
        $data = $response->json();
        \Log::info('restream login', [
            'data' =>$data ,
        ]);
        return response()->json($data );

        */
    }


    public function redirectfromReastream(Request $request)
    {
        if (isset($request->state) && isset($request->code)) {
            $state = $request->state;
            $code = $request->code;
            $scope = $request->scope;
            \Log::info('restream redirect', [
                'data' => [$state, $code, $scope],
            ]);

            $res = $this->getAccessToken($code);

            return response()->json($res);
        } else {
            $this->tokenRedirect($request);
        }

    }

    public function getAccessToken($code)
    {
        $clientId = "6d7daf26-4ca7-40ed-9dfd-c99761cc6c95";
        $clientSecret = "cbb83a44-566b-4929-86e2-008f12f124e6";
        $redirectUri = 'https://zawed.ae/restream/redirect';

        $response = Http::asForm()
            ->withBasicAuth($clientId, $clientSecret)
            ->post('https://api.restream.io/oauth/token', [
                'grant_type' => 'authorization_code',
                'redirect_uri' => $redirectUri,
                'code' => $code,
                // 'state'=>$state
            ]);

        if ($response->successful()) {
            
            $tokens = $response->json();
            $access_token = $tokens['access_token'];
            $refresh_token = $tokens['refresh_token'];

            //    $data= [
            //         'access_token' => $tokens['access_token'],
            //         'refresh_token' => $tokens['refresh_token'] ?? null,
            //         'expires_in' => $tokens['expires_in'],
            //         'token_type' => $tokens['token_type'],
            //         'scope' => $tokens['scope'] ?? null,
            //     ];
            \Log::info('restream get token ', [
                'data' => $tokens,
            ]);
            return  [
                'access_token' => $access_token,
                'refresh_token' => $refresh_token
            ];
        }
        \Log::error("restream Token FAILED", $response->json());
        // معالجة الخطأ
        return $response->json();
        // return response()->json([
        //     'error' => true,
        //     'status' => $response->status(),
        //     'message' => $response->json('error_description') ?? 'فشل في الحصول على التوكن',
        // ]);
    }
    public function tokenRedirect(Request $request)
    {
        $access_token = $request->access_token;
        $refreshToken = $request->refresh_token;
        //    $code= $request->code;
//    $scope=$request->scope;
        \Log::info('restream token redirect', [
            'data' => $request->all(),
        ]);



        return response()->json([
            'access_token' => $access_token,
            'refreshToken' => $refreshToken
        ]);
    }

    public function getUserProfile(Request $request)
    {
        $accessToken=$request->access_token;
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'application/json',
               
            ])         
            ->get('https://api.restream.io/v2/user/profile');
            
          
            if ($response->successful()) {
                \Log::info('restream profile', [
                    'data' =>$response->json(),
                ]);
                return response()->json([
                    'success' => true,
                    'data' => $response->json(),
                    'status' => $response->status(),
                ]);
            }
            \Log::error("restream profile FAILED", $response->json());
            return response()->json($response->json());            
   
    }


    public function startListener_method($token,$livestream_id )
{

    //$id=auth('api_marketers')->user()->id;
 //   $jwt = auth('api_marketers')->tokenById( $id);
    $processName = "restream-{$livestream_id}";
    $pm2 = trim(shell_exec("which pm2"));

   // $home = '/var/www/.pm2';
    $listenerPath = base_path("tiktok/restream_ws.js");
    $cmd = "HOME=/var/www/.pm2 $pm2 start {$listenerPath} --name {$processName} -- {$livestream_id} {$token} ";
    $output = shell_exec("$cmd 2>&1");
    // تشغيل listener
   // $cmd = "pm2 start tiktok/listener.js --name {$processName} -- {$username} {$livestream_id} {$jwt}";
   // exec($cmd);
     $res_arr=[
        "status" => "started",
      //  "username" => $username,
        "livestream_id" => $livestream_id,
        "process" => $processName,
        'command' => $cmd,
    'output' => $output
    ];
    return $res_arr;
}
public function stopListener_method($livestream_id )
{

    $processName = "restream-{$livestream_id}";

    $pm2 = trim(shell_exec("which pm2"));
    $cmd = "HOME=/var/www/.pm2 $pm2 delete {$processName} 2>&1";
    $output = shell_exec($cmd);



   // exec("pm2 delete {$processName}");
$res_arr=[
    "status" => "stopped",
    "process" => $processName,
    "output"=> $output ,

];
    return $res_arr;

}

public function restream_comment_get(Request $request)
{
    $formdata = $request->all();
    $storrequest = new RestreamStartRequest();
    $validator = Validator::make(
        $formdata,
        $storrequest->rules(),
        $storrequest->messages()
    );

    if ($validator->fails()) {
        \Log::error('tiktok validator error', ['error' => $validator->errors()]);
        return response()->json([
            "success" => 0,
            "message" => $validator->errors()?->first(),
            "data" => $validator->errors()
        ], 422);
    } else {
        // \Log::info('youtube vars validated', [
        //     'data' => $channelName
        // ]);
        // إعداد المتغيرات من env
          try {

            $token=$formdata['access_token'];
            $livestream_id=$formdata['livestream_id'];         
            // جلب التعليقات             
                $res_arr = $this->startListener_method($token,$livestream_id);
                /*
                \Log::info('TikTok comment started', $res_arr);
*/            return response()->json(
                ["success" => 1, "message" => __('api_messages.live created'), "data" => $res_arr]
            );
            // التحقق من النتيجة
   

        } catch (\Exception $e) {
            \Log::error(' start error', ['error' => $e->getMessage()]);
            return response()->json([
                "success" => 0,
                "message" => __('api_messages.Operation failed'),
                "data" => $e->getMessage()
            ], 500);
        }
    }
}

public function restream_comment_stop(Request $request)
{

    /////////////
    //LiveStopPushRequest       
    $formdata = $request->all();
    $storrequest = new RestreamStartRequest();
    $validator = Validator::make(
        $formdata,
        $storrequest->rules(),
        $storrequest->messages()
    );
    if ($validator->fails()) {
        return response()->json(
            ["success" => 0, "message" => $validator->errors()?->first(), "data" => $validator->errors()]
            ,
            422
        );
    } else {
      
        try {
            
            $livestream_id=$formdata['livestream_id']; 
    
            
                $res_arr = $this->stopListener_method($livestream_id);
                /*
                \Log::info('TikTok comment started', $res_arr);
                */
          

            return response()->json(
                [
                    "success" => 1,
                    "message" => __('api_messages.live stoped'),
                    "data" => ['result' => $res_arr]
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => 0,
                    "message" => __('api_messages.Operation failed'),
                    "data" => $e->getMessage()
                ]
                ,
                500
            );
        }
        ////////////////


    }

}


}
