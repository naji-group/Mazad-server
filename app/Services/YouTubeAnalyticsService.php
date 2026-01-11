<?php

namespace App\Services;

use Carbon\CarbonInterval;
use Google_Client;
use Google_Service_YouTube;
use Google_Service_YouTubeAnalytics;
use Illuminate\Support\Facades\Log;

class YouTubeAnalyticsService
{
    protected $client;
    protected $youtubeData;
    protected $youtubeAnalytics;
    protected $accessToken;

    public function __construct( $accessToken,$refresh_token)
    {
        $this->accessToken=$accessToken;
        $this->client = new Google_Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect_uri'));
        $this->client->addScope([
            'https://www.googleapis.com/auth/youtube.readonly',
            'https://www.googleapis.com/auth/yt-analytics.readonly',
        ]);
        $this->client->setAccessType('offline');

        // هنا يجب أن يكون لديك token مخزّن مسبقاً
      //  $accessToken = /* جلب token من قاعدة البيانات أو التخزين */;
        $this->client->setAccessToken($this->accessToken);

        if ($this->client->isAccessTokenExpired()) {
            // تجديد الـ token باستخدام refresh token
           // $refreshToken = $this->client->getRefreshToken();
           $refreshToken = $refresh_token;
            // Log::info('YouTube ', [
            //     'refreshToken' =>   $refreshToken,
                
            // ]);
            $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
            // ثم احفظ الـ access token الجديد
        }

        $this->youtubeData = new Google_Service_YouTube($this->client);
        $this->youtubeAnalytics = new Google_Service_YouTubeAnalytics($this->client);
    }

    /**
     * جلب الإحصائيات الأساسية للفيديو
     */
    public function getVideoStats(string $videoId)
    {
        $response = $this->youtubeData->videos->listVideos('statistics,contentDetails', [
            'id' => $videoId,
        ]);

        if (empty($response->getItems())) {
            return null;
        }

        $item = $response->getItems()[0];
        $stats = $item->getStatistics();
        // Log::info('YouTube Stats', [
        //     'VideoStats' => $response->getItems(),
            
        // ]);
        $duration = $item->getContentDetails()->getDuration();
        $interval = CarbonInterval::make($duration);

// نحولها إلى صيغة HH:MM:SS
$formatted = $interval->format('%H:%I:%S');
        return [
            'viewCount' => $stats->getViewCount(),
            'likeCount' => $stats->getLikeCount(),
            'commentCount' => $stats->getCommentCount(),
            'dislikeCount'=>$stats->getDislikeCount(),
            'favoriteCount' =>$stats->getFavoriteCount(),
            'duration' =>$formatted ,
           
        ];
    }

    /**
     * جلب تقرير التحليلات لما بعد البث
     */
    public function getAnalyticsForVideo(string $videoId, string $startDate, string $endDate)
    {
        $response = $this->youtubeAnalytics->reports->query([
            'ids' => 'channel==MINE', // أو channel==MINE حسب نوع الحساب
            'startDate' => $startDate,
            'endDate' => $endDate,
            'metrics' => 'views,averageViewDuration,likes',//subsGained
            'dimensions' => '',
            'filters' => "video==$videoId",
        ]);

        $rows = $response->getRows();
        // Log::info('YouTube Stats', [
        //     'AnalyticsForVideo' =>$rows,
            
        // ]);
        if (empty($rows)) {
            return null;
        }
     
        // مثال: أول صف من النتائج
        $row = $rows[0];

        return [
            'views' => $row[0],
            'averageViewDuration' => $row[1],
           // 'subsGained' => $row[2],
            'likes' => $row[2],
        ];
    }

    // public function refreshTokenIfNeeded($marketersocial)
    // {
    
       
         
     
      
    
    //         $clientId     = config('services.google.client_id');
    //         $clientSecret = config('services.google.client_secret');
    
    //         $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
    //             'grant_type'    => 'refresh_token',
    //             'refresh_token' => $marketersocial->refresh_token,
    //             'client_id'     => $clientId,
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
            
        
    
    //     return false;
    // }
}
