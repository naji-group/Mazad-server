<?php
namespace App\Jobs;

use App\Models\LiveStream;
use App\Models\LiveComment;
use App\Models\Marketer;
use App\Models\MarketerSocial;
use App\Models\Social;
use App\Services\FacebookCommentsService;
use App\Services\YouTubeCommentsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class FetchLiveCommentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $streamId;
public Social $social;
protected MarketerSocial $marketer_social;
    // You can inject services via container in handle()
    public function __construct(int $streamId,Social $social,MarketerSocial $marketer_social)
    {
        $this->streamId = $streamId;
        $this->social=$social;
        $this->marketer_social=$marketer_social;
    }

    public function handle(FacebookCommentsService $fbService, YouTubeCommentsService $ytService)
    {
        \Log::info(" بدء جلب تعليقات".$this->social->code);  
        $stream = LiveStream::find($this->streamId);     
        if (!$stream || !$stream->is_active) {
            \Log::info(" البث رقم {$this->streamId} غير نشط، تم إيقاف الـ Job.");
            return;
        }

        $marketerId = $stream->marketer_id;
if($this->social->code=="facebook"){
    // 1) آخر comment_id لكل منصة محفوظ في قاعدة البيانات لنفس agora_live_id
    $lastFb = LiveComment::where('live_stream_id', $stream->id)
    ->where('social_id', $this->social->id)
    ->orderByDesc('comment_time')
    ->value('comment_id');

       // 2) جلب تعليقات فيسبوك جديدة
       if ($stream->facebook_live_video_id && $stream->facebook_access_token && $stream->facebook_is_active) {
        \Log::info(" بدء جلب تعليقات فايسبوك الجديدة");
        try {
            $fbComments = $fbService->getNewComments($stream->facebook_live_video_id, $stream->facebook_access_token, $lastFb);

            foreach ($fbComments as $c) {
                // حفظ فقط إذا لم يكن موجوداً (unique constraint on platform/comment_id)
                try {
                    $comment = LiveComment::updateOrCreate( [
                        'platform'   => 'facebook',
                        'comment_id' => $c['id'],
                    ],
                    [
                  
                        'marketer_id' => $marketerId,
                        'agora_live_id' => $stream->agora_live_id,
                        'live_stream_id'=> $stream->id,
                        'platform' => 'facebook',
                        'comment_id' => $c['id'],
                        'author_name' => $c['from_name'],
                        'message' => $c['message'],
                        'comment_time' => $c['time']->toDateTimeString(),
                        'social_id'=>$this->social->id,
                    ]);
                    if ($comment->wasRecentlyCreated) {
                    $newSaved[] = [
                        'platform'=>'facebook',
                        'comment_id'=>$c['id'],
                        'author_name'=>$c['from_name'],
                        'message'=>$c['message'],
                        'comment_time'=>$c['time']->toIso8601String(),
                        'social_id'=>$this->social->id,
                    ];
                }
                } catch (\Exception $e) {
                    // قد يكون التعليق موجود مسبقًا بسبب سباق/مكرّر -> تجاهل
                    Log::warning('FB save comment failed: '.$e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error('FB fetch error: '.$e->getMessage());
        }

              // 4) إذا وجدنا تعليقات جديدة -> نرسل إشعار عبر FCM إلى firebase_token في جدول marketers
              if (!empty($newSaved)) {
                // رتب من الأحدث إلى الأقدم قبل الإرسال
                // usort($newSaved, function($a,$b){
                //     return strcmp($b['comment_time'], $a['comment_time']);
                // });
                    // احصل على firebase_token(s) للمسوق


                SendMarketerNotification::dispatch(
                    [$stream->marketer_id],'','',$newSaved ,['fcm']);         
            }
            
        if ($stream->fresh()->is_active) {
            dispatch(new self($this->streamId,$this->social,$this->marketer_social))->delay(now()->addSeconds(10));
            }
    }
   

}else if($this->social->code=="youtube"){

    //تعليقات يو تيوب
  //$isfirstComment=  LiveComment::where('live_stream_id', $stream->id)->first();
 
 
    $lastYt = LiveComment::where('live_stream_id', $stream->id)
    ->where('social_id', $this->social->id)
                ->orderByDesc('comment_time')
                ->value('comment_id');
 

    $newSaved = []; // نجمع التعليقات الجديدة لإرسال اشعار واحد مرتب
    //جلب اخر توكن
    $this->marketer_social->refresh();
    // التحقق من تاريخ صلاحية التوكن
  $is_refresh=  $this->refreshTokenIfNeeded($this->marketer_social);
 
 if($is_refresh){
    // جلب التوكن الحديث بعد الحصول عليه من غوغل
    $this->marketer_social->refresh();
 }
    // 3) جلب تعليقات يوتيوب جديدة
    if ($stream->youtube_live_chat_id &&  $this->marketer_social->access_token) {
        try {
            \Log::info(" بدء جلب تعليقات يوتيوب الجديدة");
            $ytComments = $ytService->getNewComments($stream->youtube_live_chat_id,$this->marketer_social->access_token, $lastYt);

            foreach ($ytComments as $c) {
                try {
                    $comment_time=$c['time']->toDateTimeString();
                    $comment = LiveComment::updateOrCreate( [
                        'platform'   => 'youtube',
                        'comment_id' => $c['id'],
                    ],
                    [
                        'marketer_id' => $marketerId,
                        'agora_live_id' => $stream->agora_live_id,
                        'live_stream_id'=> $stream->id,
                        'platform' => 'youtube',
                        'comment_id' => $c['id'],
                        'author_name' => $c['from_name'],
                        'message' => $c['message'],
                        'comment_time' => $comment_time,
                        'social_id'=>$this->social->id,
                    ]);
                    if ($comment->wasRecentlyCreated) {
                        $comment_time =  Carbon::parse($comment_time)->timezone(config('app.default_timezone'));
                  //  $newSaved[] = [
                        $newSaved = [
                        'platform'=>'youtube',
                        'comment_id'=>$c['id'],
                        'author_name'=>$c['from_name'],
                        'message'=>$c['message'],
                        'comment_time'=>$comment_time,
                        'social_id'=>strval($this->social->id),
                    ];
                    SendMarketerNotification::dispatch(
                        [$stream->marketer_id],'','',$newSaved ,['database', 'fcm']);   
                }
                } catch (\Exception $e) {
                    Log::warning('YT save comment failed: '.$e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error('YT fetch error: '.$e->getMessage());
        }


      // 4) إذا وجدنا تعليقات جديدة -> نرسل إشعار عبر FCM إلى firebase_token في جدول marketers
      if (!empty($newSaved)) {
        // رتب من الأحدث إلى الأقدم قبل الإرسال
        // usort($newSaved, function($a,$b){
        //     return strcmp($b['comment_time'], $a['comment_time']);
        // });
            // احصل على firebase_token(s) للمسوق

            //test
        // SendMarketerNotification::dispatch(
        //     [$stream->marketer_id],'','',[$newSaved] ,['database', 'fcm']);         
    }
    // \Log::info('youtube Notification sent ', [
    //     'data' =>['newSaved'=>$newSaved],
    // ]);
 if ($stream->fresh()->is_active) {
        dispatch(new self($this->streamId,$this->social,$this->marketer_social))->delay(now()->addSeconds(10));
        }

    }   
}
        // 5) إعادة جدولة نفس الـ Job بعد 10 ثواني طالما السجل لا يزال موجود (يحاكي polling كل 10s)
        // (يمكنك تغيير المنطق لإيقافه عندما ينتهي البث)
       
    }

    public function refreshTokenIfNeeded($marketersocial)
{

    $expires = Carbon::parse($marketersocial->expires_in_date);
    $res=false;
    //هل expires_in_date أقل أو يساوي الآن + 10 دقائق
    if ($expires->lte(now()->addMinutes(10))) {
        $res=true;
    }
    // إذا لم يقل عن 10 دقائق على الانتهاء → نجدد
    if ($marketersocial->expires_in_date && $res) {

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

    // protected function sendFcmNotification(array $tokens, array $comments)
    // {
    //     // أبسط طريقة: استخدام Firebase legacy HTTP v1 (server key) أو استخدام مكتبة kreait
    //     // هنا مثال باستخدام الـ HTTP endpoint القديم (تأكد من وضع SERVER_KEY في env)
    //     $serverKey = config('services.firebase.server_key'); // ضع المفتاح في services.php/env

    //     $dataPayload = [
    //         'comments' => $comments // سترسل كل التعليقات كمصفوفة في الـ data
    //     ];

    //     $body = [
    //         'registration_ids' => $tokens,
    //         'data' => $dataPayload,
    //         'priority' => 'high',
    //     ];

    //     $res = Http::withHeaders([
    //         'Authorization' => 'key '.$serverKey,
    //         'Content-Type' => 'application/json',
    //     ])->post('https://fcm.googleapis.com/fcm/send', $body);

    //     if ($res->failed()) {
    //         \Log::error('FCM send failed: '.$res->body());
    //     }
    // }
}
