<?php

namespace App\Jobs;


use App\Http\Controllers\Api\YTubeController;
use App\Models\MarketerSocial;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Models\LiveStream;
use App\Models\Social;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\FetchLiveCommentsJob;

class GetFacebookVideoIdJob implements ShouldQueue
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
    protected $accessToken;

    protected $attemptNumber;
    public function __construct(LiveStream $stream, Social $social, MarketerSocial $marketer_social, $attemptNumber = 1)
    {
        $this->stream = $stream;
        $this->social = $social;
        $this->marketer_social = $marketer_social;
        $this->accessToken = $marketer_social->access_token;

        $this->attemptNumber = $attemptNumber;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info(" محاولة رقم  للحصول على livenidId" . $this->attemptNumber);

        // $stream = LiveStream::find($this->streamId);
        if (!$this->stream) {
            Log::error("Stream not found");
            return;
        }

        // 🔹 2. جلب البث المباشر

        $videoId_arr = $this->getFaceLiveChatId();
        // فشل في API
        if (!$videoId_arr['success']) {
            Log::warning(" فشل في جلب liveChatId. المحاولة رقم {$this->attemptNumber}");
            // إذا وصل الحد الأقصى = توقف نهائياً
            if ($this->attemptNumber >= $this->tries) {
                Log::error(" فشل كل مرات. تم إيقاف المحاولات.");
                return;
            }
            // إعادة المحاولة بعد 10 ثواني
            dispatch(new GetFacebookVideoIdJob(
                $this->stream,
                $this->social,
                $this->marketer_social,

                $this->attemptNumber + 1
            ))->delay(now()->addSeconds($this->backoff));
            return;
        }
        //  نجاح — حفظ liveChatId
        $videoId = $videoId_arr['data'];
        $this->stream->facebook_live_video_id = $videoId;
        // $this->stream->facebook_is_active = true;
        $this->stream->save();
        Log::info("✅ تم الحصول على videoId بنجاح: " . $videoId);



        ///////////////     


        //  تشغيل Job التعليقات بعد 1 ثانية
        // \Log::info(' ', [
        //     'data' => 'start job',
        // ]);
        $yt_cntroller = new YTubeController();
        $yt_cntroller->startListener_method($this->stream);
        // FetchLiveCommentsJob::dispatch($this->stream->id, $this->social,$this->marketer_social)
        //     ->delay(now()->addSeconds(1));
    }


    public function getFaceLiveChatId()
    {

        $liveRes = Http::asJson()->post(
            "https://graph.facebook.com/v19.0/{$this->stream->facebook_page_id}/live_videos",
            [
                'access_token' => $this->stream->facebook_page_token,
            ]
        );

        // 🔹 3. في حالة الخطأ

        if ($liveRes->failed()) {

            \Log::error(' liveChatId error', ['error' => $liveRes->json()]);

            //  return response()->json(['error' => 'Failed to fetch liveChatId', 'details' => $videoResponse->json()], 500);
            $res = [
                "success" => 0,
                "message" => "",
                "data" => $$liveRes->json()
            ];
            return $res;
        }

        $liveData = $liveRes->json();
        \Log::info('facebook live_videos', [
            'data' => $liveData
        ]);
        $liveVideoId = $datliveDataa['id'] ?? null;
        $res = [
            "success" => 1,
            "message" => '',
            "data" => $liveVideoId
        ];
        return $res;
    }

}
