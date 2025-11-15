<?php

namespace App\Jobs;

 
use App\Models\MarketerSocial;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Models\LiveStream;
use App\Models\Social;
 
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http; 
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use  App\Jobs\FetchLiveCommentsJob;
 
class GetYoutubeLiveChatIdJob implements ShouldQueue
{
     
    use InteractsWithQueue, Queueable, SerializesModels;
    /**
     * Create a new job instance.
     */    
     public $tries = 15;          // أقصى عدد محاولات
     public $backoff = 10;       // تأخير إعادة المحاولة (ثواني)
 
     protected $stream;
     protected $social;
     protected $marketer_social;
     protected  $accessToken;
     protected $channelId;
     protected $attemptNumber;
    public function __construct(LiveStream $stream, Social $social,MarketerSocial $marketer_social,$channelId, $attemptNumber = 1)
    {
        $this->stream= $stream;
        $this->social = $social;
        $this->$marketer_social=$marketer_social;
        $this->accessToken=$marketer_social->access_token;
        $this->channelId=$channelId;
        $this->attemptNumber = $attemptNumber;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info(" محاولة رقم  للحصول على liveChatId".$this->attemptNumber);

       // $stream = LiveStream::find($this->streamId);
        if (!$this->stream) {
            Log::error("Stream not found");
            return;
        }
        // $accessToken = $this->social->access_token;

        // $videoId = $this->stream->youtube_video_id;

        // 🔹 استدعاء الإجراء الحالي الذي تملكه في Controller
        // $liveChatIdArr = app('App\Http\Controllers\Api\YoutubeController')
        //     ->getYoutubeLiveChatId($accessToken, $videoId, $this->stream);

            $videoId_arr = $this->getYoutubeVideoId($this->channelId);
              // فشل في API
        if (!$videoId_arr['success']) {
            Log::warning(" فشل في جلب liveChatId. المحاولة رقم {$this->attemptNumber}");
            // إذا وصل الحد الأقصى = توقف نهائياً
            if ($this->attemptNumber >= $this->tries) {
                Log::error(" فشل 5 مرات. تم إيقاف المحاولات.");
                return;
            }
            // إعادة المحاولة بعد 10 ثواني
            dispatch(new GetYoutubeLiveChatIdJob(
         $this->stream, $this->social,$this->marketer_social,$this->channelId,$this->attemptNumber + 1
            ))->delay(now()->addSeconds($this->backoff));
            return;
        }
        //  نجاح — حفظ liveChatId
        $videoId = $videoId_arr['data'];
        $this->stream->youtube_video_id = $videoId;
        Log::info("✅ تم الحصول على videoId بنجاح: ".$videoId);
      //get Live chat id
        $liveChatId_arr = $this->getYoutubeLiveChatId($this->accessToken, $videoId);
        if (!$liveChatId_arr['success']) {
            \Log::error('youtube error', ['error' =>  $liveChatId_arr]);
           return;
        }
        $liveChatId = $liveChatId_arr['data'];              
        $this->stream->youtube_live_chat_id = $liveChatId;
        ///////////////     
        $this->stream->youtube_is_active = true;
        $this->stream->save();
        Log::info("تم الحصول على liveChatId بنجاح: ".$liveChatId);
        //  تشغيل Job التعليقات بعد 1 ثانية
        \Log::info('youtube', [
            'data' => 'start job',
        ]);
        FetchLiveCommentsJob::dispatch($this->stream->id, $this->social,$this->marketer_social)
            ->delay(now()->addSeconds(1));
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

}
