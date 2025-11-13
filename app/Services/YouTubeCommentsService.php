<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class YouTubeCommentsService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.youtube.key2'); // used only if needed
    }

    /**
     * إرجاع مصفوفة تعليقات جديدة مرتبة من الأقدم إلى الأحدث
     * كل عنصر: ['id','from_name','message','time']
     */
    public function getNewComments(string $liveChatId, string $accessToken, ?string $sinceMessageId = null): array
    {

//new fetch
 

// if ($chatResponse->failed()) {
// \Log::error('live chat messages error', ['error' =>  $chatResponse->json()]);
//   return response()->json(['error' => 'Failed to fetch live chat messages', 'details' => $chatResponse->json()], 500);
// }
// \Log::info('live chat messages', [
// 'data' =>  $chatResponse->json(),

// ]);
// $messages = collect($chatResponse->json()['items'] ?? [])->map(function ($msg) {
//   $snippet = $msg['snippet'];
//   $author = $msg['authorDetails'];
//   return [
//       'author' => $author['displayName'],
//       'profile_image' => $author['profileImageUrl'],
//       'message' => $snippet['displayMessage'],
//       'published_at' => $snippet['publishedAt'],
//   ];
// });

// return response()->json([
//   'success' => true,
//   'video_id' => $videoId,
//   'live_chat_id' => $liveChatId,
//  'comments' => $messages,
// //  'comments' => $chatResponse->json()
// ]);

//end new fetch


        \Log::info(" بدء جلب تعليقات يوتيوب");
        
        // $url = "https://www.googleapis.com/youtube/v3/liveChat/messages";
        // $res = Http::get($url, [
        //     'liveChatId' => $liveChatId,
        //     'part' => 'snippet,authorDetails',
        //     'access_token' => $accessToken,
        //     'maxResults' => 200,
        // ]);

          // 🔹 الخطوة 2: جلب الرسائل من live chat
  $chatResponse = Http::withToken($accessToken)
  ->get('https://www.googleapis.com/youtube/v3/liveChat/messages', [
      'liveChatId' => $liveChatId,
      'part' => 'id,snippet,authorDetails',
      'maxResults' => 200,
  ]);
  if ($chatResponse->failed()) {
    \Log::error('live chat messages error', ['error' =>  $chatResponse->json()]);
    
    return [];
    }
       

        $items =  $chatResponse->json('items', []);//////////////here eeeeeee
        //for test
        \Log::info( $items );
        $result = [];
        foreach ($items as $item) {
            $id = $item['id'] ?? null;
            $snippet = $item['snippet'] ?? [];
            $author = $item['authorDetails']['displayName'] ?? 'unknown';
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
