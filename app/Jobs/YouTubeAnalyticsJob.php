<?php

namespace App\Jobs;

use App\Http\Controllers\Api\HelpController;
use App\Models\LiveStream;
use App\Models\LivestreamSocial;
use App\Models\MarketerSocial;
use App\Models\Social;
use App\Services\YouTubeAnalyticsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class YouTubeAnalyticsJob implements ShouldQueue
{
    use Queueable;
    public LivestreamSocial $sts_model;
    /**
     * Create a new job instance.
     */
    public function __construct(LivestreamSocial $sts_model)
    {
      $this->sts_model=$sts_model;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try{
            $this->get_YT_analytic_method( );
        }   catch (\Exception $e) {
            \Log::error('error', ['error' => $e->getMessage()]);
            
        }

    
    }

    public function get_YT_analytic_method( )
    {       
        $livestream_id=$this->sts_model->live_stream_id;
     $livestream=LiveStream::find($livestream_id);
     $social=Social::where('code','youtube')->first();
     $msocial=MarketerSocial::where('marketer_id', $livestream->marketer_id)->where('social_id',$social->id)->first();
    $videoId = $livestream->youtube_video_id;
    $accessToken=$msocial->access_token;
$service = new YouTubeAnalyticsService($accessToken,$msocial->refresh_token);
 
// جلب الإحصائيات الأساسية
$basicStats = $service->getVideoStats($videoId);
//تحديد مدة البث
// $start_date=$this->sts_model->start_date;
// $end_date=$this->sts_model->end_date;
//  $help_ctrlr=new HelpController();
//  $datediff_res=$help_ctrlr->date_diff($start_date,$end_date);
// //حفظ الاحصائيات 
//  $this->sts_model->duration=$datediff_res['duration_seconds'];
//  $this->sts_model->duration_str=$datediff_res['duration_str'];
 
$this->sts_model->live_stream_id=$livestream_id;
$this->sts_model->social_id=$social->id;
$this->sts_model->real_comments_count=$basicStats['commentCount'];
$this->sts_model->views_count=$basicStats['viewCount'];
$this->sts_model->likes_count=$basicStats['likeCount'];
$this->sts_model->dislike_count=$basicStats['dislikeCount'];
$this->sts_model->favorite_count=$basicStats['favoriteCount'];

$this->sts_model->save();
// $startDate = Carbon::parse($livestream->start_date)->subDay()->toDateString();
// $endDate = Carbon::parse(now())->toDateString();
// جلب تحليل ما بعد البث — مثلاً من يوم البث لليوم الحالي
// $report = $service->getAnalyticsForVideo($videoId, $startDate, $endDate);

// دمج وحفظ النتائج في قاعدة البيانات أو عرضها
\Log::info('YouTube Stats', [
    'basic' => $basicStats,
  // 'report' => $report,
]);
return  $basicStats;
 

    }
}
