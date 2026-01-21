<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auction\StoreAuctionRequest;
use App\Http\Requests\Api\Auction\ViewAuctionRequest;
use App\Http\Resources\AuctionResource;
use App\Http\Resources\ExtraSocialResource;
use App\Models\Auction;
use App\Models\LiveStream;
use App\Models\Social;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\Catch_;
class AuctionController extends Controller
{


    public function store(Request $request)
    {
        $formdata = $request->all();
        $storrequest = new StoreAuctionRequest();

        $validator = Validator::make(
            $formdata,
            $storrequest->rules(),
            $storrequest->messages()
        );
        if ($validator->fails()) {

            return response()->json(["success" => 0, "message" => __('api_messages.form.fill required'), "data" => $validator->errors()], 422);
        } else {
            $authuser = auth('api_marketers')->user();
            // if (!($authuser->id == $formdata["id"])) {

            //     return response()->json('notexist', 401);
            // } else {

            //  $authuser->full_name = $formdata["full_name"];
            $activeAuction = Auction::where('marketer_id', $authuser->id)->where('live_video_id', $formdata["live_video_id"])->where('is_active', 1)
                ->update(
                    [
                        'is_active' => 0
                    ]
                );

            $auction = new Auction();
            $auction->marketer_id = $formdata['marketer_id'];
            $auction->live_video_id = $formdata['live_video_id'];
            $auction->is_active = 1;
            $auction->price = $formdata['price'];
            $auction->social_id = $formdata['social_id'];
            $auction->customer_name = $formdata['customer_name'];

            //$auction->customer_link=$formdata['customer_link'] ;
            $auction->save();
            // try {
            //     $livestream = LiveStream::find($auction->live_video_id);
            //     if ($livestream->is_active && $livestream->agora_channel) {
            //         $chatCrlr = new ChatController();
            //         $chatCrlr->updateOverlay($livestream->agora_channel, $auction->customer_name, $auction->price, $auction->social_id);
            //     }
            // } catch (\Exception $e) {
            //     \Log::error('overlay error', ['error' => $e->getMessage()]);

            // }
            return response()->json(
                ["success" => 1, "message" => __('api_messages.form.success save'), "data" => $auction->id]
            );

        }
    }

    public function view(Request $request)
    {
        //     $lang=$request->header('lang')?$request->header('lang'):env('APP_LOCALE','ar');

        // app()->setLocale($lang);
        // $lang=app()->getLocale();
        $formdata = $request->all();
        $storrequest = new ViewAuctionRequest();

        $validator = Validator::make(
            $formdata,
            $storrequest->rules(),
            $storrequest->messages()
        );
        if ($validator->fails()) {
            // app()->setLocale('ar');
            return response()->json(
                ["success" => 0, "message" => $validator->errors()?->first(), "data" => $validator->errors()]
                ,
                422
            );
        } else {
            $authuser = auth('api_marketers')->user();
            // if (!($authuser->id == $formdata["id"])) {
            //     return response()->json('notexist', 401);
            // } else {
            $list = Auction::with('social')->where('marketer_id', $authuser->id)->where('live_video_id', $formdata['live_video_id'])
                //->orderByDesc('price') 
                ->orderByDesc('is_active')->get();
            return response()->json(
                ["success" => 1, "data" => AuctionResource::collection($list), "message" => '']
            );
        }
    }

    public function max_price(Request $request)
    {
        $formdata = $request->all();
        $storrequest = new ViewAuctionRequest();

        $validator = Validator::make(
            $formdata,
            $storrequest->rules(),
            $storrequest->messages()
        );
        if ($validator->fails()) {
            // app()->setLocale('ar');
            return response()->json(
                ["success" => 0, "message" => $validator->errors()?->first(), "data" => $validator->errors()]
                ,
                422
            );
        } else {
            $authuser = auth('api_marketers')->user();
            // if (!($authuser->id == $formdata["id"])) {
            //     return response()->json('notexist', 401);
            // } else {
            $maxrow = Auction::with('social')->where('marketer_id', $authuser->id)->where('live_video_id', $formdata['live_video_id'])
                ->where('is_active', 1)
                ->first();
            $maxrow_resource = new AuctionResource($maxrow);
            return response()->json(
                ["success" => 1, "data" => ["auction" => $maxrow_resource], "message" => '']
            );
        }

    }
    public function extra_socials(Request $request)
    {
        //     $lang=$request->header('lang')?$request->header('lang'):env('APP_LOCALE','ar');

        // app()->setLocale($lang);
        // $lang=app()->getLocale();


        $list = Social::where('is_active', 1)->orderBy('sequence')->get();

        return response()->json(
            ["success" => 1, "data" => ExtraSocialResource::collection($list), "message" => '']
        );
        //}
    }
}
