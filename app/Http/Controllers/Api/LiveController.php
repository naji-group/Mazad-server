<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LiveCreateRequest;
use Illuminate\Http\Request;
use App\Models\MarketerSocial;
use App\Models\Marketer;
use App\Models\Social;
use App\Http\Requests\Api\TokenSaveRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
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


    

     
}
