<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FetchYoutubeCommentRequest;



use App\Models\LiveComment;
use App\Models\LiveStream;
// use App\Models\LivestreamSocial;
use App\Models\Marketer;
use App\Models\MarketerSocial;
use App\Models\Social;
use App\Notifications\MarketerNotification;
use App\Services\FacebookCommentsService;
use Illuminate\Http\Request;
// use App\Jobs\SendMarketerNotification;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
// use Illuminate\Support\Facades\Http;

use Carbon\CarbonTimeZone;
class FaceController extends Controller
{
    // تشغيل listener


    // public function startListener(Request $request)
    // {
    //     // return exec('whoami');
    //     // return shell_exec("ls -la");
    //     $request->validate([

    //         'livestream_id' => 'required',
    //     ]);
    //     $username = $request->username;
    //     $livestream_id = $request->livestream_id;
    //     try {
    //         $res_arr = $this->startListener_method( $livestream_id);
    //         // توليد JWT للتوثيق
    //         return response()->json($res_arr);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             "success" => 0,
    //             "message" => __('api_messages.Operation failed'),
    //             "data" => $e->getMessage()
    //         ], 500);
    //     }

    // }

    // إيقاف listener
    // public function stopListener(Request $request)
    // {
    //     $request->validate([

    //         'livestream_id' => 'required',
    //     ]);
    //     $res_arr = $this->stopListener_method( $request->livestream_id);
    //     return response()->json($res_arr);
    // }


    public function startListener_method($stream)
    {
        $livestream_id = $stream->id;
        $user_id = $stream->marketer_id;
        //  $user_id = auth('api_marketers')->user()->id;
        $jwt = auth('api_marketers')->tokenById($user_id);
        $processName = "facebook-{$livestream_id}";
        $pm2 = trim(shell_exec("which pm2"));

        // $home = '/var/www/.pm2';
        $listenerPath = base_path("tiktok/facelistener.js");
        $cmd = "HOME=/var/www/.pm2 $pm2 start {$listenerPath} --name {$processName} -- {$livestream_id} {$jwt}";
        $output = shell_exec("$cmd 2>&1");
        // تشغيل listener
        // $cmd = "pm2 start tiktok/listener.js --name {$processName} -- {$username} {$livestream_id} {$jwt}";
        // exec($cmd);
        $res_arr = [
            "status" => "started",
            //  "username" => $username,
            "livestream_id" => $livestream_id,
            "process" => $processName,
            'command' => $cmd,
            'output' => $output
        ];
        return $res_arr;
    }
    public function stopListener_method($livestream_id)
    {

        $processName = "facebook-{$livestream_id}";
        $pm2 = trim(shell_exec("which pm2"));
        $cmd = "HOME=/var/www/.pm2 $pm2 delete {$processName} 2>&1";
        $output = shell_exec($cmd);
        // exec("pm2 delete {$processName}");
        $res_arr = [
            "status" => "stopped",
            "process" => $processName,
            "output" => $output,

        ];
        return $res_arr;

    }

