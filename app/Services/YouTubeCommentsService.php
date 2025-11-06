<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class YouTubeCommentsService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.youtube.key'); // used only if needed
    }

    /**
     * إرجاع مصفوفة تعليقات جديدة مرتبة من الأقدم إلى الأحدث
     * كل عنصر: ['id','from_name','message','time']
     */
    public function getNewComments(string $liveChatId, string $accessToken, ?string $sinceMessageId = null): array
    {

        \Log::info(" بدء جلب تعليقات يوتيوب");
        // نستخدم access_token (OAuth) أو API key. هنا نستخدم access_token param.
        $url = "https://www.googleapis.com/youtube/v3/liveChat/messages";
        $res = Http::get($url, [
            'liveChatId' => $liveChatId,
            'part' => 'snippet,authorDetails',
            'access_token' => $accessToken,
            'maxResults' => 200,
        ]);

        if ($res->failed()) {
            \Log::error(' youtubeerror', ['error' => 'https://www.googleapis.com/youtube/v3/liveChat/messages']);
            return [];
        }

        $items = $res->json('items', []);
        //for test
        \Log::info( $items );
        $result = [];
        foreach ($items as $item) {
            $id = $item['id'] ?? null;
            $snippet = $item['snippet'] ?? [];
            $author = $item['authorDetails']['displayName'] ?? 'مستخدم';
            $message = $snippet['displayMessage'] ?? '';
            $timeStr = $snippet['publishedAt'] ?? now()->toIso8601String();
            $time = Carbon::parse($timeStr);

            if ($sinceMessageId && $id === $sinceMessageId) {
                break;
            }

            $result[] = [
                'id' => $id,
                'from_name' => $author,
                'message' => $message,
                'time' => $time,
            ];
        }

        return array_reverse($result);
    }
}
