<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LiveCreateRequest;
use App\Http\Requests\Api\LiveEndRequest;
use App\Http\Requests\Api\LiveStartPushRequest;
use App\Http\Requests\Api\LiveStartRequest;
use App\Http\Requests\Api\LiveStopPushRequest;
use Illuminate\Http\Request;
use App\Models\MarketerSocial;
use App\Models\Marketer;
use App\Models\LiveStream;
//use App\Models\LiveComment;
use App\Models\Social;
use App\Http\Requests\Api\TokenSaveRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Jobs\FetchLiveCommentsJob;
class LiveController extends Controller
{
    public function savefaceaccesstoken(Request $request)
    {
        $formdata = $request->all();
        $storrequest = new TokenSaveRequest();

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
            $id = $formdata['id'];
            $access = $formdata['access_token'];
            $social = Social::where('code', 'facebook')->first();
            if ($social) {
                $record = MarketerSocial::firstOrNew([
                    'marketer_id' => $id,
                    'social_id' => $social->id,
                ]);
                $record->access_token = $access;
                $record->save();

            }
            return response()->json(
                ["success" => 1, "message" => __('api_messages.form.success save'), "data" => []]
            );
        }
    }
    public function create_facebook_live(Request $request)
    {
        $formdata = $request->all();
        $storrequest = new LiveCreateRequest();
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
            $fbToken = $request->input('fbToken');
            $title = $request->input('title', 'My Laravel Live Stream');
            $description = $request->input('description', 'Streaming live from Laravel 🎥');    
            try {
                // 🔹 1. الحصول على الصفحات التابعة للمستخدم
                $pagesRes = Http::get("https://graph.facebook.com/v19.0/me/accounts", [
                    'access_token' => $fbToken,
                ]);    
                $pages = $pagesRes->json();    
                if (empty($pages['data']) || count($pages['data']) === 0) {
                    return response()->json(
                        [ "success" => 0, "message" => __('api_messages.No pages found'), 
                       "data" => 'No pages found for this user.']
                       , 404);
                }    
                // 🔹 نختار الصفحة الأولى (أو يمكنك تعديل الكود لاختيار صفحة محددة)
                $page = $pages['data'][0];
                $pageId = $page['id'];
                $pageToken = $page['access_token'];    
                // 🔹 2. إنشاء البث المباشر
                $liveRes = Http::asJson()->post("https://graph.facebook.com/v19.0/{$pageId}/live_videos", [
                    'status' => 'LIVE_NOW',
                    'title' => $title,
                    'description' => $description,
                    'access_token' => $pageToken,
                ]);    
                $liveData = $liveRes->json();    
                // 🔹 3. في حالة الخطأ
                if ($liveRes->failed()) {
                    return response()->json(
                       [ "success" => 0, "message" => __('api_messages.live create failed'), 
                       "data" => $liveData['error']['message'] ?? 'Failed to create Facebook Live.']
                       , 500);
                }    
                // 🔹 4. الإرجاع
                return response()->json(
                    ["success" => 1, "message" => __('api_messages.live created'), "data" => $liveData]
                //     [
                //     'page_id' => $pageId,
                //     'page_name' => $page['name'],
                //     'live_video_id' => $liveData['id'] ?? null,
                //     'stream_url' => $liveData['stream_url'] ?? null,
                //     'secure_stream_url' => $liveData['secure_stream_url'] ?? null,
                // ]
            );    
            } catch (\Exception $e) {
                return response()->json(
                    [ "success" => 0, "message" => __('api_messages.Operation failed'), 
                    "data" => $e->getMessage()]                     
                    , 500);
            }
           
        }
    }

    public function youtube_push(Request $request){
         // ✅ التحقق من المدخلات
       
        $formdata = $request->all();
        $storrequest = new LiveStartPushRequest();
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
            $channelName = $formdata['channelName'];
            $uid = $formdata['uid'];
            $youtubeStreamKey = $formdata['youtubeStreamKey'];
    
            // إعداد المتغيرات من env
            $appId = env('AGORA_APP_ID');
            $customerKey = env('AGORA_CUSTOMER_KEY');
            $customerSecret = env('AGORA_CUSTOMER_SECRET');
            $region = env('AGORA_REGION', 'na');// or ap, eu, cn
    //return  response()->json($appId);
            // RTMP URL ليوتيوب
            try { 
            $rtmpUrl = "rtmp://a.rtmp.youtube.com/live2/{$youtubeStreamKey}";
    
            // الجسم المرسل إلى Agora API
            $body = [
                'converter' => [
                    'name' => "push-{$channelName}-" . time(),
                    'rawOptions' => [
                        'rtcChannel' => $channelName,
                        'rtcStreamUid' => $uid,
                    ],
                    'rtmpUrl' => $rtmpUrl,
                    // 'idleTimeout' => 3600, // اختياري
                ],
            ];    
            // تهيئة الـ Basic Auth
            $authHeader = 'Basic ' . base64_encode("{$customerKey}:{$customerSecret}");    
            // إرسال الطلب إلى Agora API
            $response = Http::withHeaders([
                'Authorization' => $authHeader,
                'Content-Type' => 'application/json',
            ])->post("https://api.agora.io/{$region}/v1/projects/{$appId}/rtmp-converters", $body);
    
            // التحقق من النتيجة
            if ($response->failed()) {
                return response()->json(
                    [ "success" => 0, "message" => __('api_messages.live create failed'), 
                    "data" => $response->json()]
                    , 500);
            }    
            return response()->json(
                ["success" => 1, "message" => __('api_messages.live created'), "data" => ['converter' => $response->json()]]
          );
        } catch (\Exception $e) {
            return response()->json(
                [ "success" => 0, "message" => __('api_messages.Operation failed'), 
                "data" => $e->getMessage()]                     
                , 500);
        }
        }
      
    }

       /**
     * إيقاف البث (حذف الـ RTMP Converter)
     */
    public function youtube_stop_push(Request $request)
    {
        //LiveStopPushRequest       
        $formdata = $request->all();
        $storrequest = new LiveStopPushRequest();
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
            $converterId = $formdata['converterId'];
            $appId = env('AGORA_APP_ID');
            $customerKey = env('AGORA_CUSTOMER_KEY');
            $customerSecret = env('AGORA_CUSTOMER_SECRET');
            $region = env('AGORA_REGION', 'na');
            try { 
            $authHeader = 'Basic ' . base64_encode("{$customerKey}:{$customerSecret}");
    
            // DELETE إلى Agora API
            $response = Http::withHeaders([
                'Authorization' => $authHeader,
                'Content-Type' => 'application/json',
            ])->delete("https://api.agora.io/{$region}/v1/projects/{$appId}/rtmp-converters/{$converterId}");
    
            if ($response->failed()) {
                return response()->json(                    
                [ "success" => 0, "message" => __('api_messages.Operation failed'), 
                "data" => $response->json()]                     
                , 500 );
            }    
            //success
            return response()->json(
                ["success" => 1, "message" => __('api_messages.live stoped'),
                 "data" => ['result' => $response->json()]]
       );
    } catch (\Exception $e) {
        return response()->json(
            [ "success" => 0, "message" => __('api_messages.Operation failed'), 
            "data" => $e->getMessage()]                     
            , 500);
    }
        }

       
    }   
     // Start Comment
    public function start(Request $request)
    {
        
        $formdata = $request->all();
        $storrequest = new LiveStartRequest();
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
            $stream = LiveStream::updateOrCreate(
                ['agora_live_id' => $formdata['agora_live_id']],
                [
                    'marketer_id' => $formdata['marketer_id'],
                    'is_active' => 1,
                    'youtube_live_chat_id' => $formdata['youtube_live_chat_id'] ?? null,
                    'youtube_access_token' => $formdata['youtube_access_token'] ?? null,
                    'facebook_live_video_id' => $formdata['facebook_live_video_id'] ?? null,
                    'facebook_access_token' => $formdata['facebook_access_token'] ?? null,
                ]
            );
            \Log::info(" بدءعمل جلب تعليقات");  
            // جدولة job يبدأ فورًا ويعيد جدولة نفسه كل 10 ثواني
            FetchLiveCommentsJob::dispatch($stream->id)->delay(now()->addSeconds(1));
            return response()->json(
                ["success" => 1, "message" => __('api_messages.live created'),
                 "data" => ['stream_id' => $stream->id]]
       );
            
        }
 

        // احفظ أو حدّث السجل
     
    }

    public function endLiveStream(Request $request)
    {
     
        $formdata = $request->all();
        $storrequest = new LiveEndRequest();
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
            $liveStream = LiveStream::where('agora_live_id', $request->agora_live_id)->first();
    
            if (!$liveStream) {
                return response()->json(  [ "success" => 0, "message" => __('api_messages.Stream not found'), 
                "data" =>[]  ], 500);
            }
        
            $liveStream->update(['is_active' => false]);
        
            return response()->json( 
            ["success" => 1, "message" => __('api_messages.live stoped'),
            "data" => []]);
        }
       
    
     
    }
     
}
