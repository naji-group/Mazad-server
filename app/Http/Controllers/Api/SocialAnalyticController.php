<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StatisticsCommentRequest;
use App\Http\Requests\Api\StatisticsRequest;
use App\Http\Resources\AuctionStSResource;
use App\Http\Resources\LiveStreamResource;
//use App\Http\Resources\LiveStreamsResource;
use App\Jobs\YouTubeAnalyticsJob;
use App\Models\Auction;
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
                ["success" => 0, "message" => __('api_messages.data empty'), "data" => $validator->errors()]
                ,
                422
            );
        } else {
            $perPage = 100;
            $page = 1;
            if (isset($formdata['page'])) {
                $page = $formdata['page'];
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
                ["success" => 1, "message" => '', "data" => ["livestreams" => $list, "pagination" => $pagination]]
            );

        }
    }
    public function live_comment(Request $request)
    {
        $formdata = $request->all();
        $storrequest = new StatisticsCommentRequest();

        $validator = Validator::make(
            $formdata,
            $storrequest->rules(),
            $storrequest->messages()
        );
        if ($validator->fails()) {

            return response()->json(
                ["success" => 0, "message" => __('api_messages.data empty'), "data" => $validator->errors()]
                ,
                422
            );
        } else {
            $perPage = 100;
            $page = 1;
            if (isset($formdata['page'])) {
                $page = $formdata['page'];
            }
            $authuser = auth('api_marketers')->user();
            $user_id = $authuser->id;
            //  return  $user_id;
            $livestream_id = $formdata['livestream_id'];
            $livestream = LiveStream::with([
                'livestreamsocials' => function ($query) {
                    $query->select(
                        'id',
                        'live_stream_id',
                        'social_id',
                        'real_comments_count',
                        'views_count',
                        'likes_count',

                        'dislike_count',
                        'favorite_count',
                        // 'duration',
                        'duration_str',
                        'start_date',
                        'end_date',
                    );
                }
                ,
                'marketer:id,full_name,username,is_active'
                //     'auctions'=> function  ($query)   {       
//         $query->select('id',
//         'marketer_id',
//         'live_video_id',
//         'is_active',
//         'price',
//         'social_id',
//         'customer_name',
//       //  'customer_link',
//          );
// }
            ])->select(
                    'id',
                    'marketer_id',
                    // 'agora_live_id',
                    'is_active',
                    'start_date',
                    'end_date',
                    //'duration',
                    'duration_str'
                )
                ->where('id', $livestream_id)->where('marketer_id', $user_id)->first()
            ;
if(!$livestream){
 
    return response()->json(
        ["success" => 0, "message" => 'not found', "data" => ""]
        ,
        404
    );
}
            $auction_query = Auction::where('live_video_id', $livestream_id)->where('marketer_id', $user_id)
                ->with(['marketer:id,full_name,username,is_active', 'social:id,name,code,icon'])
                ->select(
                    'id',
                    'marketer_id',
                    'live_video_id',
                    'is_active',
                    'price',
                    'social_id',
                    'customer_name',
                    'created_at'
                    //  'customer_link',
                );        

            $socials = Social::orderBy('sequence')->where('is_active', 1)->get();
            $all_count = $auction_query->count();
            $comments_min = $auction_query->min('price');
            $comments_max = $auction_query->max('price');
        
            $all_sts = [
                "marketer" => ["label" => "المسوق", "value" => $livestream->marketer->username],
                "status" => ["label" => "الحالة", "value" => $livestream->is_active],
                "start_date" => ["label" => "تاريخ البدء", "value" => $livestream->start_date],
                "end_date" => ["label" => "تاريخ الانتهاء", "value" => $livestream->end_date],
                "duration" => ["label" => "المدة", "value" => $livestream->duration_str],
                "auction_count" => ["label" => "عدد المزايدات", "value" => $all_count],
                "auction_min" => ["label" => "ادنى سعر", "value" => $comments_min],
                "auction_max" => ["label" => "اعلى سعر", "value" => $comments_max],

            ];
            $all_sts_arr = ["label" => "معلومات البث", "value" => $all_sts];
            $all_sections = [];
      
            foreach ($socials as $social) {
                $comment_social1 = clone $auction_query;
                $comment_social= $comment_social1->where('social_id', $social->id);
                        
                $comments_count = $comment_social->count();
               // return  $comments_count
                $comments_min = $comment_social->min('price');
                $comments_max = $comment_social->max('price');
                //analytic
                $social_analytic = $livestream->livestreamsocials->where("social_id", $social->id)->first();
 
                $social_sts = [                    
                    "start_date" => ["label" => "تاريخ البدء", "value" => $social_analytic?->start_date],
                    "end_date" => ["label" => "تاريخ الانتهاء", "value" => $social_analytic?->end_date],
                    "duration" => ["label" => "المدة", "value" => $social_analytic?->duration_str],
                    "real_comments_count" => ["label" => "التعليقات", "value" => $social_analytic?->real_comments_count],
                    "views_count" => ["label" => "المشاهدات", "value" => $social_analytic?->views_count],
                    "likes_count" => ["label" => "الاعجابات", "value" => $social_analytic?->likes_count],
                    "dislike_count" => ["label" => "عدم الاعجاب", "value" => $social_analytic?->dislike_count],
                    "favorite_count" => ["label" => "المفضلة", "value" => $social_analytic?->favorite_count],                  
                    "auction_count" => ["label" => "عدد المزايدات", "value" => $comments_count],
                    "auction_min" => ["label" => "ادنى سعر", "value" => $comments_min],
                    "auction_max" => ["label" => "اعلى سعر", "value" => $comments_max],
 //"comments"=>$auction_query->get
                ];
                $social_sts_arr = ["label" => "احصائيات " . $social->name, "value" =>  $social_sts];
                $all_sections[]=$social_sts_arr;
            }
            $all_social_sts_arr = ["label" => "  احصائيات المنصات" , "value" =>  $all_sections];
            $auction = $auction_query->paginate($perPage, ['*'], 'page', $page);
            $list = AuctionStSResource::collection($auction);
            $pagination = [
                'current_page' => $auction->currentPage(),
                'from' => $auction->firstItem(),
                'last_page' => $auction->lastPage(),
                'per_page' => $auction->perPage(),
                'to' => $auction->lastItem(),
                'total' => $auction->total(),
            ];
            //  $auction
            return response()->json(
                ["success" => 1, "message" => '', "data" => ["live_statistics" => $all_sts_arr,"social_statistics"=>$all_social_sts_arr,  "auctions" => $list, "pagination" => $pagination]]
            );

        }
    }
}
