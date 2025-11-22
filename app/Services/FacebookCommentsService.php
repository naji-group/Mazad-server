<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class FacebookCommentsService
{
    /**
     * إرجاع مصفوفة تعليقات جديدة مرتبة من الأقدم إلى الأحدث
     * كل عنصر: ['id','from_name','message','time'] (time => Carbon)
     */
    public function getNewComments(string $liveVideoId, string $accessToken, ?string $sinceCommentId = null): array
    {
        $url = "https://graph.facebook.com/v19.0/{$liveVideoId}/comments";
        $res = Http::get($url, [
            'access_token' => $accessToken,
            'order' => 'reverse_chronological', // أحدث أولاً
            'live_filter' => 'stream',
            'limit' => 100,
        ]);

        if ($res->failed()) {
            \Log::error('live chat messages error', ['error' =>  $res->json()]);
            return [];
        }
        \Log::info( ' facebook comment res', $res->json());
        $data = $res->json('data', []);

        // قاعدة: نعيد من الأقدم إلى الأحدث، ونتخطى التعليقات الموجودة بالفعل (حتى comment_id == sinceCommentId)
        $result = [];
        foreach ($data as $item) {
            $id = $item['id'] ?? null;
            $message = $item['message'] ?? '';
            $fromName = $item['from']['name'] ?? ($item['from_name'] ?? 'مستخدم');
            // facebook may have created_time
            $timeStr = $item['created_time'] ?? now()->toIso8601String();
            $time = Carbon::parse($timeStr);

            // If sinceCommentId provided: skip until we reach it; BUT easier: collect only those with id != sinceCommentId and assume API returns newest-first
            if ($sinceCommentId && $id === $sinceCommentId) {
                // we've reached last known comment => break (because data sorted newest-first)
                break;
            }

            $result[] = [
                'id' => $id,
                'from_name' => $fromName,
                'message' => $message,
                'time' => $time,
            ];
        }

        // currently $result is newest-first => reverse to get oldest->newest
        return array_reverse($result);
    }
}
