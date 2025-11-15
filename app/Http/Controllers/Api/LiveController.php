<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LiveCreateInstagramRequest;
use App\Http\Requests\Api\LiveCreateRequest;
use App\Http\Requests\Api\LiveEndFacebookRequest;
use App\Http\Requests\Api\LiveEndInstagramRequest;
use App\Http\Requests\Api\LiveEndRequest;
use App\Http\Requests\Api\LiveStartPushRequest;
use App\Http\Requests\Api\LiveStartRequest;
use App\Http\Requests\Api\LiveStartTiktokRequest;
use App\Http\Requests\Api\LiveStopPushRequest;
use App\Http\Requests\Api\LiveStopTiktokRequest;
use App\Jobs\GetYoutubeLiveChatIdJob;
use App\Jobs\SendMarketerNotification;
use App\Models\Livevar;
use Google\Service\Datastream\Stream;
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
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
class LiveController extends Controller
{
    protected static $ffmpegProcess = null;

    public function generate_live_token(Request $request)
    {
        $ctrlr = new AgoraController();
        try {
            $resArr = $ctrlr->generateToken();

            $livestream = new LiveStream();
            $livestream->marketer_id = auth('api_marketers')->user()->id;

            $livestream->is_active = 1;
            $livestream->save();
            $resArr['agora_live_id'] = $livestream->id;
            return response()->json(
                ["success" => 1, "message" => __('api_messages.live created'), "data" => $resArr]
            );
        } catch (\Exception $e) {
            \Log::error(' live_token error', ['error' => $e->getMessage()]);
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
    }
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
            $description = $request->input('description', 'Streaming live');
            try {
                // 🔹 1. الحصول على الصفحات التابعة للمستخدم
                $pagesRes = Http::get("https://graph.facebook.com/v19.0/me/accounts", [
                    'access_token' => $fbToken,
                ]);
                $pages = $pagesRes->json();
                if (empty($pages['data']) || count($pages['data']) === 0) {
                    return response()->json(
                        [
                            "success" => 0,
                            "message" => __('api_messages.No pages found'),
                            "data" => 'No pages found for this user.'
                        ]
                        ,
                        404
                    );
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
                        [
                            "success" => 0,
                            "message" => __('api_messages.live create failed'),
                            "data" => $liveData['error']['message'] ?? 'Failed to create Facebook Live.'
                        ]
                        ,
                        500
                    );
                }

                $livevar = Livevar::updateOrCreate(
                    ['marketer_id' => auth('api_marketers')->user()->id, 'live_video_id' => $liveData['id']],
                    [
                        'first_value' => $pageId,
                        'second_value' => $pageToken,
                        'is_active' => 1,
                        'social' => 'facebook',
                    ]
                );

                //بدء جلب التعليقات
                $stream = LiveStream::find($request->input('agora_live_id'));
                $social = Social::where('code', 'facebook')->first();
                $marketer_social = MarketerSocial::where('marketer_id', auth('api_marketers')->user()->id)->where('social_id', $social->id)->first();

                $stream->facebook_live_video_id = $liveData['id'] ?? null;
                $stream->facebook_access_token = $marketer_social->access_token;
                $stream->facebook_is_active = true;
                $stream->save();
                //start job
                // جدولة job يبدأ فورًا ويعيد جدولة نفسه كل 10 ثواني
                FetchLiveCommentsJob::dispatch($stream->id, $social)->delay(now()->addSeconds(1));

                \Log::info('facebook', [
                    'data' => $liveData,
                ]);
                /*
                'facebook_live_video_id' => $formdata['facebook_live_video_id'] ?? null,
                'facebook_access_token' => $formdata['facebook_access_token'] ?? null,
                */
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
                    [
                        "success" => 0,
                        "message" => __('api_messages.Operation failed'),
                        "data" => $e->getMessage()
                    ]
                    ,
                    500
                );
            }

        }
    }

    /**
     * إنهاء بث مباشر موجود facebook
     */
    public function end_facebook_live(Request $request)
    {
        $formdata = $request->all();
        $storrequest = new LiveEndFacebookRequest();
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
            $liveVideoId = $request->live_video_id;

            // $pageToken = $request->page_token;
            try {
                $livevar = Livevar::where('marketer_id', auth('api_marketers')->user()->id)->where('live_video_id', $liveVideoId)->first();
                $pageToken = $livevar->second_value;
                $response = Http::post("https://graph.facebook.com/v19.0/{$liveVideoId}", [
                    'end_live_video' => true,
                    'access_token' => $pageToken,
                ]);
                if ($response->failed()) {
                    return response()->json(
                        [
                            "success" => 0,
                            "message" => __('api_messages.Operation failed'),
                            "data" => $response->json()
                        ],
                        500
                    );
                }

                //end job             
                $stream = LiveStream::find($request->input('agora_live_id'));
                $stream->facebook_is_active = false;
                $stream->save();
                return response()->json(
                    ["success" => 1, "message" => __('api_messages.live stoped'), "data" => $response->json()]
                );
            } catch (\Exception $e) {
                // أي خطأ آخر غير متوقع
                return response()->json(
                    [
                        "success" => 0,
                        "message" => __('api_messages.Operation failed'),
                        "data" => $e->getMessage()
                    ],
                    500
                );
            }
        }
    }
    //instagram
    private function checkFfmpeg()
    {
        try {
            $process = new Process(['ffmpeg', '-version']);
            $process->run();

            if ($process->isSuccessful()) {
                return true;
            }

            return false;
        } catch (\Throwable $e) {
            return false;
        }
    }
    public function create_instagram_live(Request $request)
    {
        $formdata = $request->all();
        $storrequest = new LiveCreateInstagramRequest();
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

            // فحص وجود FFmpeg أولاً
            if (!$this->checkFfmpeg()) {
                //FFmpeg not found on this server. Please install it first.
                return response()->json(
                    [
                        "success" => 0,
                        "message" => __('api_messages.Operation failed'),
                        "data" => ["error" => "FFmpeg not installed"]

                    ],
                    500
                );
            }

            if (self::$ffmpegProcess) {
                //Live already running.
                return response()->json(
                    [
                        "success" => 0,
                        "message" => __('api_messages.live already created'),
                        "data" => []
                    ],
                    400
                );
            }

            $agoraUrl = $request->agora_url;
            $instagramUrl = $request->instagram_url;
            $instagramKey = $request->instagram_key;

            $fullRtmp = "{$instagramUrl}/{$instagramKey}";

            $ffmpegArgs = [
                'ffmpeg',
                '-re',
                '-i',
                $agoraUrl,
                '-c:v',
                'libx264',
                '-preset',
                'veryfast',
                '-maxrate',
                '3000k',
                '-bufsize',
                '6000k',
                '-c:a',
                'aac',
                '-b:a',
                '128k',
                '-ar',
                '44100',
                '-f',
                'flv',
                $fullRtmp,
            ];

            try {

                //  بناء الأمر الكامل لتشغيله في الخلفية
                // $cmd = implode(' ', $ffmpegArgs) . " > /dev/null 2>&1 & echo $!";
                // $pid = exec($cmd);
                // if ($pid) {
                //     // حفظ PID لتتمكن من إيقاف البث لاحقًا
                //     cache(['instagram_ffmpeg_pid' => $pid], now()->addHours(2));
                //     return response()->json(
                //         ["success" => 1, 
                //         "message" => __('api_messages.live created'), 
                //         "data" =>['pid' => $pid]
                //         ] );
                // }else{
                //     return response()->json([
                //         "success" => 0,
                //         "message" => __('api_messages.Operation failed'),
                //         "data" => []
                //     ], 500);
                // }
                $process = new Process($ffmpegArgs);
                $process->setTimeout(0);
                $process->start();

                self::$ffmpegProcess = $process;

                //Live started successfully.
                return response()->json(
                    [
                        "success" => 1,
                        "message" => __('api_messages.live created'),
                        "data" => ['pid' => $process->getPid()]
                    ]
                );
            } catch (ProcessFailedException $e) {
                // Failed to start stream.
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

        }
    }

    /**
     * إنهاء بث مباشر موجود facebook
     */
    public function end_instagram_live(Request $request)
    {
        // $formdata = $request->all();
        // $storrequest = new LiveEndInstagramRequest();
        // $validator = Validator::make(
        //     $formdata,
        //     $storrequest->rules(),
        //     $storrequest->messages()
        // );
        // if ($validator->fails()) {
        //     return response()->json(
        //         ["success" => 0, "message" => $validator->errors()?->first(), "data" => $validator->errors()]
        //         ,422);
        // } else {   
        try {
            if (!self::$ffmpegProcess) {
                //No live stream running.
                return response()->json(['message' => 'No live stream running.'], 400);
            }

            $process = self::$ffmpegProcess;

            if ($process->isRunning()) {
                if (!defined('SIGINT')) {
                    define('SIGINT', 2); // ✅ دعم Windows
                }

                $process->stop(3, SIGINT);
            }

            self::$ffmpegProcess = null;

            return response()->json(
                ["success" => 1, "message" => __('api_messages.live stoped'), "data" => []]
            );

        } catch (\Exception $e) {
            //Error stopping live stream.            
            return response()->json([
                "success" => 0,
                "message" => __('api_messages.Operation failed'),
                "data" => $e->getMessage()
            ], 500);
        }
        // }      
    }

    // end Instgram
    public function youtube_push(Request $request)
    {
        //  التحقق من المدخلات

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
           // $accessToken = $formdata['youtube_access_token'];
            \Log::info('youtube vars validated', [
                'data' => $channelName . '-' .
                    $uid . '-' .
                    $youtubeStreamKey,
            ]);

            // إعداد المتغيرات من env
            $appId = config('services.agora.app_id');
            $customerKey = config('services.agora.customer_key');
            $customerSecret = config('services.agora.customer_secret');
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
                \Log::info('youtube', [
                    'data' => 'sendto:' . 'https://api.agora.io',
                ]);

                // التحقق من النتيجة
                if ($response->failed()) {

                    \Log::error('youtube error', ['error' => $response->json()]);
                    return response()->json(
                        [
                            "success" => 0,
                            "message" => __('api_messages.live create failed'),
                            "data" => $response->json()
                        ]
                        ,
                        500
                    );
                }
                //بدء جلب التعليقات
                $stream = LiveStream::find($formdata['agora_live_id']);
                $social = Social::where('code', 'youtube')->first();

               $marketer_social = MarketerSocial::where('marketer_id', auth('api_marketers')->user()->id)->where('social_id', $social->id)->first();

              //  $liveChatId = null;
                //start
                $accessToken= $marketer_social->access_token;
                $accessToken_arr = $this->getYoutubechanneld($accessToken);
                if (!$accessToken_arr['success']) {
                    return response()->json(
                        $accessToken_arr,
                        400
                    );
                }
                $channelId = $accessToken_arr['data'];
                $stream->youtube_access_token = $accessToken;
                $stream->youtube_channel_id = $channelId;               
                $stream->save();
                
                GetYoutubeLiveChatIdJob::dispatch($stream, $social,$marketer_social,$channelId)->delay(now()->addSecond());

                // $videoId_arr = $this->getYoutubeVideoId($channelId);
                // if (!$videoId_arr['success']) {
                //     return response()->json(
                //         $videoId_arr,
                //         400
                //     );
                // }
                // $videoId = $videoId_arr['data'];
                // $stream->youtube_video_id = $videoId;
              
                // $liveChatId_arr = $this->getYoutubeLiveChatId($accessToken, $videoId);
                // if (!$liveChatId_arr['success']) {
                //     return response()->json(
                //         $liveChatId_arr,
                //         400
                //     );
                // }
                // $liveChatId = $liveChatId_arr['data'];              
                // $stream->youtube_live_chat_id = $liveChatId;


             //   $stream->save();
                //end
                //    //
                //     $response = Http::get('https://www.googleapis.com/youtube/v3/liveBroadcasts', [
                //         'part' => 'snippet',
                //         'broadcastStatus' => 'active',
                //         'key' => config('services.youtube.key'),

                //     ]);
                //     \Log::info('youtube', [
                //         'data' => 'sendto:'.'https://www.googleapis.com/youtube/v3',
                //     ]);

                //     \Log::info('youtube response', [
                //         'data' => $response->json(),
                //     ]);

                //     if ($response->successful() && isset($response->json()['items'][0]['snippet']['liveChatId'])) {
                //         $liveChatId = $response->json()['items'][0]['snippet']['liveChatId'];

                //         \Log::info('youtube response', [
                //             'data' => $response->json(),
                //         ]);

                //     } else {
                //         $liveChatId = null; // لا يوجد بث مباشر حالياً
                //         \Log::info('youtube response', [
                //             'data' => "no response and liveChatId = null",
                //         ]);
                //     }
//


                // $stream->youtube_live_chat_id = $liveChatId ?? null;
                // $stream->youtube_access_token = $formdata['youtube_access_token'];
            //    $stream->youtube_is_active = true;
              
                //start job
                // \Log::info('youtube', [
                //     'data' => 'start job',
                // ]);

                // جدولة job يبدأ فورًا ويعيد جدولة نفسه كل 10 ثواني
            //    FetchLiveCommentsJob::dispatch($stream->id, $social)->delay(now()->addSeconds(1));
                //                

                return response()->json(
                    ["success" => 1, "message" => __('api_messages.live created'), "data" => ['converter' => $response->json()]]
                );
            } catch (\Exception $e) {
                \Log::error('youtube error', ['error' => $e->getMessage()]);
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
        }

    }

    public function youtube_push_test(Request $request)
    {
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
            $accessToken = $formdata['youtube_access_token'];
            \Log::info('youtube vars validated', [
                'data' => $channelName . '-' .
                    $uid . '-' .
                    $youtubeStreamKey,
            ]);

            // إعداد المتغيرات من env
            $appId = config('services.agora.app_id');
            $customerKey = config('services.agora.customer_key');
            $customerSecret = config('services.agora.customer_secret');
            $region = env('AGORA_REGION', 'na');// or ap, eu, cn

            //return  response()->json($appId);
            // RTMP URL ليوتيوب
            try {
                // $rtmpUrl = "rtmp://a.rtmp.youtube.com/live2/{$youtubeStreamKey}";

                // // الجسم المرسل إلى Agora API
                // $body = [
                //     'converter' => [
                //         'name' => "push-{$channelName}-" . time(),
                //         'rawOptions' => [
                //             'rtcChannel' => $channelName,
                //             'rtcStreamUid' => $uid,
                //         ],
                //         'rtmpUrl' => $rtmpUrl,
                //         // 'idleTimeout' => 3600, // اختياري
                //     ],
                // ];
                // تهيئة الـ Basic Auth
                // $authHeader = 'Basic ' . base64_encode("{$customerKey}:{$customerSecret}");
                // إرسال الطلب إلى Agora API
                // $response = Http::withHeaders([
                //     'Authorization' => $authHeader,
                //     'Content-Type' => 'application/json',
                // ])->post("https://api.agora.io/{$region}/v1/projects/{$appId}/rtmp-converters", $body);

                // \Log::info('youtube', [
                //     'data' => 'sendto:' . 'https://api.agora.io',
                // ]);

                // التحقق من النتيجة
                // if ($response->failed()) {

                //     \Log::error('youtube error', ['error' => $response->json()]);


                //     return response()->json(
                //         [
                //             "success" => 0,
                //             "message" => __('api_messages.live create failed'),
                //             "data" => $response->json()
                //         ]
                //         ,
                //         500
                //     );
                // }

             


                //بدء جلب التعليقات
                $stream = LiveStream::find($formdata['agora_live_id']);
                $social = Social::where('code', 'youtube')->first();

               $marketer_social = MarketerSocial::where('marketer_id', auth('api_marketers')->user()->id)->where('social_id', $social->id)->first();
              
               $liveChatId = null;
                //start
                $accessToken= $marketer_social->access_token;
                $accessToken_arr = $this->getYoutubechanneld($accessToken);
                if (!$accessToken_arr['success']) {
                    return response()->json(
                        $accessToken_arr,
                        400
                    );
                }
                // $channelId = $accessToken_arr['data'];
                // $videoId_arr = $this->getYoutubeVideoId($channelId);
                // if (!$videoId_arr['success']) {
                //     return response()->json(
                //         $videoId_arr,
                //         400
                //     );
                // }
                // $videoId = $videoId_arr['data'];
                // $liveChatId_arr = $this->getYoutubeLiveChatId($accessToken, $videoId);
                // if (!$videoId_arr['success']) {
                //     return response()->json(
                //         $liveChatId_arr,
                //         400
                //     );
                // }
                // $liveChatId = $liveChatId_arr['data'];
                // $stream->youtube_access_token = $accessToken;
                // $stream->youtube_channel_id = $channelId;
                // $stream->youtube_video_id = $videoId;
                // $stream->youtube_live_chat_id = $liveChatId;
                // $stream->save();
                //end
                //    //
                //     $response = Http::get('https://www.googleapis.com/youtube/v3/liveBroadcasts', [
                //         'part' => 'snippet',
                //         'broadcastStatus' => 'active',
                //         'key' => config('services.youtube.key'),

                //     ]);
                //     \Log::info('youtube', [
                //         'data' => 'sendto:'.'https://www.googleapis.com/youtube/v3',
                //     ]);

                //     \Log::info('youtube response', [
                //         'data' => $response->json(),
                //     ]);

                //     if ($response->successful() && isset($response->json()['items'][0]['snippet']['liveChatId'])) {
                //         $liveChatId = $response->json()['items'][0]['snippet']['liveChatId'];

                //         \Log::info('youtube response', [
                //             'data' => $response->json(),
                //         ]);

                //     } else {
                //         $liveChatId = null; // لا يوجد بث مباشر حالياً
                //         \Log::info('youtube response', [
                //             'data' => "no response and liveChatId = null",
                //         ]);
                //     }
//


                // $stream->youtube_live_chat_id = $liveChatId ?? null;
                // $stream->youtube_access_token = $formdata['youtube_access_token'];
                // $stream->youtube_is_active = true;
                // $stream->save();
                $channelId = $accessToken_arr['data'];
                $stream->youtube_access_token = $accessToken;
                $stream->youtube_channel_id = $channelId;               
                $stream->save();
                
                GetYoutubeLiveChatIdJob::dispatch($stream, $social,$marketer_social,$channelId)->delay(now()->addSecond());


                //start job
                \Log::info('youtube', [
                    'data' => 'start job',
                ]);

                // جدولة job يبدأ فورًا ويعيد جدولة نفسه كل 10 ثواني
               // FetchLiveCommentsJob::dispatch($stream->id, $social)->delay(now()->addSeconds(1));
                //                

                return response()->json(
                    ["success" => 1, "message" => __('api_messages.live created'), "data" => ['channelId' =>$channelId]]
                );
            } catch (\Exception $e) {
                \Log::error('youtube error', ['error' => $e->getMessage()]);
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
            $appId = config('services.agora.app_id');
            $customerKey = config('services.agora.customer_key');
            $customerSecret = config('services.agora.customer_secret');
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
                        [
                            "success" => 0,
                            "message" => __('api_messages.Operation failed'),
                            "data" => $response->json()
                        ]
                        ,
                        500
                    );
                }
                //success
                //end job             
                $stream = LiveStream::find($request->input('agora_live_id'));
                $stream->youtube_is_active = false;
                $stream->save();
                return response()->json(
                    [
                        "success" => 1,
                        "message" => __('api_messages.live stoped'),
                        "data" => ['result' => $response->json()]
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
        }


    }
    //TIKtok
    public function tiktok_push(Request $request)
    {

        $formdata = $request->all();
        $storrequest = new LiveStartTiktokRequest();
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
            $channel = $request->channel;
            $rtmpUrl = $request->rtmpUrl;
            $uid = $request->uid ?? '1';

            $appId = config('services.agora.app_id');
            $customerId = config('services.agora.customer_key');
            $customerCertificate = config('services.agora.customer_secret');
            $baseUrl = "https://api.agora.io/v1/apps";
            //  return response()->json([ "a"=>$appId, $customerId, $customerCertificate]);
            try {
                // 1️⃣ Generate resourceId
                $resourceResponse = Http::withBasicAuth($customerId, $customerCertificate)
                    ->post("$baseUrl/$appId/cloud_recording/acquire", [
                        'cname' => $channel,
                        'uid' => $uid,
                        'clientRequest' => new \stdClass(),
                    ]);

                if (!$resourceResponse->successful()) {
                    //Failed to acquire resourceId
                    return response()->json(
                        [
                            "success" => 0,
                            "message" => __('api_messages.faild'),
                            "data" => $resourceResponse->json()
                        ]
                        ,
                        500
                    );
                }

                $resourceId = $resourceResponse->json('resourceId');

                // 2️⃣ Start streaming (RTMP push)
                $startResponse = Http::withBasicAuth($customerId, $customerCertificate)
                    ->post("$baseUrl/$appId/cloud_recording/resourceid/$resourceId/mode/live/start", [
                        'cname' => $channel,
                        'uid' => $uid,
                        'clientRequest' => [
                            'streamUrl' => $rtmpUrl,
                            'recordingConfig' => [
                                'maxIdleTime' => 30,
                                'streamTypes' => 2,
                                'channelType' => 1,
                                'videoStreamType' => 0,
                            ],
                            'transcodingConfig' => [
                                'width' => 720,
                                'height' => 1280,
                                'fps' => 30,
                                'bitrate' => 1200,
                                'mixedVideoLayout' => 1,
                            ],
                        ],
                    ]);

                if (!$startResponse->successful()) {
                    //Failed to start streaming
                    return response()->json(
                        [
                            "success" => 0,
                            "message" => __('api_messages.live create failed'),
                            "data" => $startResponse->json()
                        ]
                        ,
                        500
                    );
                }

                $sid = $startResponse->json('sid');

                \Log::info('TikTok RTMP started', [
                    'channel' => $channel,
                    'resourceId' => $resourceId,
                    'sid' => $sid,
                ]);
                return response()->json(
                    [
                        "success" => 1,
                        "message" => __('api_messages.live created'),
                        "data" => [
                            'resourceId' => $resourceId,
                            'sid' => $sid,
                            'serverResponse' => $startResponse->json()
                        ]
                    ]
                );
            } catch (\Exception $e) {
                \Log::error(' Tiktok RTMP start error', ['error' => $e->getMessage()]);
                return response()->json(
                    [
                        "success" => 0,
                        "message" => __('api_messages.Operation failed'),
                        "data" => $e->getMessage()
                    ],
                    500
                );
            }
        }



    }
    //stop
    public function tiktok_stop_push(Request $request)
    {

        $formdata = $request->all();
        $storrequest = new LiveStopTiktokRequest();
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
            $channel = $request->channel;
            $resourceId = $request->resourceId;
            $sid = $request->sid;
            $uid = $request->uid ?? '1';

            $appId = config('services.agora.app_id');
            $customerId = config('services.agora.customer_key');
            $customerCertificate = config('services.agora.customer_secret');
            $baseUrl = "https://api.agora.io/v1/apps";

            try {
                // 2️⃣ Stop RTMP stream
                $stopResponse = Http::withBasicAuth($customerId, $customerCertificate)
                    ->post("$baseUrl/$appId/cloud_recording/resourceid/$resourceId/sid/$sid/mode/live/stop", [
                        'cname' => $channel,
                        'uid' => $uid,
                        'clientRequest' => new \stdClass(),
                    ]);

                if (!$stopResponse->successful()) {
                    //Failed to stop RTMP stream
                    return response()->json(
                        [
                            "success" => 0,
                            "message" => __('api_messages.faild'),
                            "data" => $stopResponse->json()
                        ]
                        ,
                        500
                    );
                }

                \Log::info('RTMP stream stopped successfully', [
                    'channel' => $channel,
                    'resourceId' => $resourceId,
                    'sid' => $sid,
                ]);
                //RTMP stream stopped successfully
                return response()->json(
                    [
                        "success" => 1,
                        "message" => __('api_messages.live stoped'),
                        "data" => $stopResponse->json()
                    ]

                );
            } catch (\Exception $e) {
                \Log::error('Tiktok RTMP stop error', ['error' => $e->getMessage()]);
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

        }



    }
    //End tiktok
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

            // جدولة job يبدأ فورًا ويعيد جدولة نفسه كل 10 ثواني
            FetchLiveCommentsJob::dispatch($stream->id)->delay(now()->addSeconds(1));
            return response()->json(
                [
                    "success" => 1,
                    "message" => __('api_messages.live created'),
                    "data" => ['stream_id' => $stream->id]
                ]
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
                return response()->json([
                    "success" => 0,
                    "message" => __('api_messages.Stream not found'),
                    "data" => []
                ], 500);
            }

            $liveStream->update(['is_active' => false]);

            return response()->json(
                [
                    "success" => 1,
                    "message" => __('api_messages.live stoped'),
                    "data" => []
                ]
            );
        }

    }
    //test
    public function getYoutubeLiveVideoId(Request $request)
    {

        $accessToken = $request->access_token;

        if (!$accessToken) {
            return response()->json(['error' => 'Missing access_token'], 400);
        }


        $response = Http::withToken($accessToken)
            ->get('https://www.googleapis.com/youtube/v3/channels', [
                'part' => 'id',
                'mine' => 'true'
            ]);

        $json = $response->json();

        // التحقق من النتيجة
        if (!$response->successful() || empty($json['items'])) {
            \Log::error(' channel ID error', ['error' => $json]);

            return response()->json([
                'error' => 'Could not fetch channel ID',
                'details' => $json
            ], 400);
        }
        \Log::info('channel id succes', [
            'data' => $response->json(),

        ]);
        // استخراج الـ channel_id
        $channelId = $json['items'][0]['id'];
        //
        $apiKey = config('services.youtube.key2');
        //التاكد
        // $response = Http::get('https://www.googleapis.com/youtube/v3/channels', [
        //     'part' => 'snippet',
        //     'id' => $channelId,
        //     'key' =>  $apiKey
        // ]);
        // $data2 = $response->json();
        // \Log::info('ِAccount by channel succes', [
        //     'data' =>  $data2,

        // ]);

        //




        // طلب البحث عن أي بث مباشر نشط الآن
        $response = Http::get("https://www.googleapis.com/youtube/v3/search", [
            'part' => 'snippet',
            'channelId' => $channelId,
            'eventType' => 'live',
            'type' => 'video',
            'key' => $apiKey
        ]);

        $data = $response->json();

        if (empty($data['items'])) {
            \Log::error(' videoId ID error', ['error' => $data]);
            return response()->json([
                'success' => false,
                'message' => 'لا يوجد بث مباشر حاليًا',
                'data' => $data,
                // 'data2' =>  $data2,
            ], 404);
        }

        \Log::info('video_id succes', [
            'data' => $data,

        ]);
        $videoId = $data['items'][0]['id']['videoId'];

        // return response()->json([
        //     'success' => true,
        //     'video_id' => $videoId
        // ]);




        // 🔹 الخطوة 1: جلب liveChatId من الفيديو
        $videoResponse = Http::withToken($accessToken)
            ->get('https://www.googleapis.com/youtube/v3/videos', [
                'part' => 'liveStreamingDetails',
                'id' => $videoId,
            ]);

        if ($videoResponse->failed()) {
            \Log::error(' liveChatId error', ['error' => $videoResponse->json()]);
            return response()->json(['error' => 'Failed to fetch liveChatId', 'details' => $videoResponse->json()], 500);
        }

        $videoData = $videoResponse->json();
        \Log::info('ِAccount by channel succes', [
            'data' => $videoData,

        ]);
        $liveChatId = $videoData['items'][0]['liveStreamingDetails']['activeLiveChatId'] ?? null;

        if (!$liveChatId) {
            \Log::error(' liveChatId error', ['error' => 'No active live chat found for this video']);
            return response()->json(['error' => 'No active live chat found for this video'], 404);
        }

        // 🔹 الخطوة 2: جلب الرسائل من live chat
        $chatResponse = Http::withToken($accessToken)
            ->get('https://www.googleapis.com/youtube/v3/liveChat/messages', [
                'liveChatId' => $liveChatId,
                'part' => 'id,snippet,authorDetails',
                'maxResults' => 50,
            ]);

        if ($chatResponse->failed()) {
            \Log::error('live chat messages error', ['error' => $chatResponse->json()]);
            return response()->json(['error' => 'Failed to fetch live chat messages', 'details' => $chatResponse->json()], 500);
        }
        \Log::info('live chat messages', [
            'data' => $chatResponse->json(),

        ]);
        $messages = collect($chatResponse->json()['items'] ?? [])->map(function ($msg) {
            $snippet = $msg['snippet'];
            $author = $msg['authorDetails'];
            return [
                'author' => $author['displayName'],
                'profile_image' => $author['profileImageUrl'],
                'message' => $snippet['displayMessage'],
                'published_at' => $snippet['publishedAt'],
            ];
        });

        return response()->json([
            'success' => true,
            'video_id' => $videoId,
            'live_chat_id' => $liveChatId,
            'comments' => $messages,
            //  'comments' => $chatResponse->json()
        ]);


    }

    public function getYoutubechanneld($accessToken)
    {
        $response = Http::withToken($accessToken)
            ->get('https://www.googleapis.com/youtube/v3/channels', [
                'part' => 'id',
                'mine' => 'true'
            ]);

        $json = $response->json();

        // التحقق من النتيجة
        if (!$response->successful() || empty($json['items'])) {
            \Log::error(' channel ID error', ['error' => $json]);
            $res = [
                "success" => 0,
                "message" => __('api_messages.youtube live failed'),
                "data" => $json
            ];
            return $res;
        }
        \Log::info('channel id succes', [
            'data' => $response->json(),

        ]);
        // استخراج الـ channel_id
        $channelId = $json['items'][0]['id'];
        $res = [
            "success" => 1,
            "message" => '',
            "data" => $channelId
        ];
        return $res;
    }

    public function getYoutubeVideoId($channelId)
    {
        $apiKey = config('services.youtube.key2');
        // طلب البحث عن أي بث مباشر نشط الآن
        $response = Http::get("https://www.googleapis.com/youtube/v3/search", [
            'part' => 'snippet',
            'channelId' => $channelId,
            'eventType' => 'live',
            'type' => 'video',
            'key' => $apiKey
        ]);
        $data = $response->json();
        if (empty($data['items'])) {
            \Log::error(' videoId ID error', ['error' => $data]);
            $res = [
                "success" => 0,
                "message" => __('api_messages.youtube live failed'),
                "data" => $data
            ];
            return $res;
            // return response()->json([
            //     'success' => false,
            //     'message' => 'لا يوجد بث مباشر حاليًا',
            //     'data' =>  $data,
            //    // 'data2' =>  $data2,
            // ], 404);
        }
        \Log::info('video_id success', [
            'data' => $data,

        ]);
        $videoId = $data['items'][0]['id']['videoId'];
        $res = [
            "success" => 1,
            "message" => '',
            "data" => $videoId
        ];
        return $res;
    }
    public function getYoutubeLiveChatId($accessToken, $videoId)
    {

        // 🔹 الخطوة 1: جلب liveChatId من الفيديو
        $videoResponse = Http::withToken($accessToken)
            ->get('https://www.googleapis.com/youtube/v3/videos', [
                'part' => 'liveStreamingDetails',
                'id' => $videoId,
            ]);

        if ($videoResponse->failed()) {
            \Log::error(' liveChatId error', ['error' => $videoResponse->json()]);

            //  return response()->json(['error' => 'Failed to fetch liveChatId', 'details' => $videoResponse->json()], 500);
            $res = [
                "success" => 0,
                "message" => __('api_messages.youtube live failed'),
                "data" => $videoResponse->json()
            ];
            return $res;
        }
        $videoData = $videoResponse->json();
        \Log::info('ِAccount by channel succes', [
            'data' => $videoData,
        ]);
        $liveChatId = $videoData['items'][0]['liveStreamingDetails']['activeLiveChatId'] ?? null;
        if (!$liveChatId) {
            \Log::error(' liveChatId error', ['error' => 'No active live chat found for this video']);
            //    return response()->json(['error' => 'No active live chat found for this video'], 404);

            $res = [
                "success" => 0,
                "message" => __('api_messages.youtube live failed'),
                "data" => ''
            ];
            return $res;
        }
        $res = [
            "success" => 1,
            "message" => '',
            "data" => $liveChatId
        ];
        return $res;
    }

    public function getYoutubeLiveChatMessages($accessToken, $videoId, $liveChatId)
    {
        // 🔹 الخطوة 2: جلب الرسائل من live chat
        $chatResponse = Http::withToken($accessToken)
            ->get('https://www.googleapis.com/youtube/v3/liveChat/messages', [
                'liveChatId' => $liveChatId,
                'part' => 'id,snippet,authorDetails',
                'maxResults' => 200,
            ]);

        if ($chatResponse->failed()) {
            \Log::error('live chat messages error', ['error' => $chatResponse->json()]);
            return response()->json(['error' => 'Failed to fetch live chat messages', 'details' => $chatResponse->json()], 500);
        }
        \Log::info('live chat messages', [
            'data' => $chatResponse->json(),

        ]);
        $messages = collect($chatResponse->json()['items'] ?? [])->map(function ($msg) {
            $snippet = $msg['snippet'];
            $author = $msg['authorDetails'];
            return [
                'author' => $author['displayName'],
                'profile_image' => $author['profileImageUrl'],
                'message' => $snippet['displayMessage'],
                'published_at' => $snippet['publishedAt'],
            ];
        });

        return response()->json([
            'success' => true,
            'video_id' => $videoId,
            'live_chat_id' => $liveChatId,
            'comments' => $messages,
            //  'comments' => $chatResponse->json()
        ]);


    }

}
