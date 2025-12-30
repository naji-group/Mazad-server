<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StatisticsRequest;
use App\Http\Resources\LiveStreamResource;
use App\Http\Resources\LiveStreamsResource;
use App\Jobs\YouTubeAnalyticsJob;
use App\Models\LiveStream;
use App\Models\LivestreamSocial;
use App\Models\MarketerSocial;
use App\Models\Social;
use Illuminate\Http\Request;
use App\Services\YouTubeAnalyticsService;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
class SocialAnalyticController extends Controller
{
    public function get_YT_analytic(Request $request)
    {
        $formdata = $request->all();
        // return response()->json([$formdata]);
        // if(isset($formdata["livestream_id"])){
        //     return  response()->json(["success" =>0],402);
        // }

        $livestream_id = $formdata["livestream_id"];
        //return response()->json([ $livestream_id]);

        $livestream = LiveStream::find($livestream_id);
        $social = Social::where('code', 'youtube')->first();
        //Analytic
        $sts_model = new LivestreamSocial();
        $sts_model->start_date = now();
        $sts_model->live_stream_id = $livestream->id;
        $sts_model->social_id = $social->id;
        $sts_model->save();

        $sts_model->end_date = now()->addHours(10)->addMinutes(35)->addSeconds(10);
        $sts_model->save();
        YouTubeAnalyticsJob::dispatch($sts_model)->delay(now()->addSecond());



        \Log::info('YouTube Stats done', [
            'id' => $sts_model->id,
            'start_date' => $sts_model->start_date,
            'end_date' => $sts_model->end_date,

        ]);
        return response()->json([
            "success" => 1,
            "message" => "ok",
            "data" =>
                [
                ]
        ]);

    }

    public function get_YT_analytic_method($livestream_id)
    {

        // return response()->json([$formdata]);
        // if(isset($formdata["livestream_id"])){
        //     return  response()->json(["success" =>0],402);
        // }

        //return response()->json([ $livestream_id]);
        $livestream = LiveStream::find($livestream_id);
        $social = Social::where('code', 'youtube')->first();
        $msocial = MarketerSocial::where('marketer_id', $livestream->marketer_id)->where('social_id', $social->id)->first();
        $videoId = $livestream->youtube_video_id;
        $accessToken = $msocial->access_token;
        $service = new YouTubeAnalyticsService($accessToken, $msocial->refresh_token);
        /*
         'viewCount' => $stats->getViewCount(),
                    'likeCount' => $stats->getLikeCount(),
                    'commentCount' => $stats->getCommentCount(),
                    'dislikeCount'=>$stats->getDislikeCount(),
                    'favoriteCount' =>$stats->getFavoriteCount(),
                    'duration' =>$formatted ,
        */
        // جلب الإحصائيات الأساسية
        $basicStats = $service->getVideoStats($videoId);
        $sts_model = new LivestreamSocial();
        $sts_model->live_stream_id = $livestream_id;
        $sts_model->social_id = $social->id;
        $sts_model->real_comments_count = $basicStats['commentCount'];
        $sts_model->views_count = $basicStats['viewCount'];
        $sts_model->likes_count = $basicStats['likeCount'];
        //$sts_model->notes=;

        $sts_model->dislike_count = $basicStats['dislikeCount'];
        $sts_model->favorite_count = $basicStats['favoriteCount'];

        $sts_model->save();
        // $startDate = Carbon::parse($livestream->start_date)->subDay()->toDateString();
// $endDate = Carbon::parse(now())->toDateString();
// جلب تحليل ما بعد البث — مثلاً من يوم البث لليوم الحالي
// $report = $service->getAnalyticsForVideo($videoId, $startDate, $endDate);

        // دمج وحفظ النتائج في قاعدة البيانات أو عرضها
        \Log::info('YouTube Stats', [
            'basic' => $basicStats,
            // 'report' => $report,
        ]);
        return
            [
                'basic' => $basicStats,
                // 'report' => $report,
            ];

    }

    //////////
    public function live_list(Request $request)
    {

        $formdata = $request->all();
        $storrequest = new StatisticsRequest();

        $validator = Validator::make(
            $formdata,
            $storrequest->rules(),
            $storrequest->messages()
        );
        if ($validator->fails()) {

            return response()->json(
                ["success" => 0, "message" => __('api_messages.user not found'), "data" => $validator->errors()]
                ,
                422
            );
        } else {
            $perPage = 100;
            $page= 1;
            if(isset($formdata['page'])){
                $page=  $formdata['page'];
            }           
            
            $authuser = auth('api_marketers')->user();
            $livestreams = LiveStream::where('marketer_id', $authuser->id)->
                with('marketer:id,full_name,username,is_active')
                ->select(
                    'id',
                    'marketer_id',
                    'is_active',
                    // 'duration',
                    'duration_str',
                    'start_date',
                    'end_date',
                )->orderByDesc('start_date')
                ->paginate($perPage, ['*'], 'page', $page);
            //->simplePaginate(100)
            //  ->get();
            ;
           
            $list = LiveStreamResource::collection($livestreams);
            $pagination = [
                'current_page' => $livestreams->currentPage(),
                'from' => $livestreams->firstItem(),
                'last_page' => $livestreams->lastPage(),
                'per_page' => $livestreams->perPage(),
                'to' => $livestreams->lastItem(),
                'total' => $livestreams->total(),
            ]
            ;
            return response()->json(
                ["success" => 1, "message" => __('api_messages.form.success save'), "data" => ["livestreams" => $list, "pagination" => $pagination]]
            );

        }
    }
    public function live_comment(Request $request)
    {
        $formdata = $request->all();
        $storrequest = new StatisticsRequest();

        $validator = Validator::make(
            $formdata,
            $storrequest->rules(),
            $storrequest->messages()
        );
        if ($validator->fails()) {

            return response()->json(
                ["success" => 0, "message" => __('api_messages.user not found'), "data" => $validator->errors()]
                ,
                422
            );
        } else {
        
            $list="";
            $pagination="";
            return response()->json(
                ["success" => 1, "message" => __('api_messages.form.success save'), "data" => ["livestreams" => $list, "pagination" => $pagination]]
            );

        }
    }
}
