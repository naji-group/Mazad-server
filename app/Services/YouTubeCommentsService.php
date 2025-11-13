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

        \Log::info(" بدء جلب تعليقات يوتيوب");
       
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

        $items =  $chatResponse->json('items', []);     
        $result = [];
        // foreach ($items as $item) {
        //     $id = $item['id'] ?? null;
        //     $snippet = $item['snippet'] ?? [];
        //     $author = $item['authorDetails']['displayName'] ?? 'unknown';
        //     $message = $snippet['displayMessage'] ?? '';
        //     $timeStr = $snippet['publishedAt'] ?? now()->toIso8601String();
        //     $time = Carbon::parse($timeStr);
        //     \Log::info( 'comment sinceMessageId',['data'=>['sinceMessageId'=>$sinceMessageId,'id'=>$id]]);
        //     if ($sinceMessageId && ($id === $sinceMessageId)) {
        //         break;
        //     }
        //     $result[] = [
        //         'id' => $id,
        //         'from_name' => $author,
        //         'message' => $message,
        //         'time' => $time,
        //     ];
        // }

        $foundLast = false;

        foreach ($items as $item) {
            $id = $item['id'] ?? null;
            $snippet = $item['snippet'] ?? [];
            $author = $item['authorDetails']['displayName'] ?? 'unknown';
            $message = $snippet['displayMessage'] ?? '';
            $timeStr = $snippet['publishedAt'] ?? now()->toIso8601String();
            $time = Carbon::parse($timeStr);
    
            // ✅ إذا لدينا sinceMessageId نبدأ حفظ ما بعده فقط
            if ($sinceMessageId) {
                if ($foundLast) {
                    // أضف التعليق بعد أن تجاوزنا آخر تعليق معروف
                    $result[] = [
                        'id' => $id,
                        'from_name' => $author,
                        'message' => $message,
                        'time' => $time,
                    ];
                } elseif ($id === $sinceMessageId) {
                    // عندما نجد آخر تعليق محفوظ، نبدأ بعدها
                    $foundLast = true;
                }
            } else {
                // في أول مرة نحفظ جميع التعليقات
                $result[] = [
                    'id' => $id,
                    'from_name' => $author,
                    'message' => $message,
                    'time' => $time,
                ];
            }
        }
        \Log::info( 'comment arr',  $result);
        return $result;
       // return array_reverse($result);
      
    }
}
