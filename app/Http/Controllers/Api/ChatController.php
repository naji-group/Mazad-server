<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FetchYoutubeCommentRequest;
use App\Http\Requests\Api\LiveFetchTiktokCommentRequest;

use App\Http\Requests\Api\LiveFetchTiktokStatisticRequest;
use App\Models\LiveComment;
use App\Models\LiveStream;
use App\Models\LivestreamSocial;
use App\Models\Marketer;
use App\Models\MarketerSocial;
use App\Models\Social;
use App\Notifications\MarketerNotification;
use App\Services\YouTubeCommentsService;

use Illuminate\Http\Request;
use App\Jobs\SendMarketerNotification;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

use Carbon\CarbonTimeZone;
class ChatController extends Controller
{



    public function updateOverlay($channel, $author_name, $message, $social_id)
    {
        $layout = [
            "canvas" => [
                "width" => 1080,
                "height" => 1920,
                "color" => "#000000"
            ],
            "textOverlay" => [
                [
                    "id" => 1,
                    "x" => 30,
                    "y" => 100,
                    "text" => " {$author_name}: {$message}",
                    "fontSize" => 26,
                    "color" => "#FFFFFF"
                ]
            ]
        ];

        return $this->updateAgoraTranscoding($channel, $layout);
    }

    private function updateAgoraTranscoding($channel, $layout)
    {
        $appId = config('services.agora.app_id');
        $customerKey = config('services.agora.customer_key');
        $customerSecret = config('services.agora.customer_secret');
        $url = "https://api.agora.io/v1/apps/"
            . $appId
            . "/cloud_transcoder/update";

        $payload = [
            "channelName" => $channel,
            "transcodingConfig" => $layout
        ];

        $response = Http::withBasicAuth(
            $customerKey,
            $customerSecret
        )->post($url, $payload);
        if ($response->failed()) {
            \Log::error('overlay error', ['error_response' => $response->json()]);
        }
        return 1;
    }








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
