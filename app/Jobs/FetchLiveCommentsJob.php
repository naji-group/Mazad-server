<?php
namespace App\Jobs;

use App\Models\LiveStream;
use App\Models\LiveComment;
use App\Models\Marketer;
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
    // You can inject services via container in handle()
    public function __construct(int $streamId,Social $social)
    {
        $this->streamId = $streamId;
        $this->social=$social;
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
                    $comment = LiveComment::create([
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
                    $newSaved[] = [
                        'platform'=>'facebook',
                        'comment_id'=>$c['id'],
                        'author_name'=>$c['from_name'],
                        'message'=>$c['message'],
                        'comment_time'=>$c['time']->toIso8601String(),
                        'social_id'=>$this->social->id,
                    ];
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
                usort($newSaved, function($a,$b){
                    return strcmp($b['comment_time'], $a['comment_time']);
                });
                    // احصل على firebase_token(s) للمسوق
                SendMarketerNotification::dispatch(
                    [auth('api_marketers')->user()->id],'','',[$newSaved] ,['fcm']);         
            }

        if ($stream->fresh()->is_active) {
            dispatch(new self($this->streamId,$this->social))->delay(now()->addSeconds(10));
            }
    }
   

}else if($this->social->code=="youtube"){

    //تعليقات يو تيوب
    $lastYt = LiveComment::where('live_stream_id', $stream->id)
    ->where('social_id', $this->social->id)
                ->orderByDesc('comment_time')
                ->value('comment_id');

    $newSaved = []; // نجمع التعليقات الجديدة لإرسال اشعار واحد مرتب

 
    // 3) جلب تعليقات يوتيوب جديدة
    if ($stream->youtube_live_chat_id && $stream->youtube_access_token) {
        try {
            \Log::info(" بدء جلب تعليقات يوتيوب الجديدة");
            $ytComments = $ytService->getNewComments($stream->youtube_live_chat_id, $stream->youtube_access_token, $lastYt);

            foreach ($ytComments as $c) {
                try {
                    $comment = LiveComment::create([
                        'marketer_id' => $marketerId,
                        'agora_live_id' => $stream->agora_live_id,
                        'live_stream_id'=> $stream->id,
                        'platform' => 'youtube',
                        'comment_id' => $c['id'],
                        'author_name' => $c['from_name'],
                        'message' => $c['message'],
                        'comment_time' => $c['time']->toDateTimeString(),
                        'social_id'=>$this->social->id,
                    ]);
                    $newSaved[] = [
                        'platform'=>'youtube',
                        'comment_id'=>$c['id'],
                        'author_name'=>$c['from_name'],
                        'message'=>$c['message'],
                        'comment_time'=>$c['time']->toDateTimeString(),
                        'social_id'=>strval($this->social->id),
                    ];
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
        usort($newSaved, function($a,$b){
            return strcmp($b['comment_time'], $a['comment_time']);
        });
            // احصل على firebase_token(s) للمسوق
        SendMarketerNotification::dispatch(
            [auth('api_marketers')->user()->id],'','',[$newSaved] ,['database', 'fcm']);         
    }
    \Log::info('youtube Notification sent ', [
        'data' =>['newSaved'=>$newSaved],
    ]);

 if ($stream->fresh()->is_active) {
        dispatch(new self($this->streamId,$this->social))->delay(now()->addSeconds(10));
        }

    }

   
}
   

        // 5) إعادة جدولة نفس الـ Job بعد 10 ثواني طالما السجل لا يزال موجود (يحاكي polling كل 10s)
        // (يمكنك تغيير المنطق لإيقافه عندما ينتهي البث)
       
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
