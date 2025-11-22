<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LiveStream;
use App\Models\MarketerSocial;
use App\Models\Social;
use Illuminate\Http\Request;
use App\Services\YouTubeAnalyticsService;
use Carbon\Carbon;
class SocialAnalyticController extends Controller
{
    public function get_YT_analytic(Request $request)
    {
        $formdata = $request->all();
        // return response()->json([$formdata]);
        // if(isset($formdata["livestream_id"])){
        //     return  response()->json(["success" =>0],402);
        // }
     $livestream_id=$formdata["livestream_id"];
     //return response()->json([ $livestream_id]);
     $livestream=LiveStream::find($livestream_id);
     $social=Social::where('code','youtube')->first();
     $msocial=MarketerSocial::where('marketer_id', $livestream->marketer_id)->where('social_id',$social->id)->first();
    $videoId = $livestream->youtube_video_id;
    $accessToken=$msocial->access_token;
$service = new YouTubeAnalyticsService($accessToken,$msocial->refresh_token);

// جلب الإحصائيات الأساسية
$basicStats = $service->getVideoStats($videoId);
$startDate = Carbon::parse($livestream->start_date)->subDay()->toDateString();
$endDate = Carbon::parse(now())->toDateString();
// جلب تحليل ما بعد البث — مثلاً من يوم البث لليوم الحالي
 $report = $service->getAnalyticsForVideo($videoId, $startDate, $endDate);

// دمج وحفظ النتائج في قاعدة البيانات أو عرضها
\Log::info('YouTube Stats', [
    'basic' => $basicStats,
   'report' => $report,
]);
return  response()->json(["success" => 1, "message" =>"ok", "data" => 
 [  'basic' => $basicStats,
 'report' => $report,
 ]]);

    }

}