    public function fetch_comment(Request $request)
    {
        $formdata = $request->all();
        $storrequest = new FetchYoutubeCommentRequest();
        $validator = Validator::make(
            $formdata,
            $storrequest->rules(),
            $storrequest->messages()
        );
        if ($validator->fails()) {
            \Log::error(' validator error', ['error' => $validator->errors()]);
            return response()->json(
                ["success" => 0, "message" => $validator->errors()?->first(), "data" => $validator->errors()]
                ,
                422
            );
        } else {

            $platform_code = 'facebook';
            $stream = LiveStream::find($request->livestream_id);

            if ($stream->facebook_is_active && $stream->is_active) {
                try {

                    $fbService = new FacebookCommentsService();
                    $social = Social::where('code', $platform_code)->first();
                    $marketer_social = MarketerSocial::where('marketer_id', $stream->marketer_id)->where('social_id', $social->id)->first();


                    //تعليقات يو تيوب
                    //$isfirstComment=  LiveComment::where('live_stream_id', $stream->id)->first();
                    $lastYt = LiveComment::where('live_stream_id', $stream->id)
                        ->where('social_id', $social->id)
                        ->orderByDesc('comment_time')
                        ->value('comment_id');
                    $newSaved = []; // نجمع التعليقات الجديدة لإرسال اشعار واحد مرتب
                    //جلب اخر توكن
                    //   $marketer_social->refresh();
                    // التحقق من تاريخ صلاحية التوكن
                    // $is_refresh = $this->refreshTokenIfNeeded($marketer_social);
                    // if ($is_refresh) {
                    //     // جلب التوكن الحديث بعد الحصول عليه من غوغل
                    //     $marketer_social->refresh();
                    // }
                    // 3) جلب تعليقات يوتيوب جديدة
                    if ($stream->facebook_live_video_id && $stream->facebook_access_token) {

                        //   \Log::info(" بدء جلب تعليقات يوتيوب الجديدة");
                        $fbComments = $fbService->getNewComments($stream->facebook_live_video_id, $stream->facebook_access_token, $lastYt);

                        foreach ($fbComments as $c) {

                            $comment_time = $c['time']->toDateTimeString();
                            $comment = LiveComment::updateOrCreate(
                                [
                                    'platform' => 'facebook',
                                    'comment_id' => $c['id'],
                                ],
                                [
                                    'marketer_id' => $stream->marketer_id,
                                    'agora_live_id' => $stream->agora_live_id,
                                    'live_stream_id' => $stream->id,
                                    'platform' => 'facebook',
                                    'comment_id' => $c['id'],
                                    'author_name' => $c['from_name'],
                                    'message' => $c['message'],
                                    'comment_time' => $comment_time,
                                    'social_id' => $social->id,
                                ]
                            );
                            if ($comment->wasRecentlyCreated) {
                                //$newcomment_time =  Carbon::parse($comment_time)->timezone(config('app.default_timezone'))->toIso8601String();
                                $newcomment_time = $this->offset_timezone($comment_time, config('app.default_timezone'));
                                //  $newSaved[] = [
                                $newSaved = [
                                    'platform' => 'facebook',
                                    'comment_id' => $c['id'],
                                    'author_name' => $c['from_name'],
                                    'message' => $c['message'],
                                    'comment_time' => strval($newcomment_time),
                                    'social_id' => strval($social->id),
                                ];
                                // send totification
                                $marketers = Marketer::whereIn('id', [$stream->marketer_id])->get();
                                foreach ($marketers as $marketer) {
                                    $marketer->notify(new MarketerNotification(
                                        '',
                                        '',
                                        $newSaved,
                                        ['database', 'fcm']
                                    ));
                                }

                                // SendMarketerNotification::dispatch(
                                //     [$stream->marketer_id],'','',$newSaved ,['database', 'fcm']);   
                            }

                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('facebook save comment failed: ' . $e->getMessage());

                    return response()->json(["sent" => 2]);
                }
                return response()->json(["sent" => 1]);
            } else {
                return response()->json(["sent" => 0]);
            }

        }
    }

    // public function fetch_statistic(Request $request)
    // {
    //     $formdata = $request->all();
    //     $storrequest = new LiveFetchTiktokStatisticRequest();
    //     $validator = Validator::make(
    //         $formdata,
    //         $storrequest->rules(),
    //         $storrequest->messages()
    //     );
    //     if ($validator->fails()) {
    //         \Log::error(' validator error', ['error' => $validator->errors()]);
    //         return response()->json(
    //             ["success" => 0, "message" => $validator->errors()?->first(), "data" => $validator->errors()]
    //             ,
    //             422
    //         );
    //     } else {
    //         $platform_code = 'tiktok';
    //         $livestream_id = $request->livestream_id;
    //         $type = $request->type;
    //         $count = $request->count ?? 0;
    //         //$stream=LiveStream::find($request->livestream_id);
    //         $social = Social::where('code', $platform_code)->first();
    //         try {
    //             $sts_model = LivestreamSocial::where('live_stream_id', $livestream_id)->where('social_id', $social->id)->first();
    //             if ($sts_model) {
    //                 switch ($type) {
    //                     case 'LIKE':

    //                         $sts_model->likes_count = ($sts_model->likes_count ?? 0) + (int) $count;
    //                         break;
    //                     case 'FOLLOW':
    //                         $sts_model->followers_count = ($sts_model->followers_count ?? 0) + (int) $count;
    //                         break;
    //                     case 'SHARE':
    //                         $sts_model->shares_count = ($sts_model->shares_count ?? 0) + (int) $count;
    //                         break;
    //                     case 'VIEWER':
    //                         $sts_model->views_count = (int) $count;
    //                         break;
    //                     default:
    //                         \Log::info('tiktok sts DEFAULT', [
    //                             'data' => $type,
    //                         ]);
    //                     // Default action if no case matches

    //                 }
    //                 $sts_model->save();
    //             }


    //             \Log::info('tiktok sts ', [
    //                 'data' => $type . "saved",
    //             ]);
    //         } catch (\Exception $e) {
    //             \Log::warning('Tiktok save sts failed: ' . $e->getMessage());
    //             return ["saved" => false];
    //         }
    //         return ["saved" => true];
    //     }
    // }

    // public function refreshTokenIfNeeded($marketersocial)
    // {

    //     $expires = Carbon::parse($marketersocial->expires_in_date);
    //     $res = false;
    //     //هل expires_in_date أقل أو يساوي الآن + 10 دقائق
    //     if ($expires->lte(now()->addMinutes(10))) {
    //         $res = true;
    //     }
    //     // إذا لم يقل عن 10 دقائق على الانتهاء → نجدد
    //     if ($marketersocial->expires_in_date && $res) {

    //         $clientId = config('services.google.client_id');
    //         $clientSecret = config('services.google.client_secret');

    //         $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
    //             'grant_type' => 'refresh_token',
    //             'refresh_token' => $marketersocial->refresh_token,
    //             'client_id' => $clientId,
    //             'client_secret' => $clientSecret,
    //         ]);

    //         if ($response->failed()) {
    //             \Log::error("Google Token Refresh FAILED", $response->json());
    //             return false;
    //         }
    //         $data = $response->json();
    //         // حدث التوكين ووقت الانتهاء
    //         $marketersocial->access_token = $data['access_token'];
    //         $marketersocial->expires_in = $data['expires_in'];
    //         $marketersocial->expires_in_date = now()->addSeconds($data['expires_in']);
    //         $marketersocial->save();
    //         \Log::info("Google Access Token Refreshed Successfully");
    //         return true;
    //     }

    //     return false;
    // }

    public function offset_timezone($time, $newtimezone)
    {

        $timezone = new CarbonTimeZone($newtimezone);
        $utcNow = Carbon::now('UTC');
        $localNow = $utcNow->copy()->setTimezone($timezone);

        // فرق التوقيت بالدقائق
        $diffInMinutes = (int) ($localNow->utcOffset());

        $comment_time = Carbon::parse($time);

        // إضافة فرق التوقيت
        $adjustedCommentTime = $comment_time->addMinutes($diffInMinutes);

        // اختبار النتيجة
        return $adjustedCommentTime;
    }
}
