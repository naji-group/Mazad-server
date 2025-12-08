<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LiveFetchTiktokCommentRequest;
 
use App\Models\LiveComment;
use App\Models\LiveStream;
use App\Models\Social;
use Illuminate\Http\Request;
use App\Jobs\SendMarketerNotification;
use Illuminate\Support\Facades\Validator;
class TikTokController extends Controller
{
        // تشغيل listener
      

        public function startListener(Request $request)
        {
           // return exec('whoami');
           // return shell_exec("ls -la");
            $request->validate([
                'username' => 'required|string',
                'livestream_id' => 'required',
            ]);    
            $username = $request->username;
            $livestream_id = $request->livestream_id;
            try{
                $res_arr= $this->startListener_method($username,$livestream_id);
                // توليد JWT للتوثيق
                        return response()->json($res_arr);
            }catch (\Exception $e) {
                return response()->json([
                    "success" => 0,
                    "message" => __('api_messages.Operation failed'),
                    "data" => $e->getMessage()
                ], 500);
            }

        }
    
        // إيقاف listener
        public function stopListener(Request $request)
{
    $request->validate([
        'username' => 'required|string',
        'livestream_id' => 'required',
    ]);
    $res_arr=$this->stopListener_method($request->username,$request->livestream_id);    
    return response()->json($res_arr);
}


public function startListener_method($username,$livestream_id)
{

    $id=auth('api_marketers')->user()->id;
    $jwt = auth('api_marketers')->tokenById( $id);
    $processName = "tiktok-{$username}-{$livestream_id}";
    $pm2 = trim(shell_exec("which pm2"));

   // $home = '/var/www/.pm2';
    $listenerPath = base_path("tiktok/listener.js");
    $cmd = "HOME=/var/www/.pm2 $pm2 start {$listenerPath} --name {$processName} -- {$username} {$livestream_id} {$jwt}";
    $output = shell_exec("$cmd 2>&1");
    // تشغيل listener
   // $cmd = "pm2 start tiktok/listener.js --name {$processName} -- {$username} {$livestream_id} {$jwt}";
   // exec($cmd);


     $res_arr=[
        "status" => "started",
      //  "username" => $username,
        "livestream_id" => $livestream_id,
        "process" => $processName,
        'command' => $cmd,
    'output' => $output
    ];
    return $res_arr;
}
public function stopListener_method($username,$livestream_id )
{

    $processName = "tiktok-{$username}-{$livestream_id}";

    $pm2 = trim(shell_exec("which pm2"));
    $cmd = "HOME=/var/www/.pm2 $pm2 delete {$processName} 2>&1";
    $output = shell_exec($cmd);



   // exec("pm2 delete {$processName}");
$res_arr=[
    "status" => "stopped",
    "process" => $processName,
    "output"=> $output ,

];
    return $res_arr;

}

public function fetch_comment(Request $request) {
    $formdata = $request->all();
    $storrequest = new LiveFetchTiktokCommentRequest();
    $validator = Validator::make(
        $formdata,
        $storrequest->rules(),
        $storrequest->messages()
    );
    if ($validator->fails()) {
        \Log::error(' validator error', ['error' => $validator->errors()]);
        return response()->json(
            ["success" => 0, "message" => $validator->errors()?->first(), "data" => $validator->errors()]
            ,
            422
        );
    } else {
    $platform_code='tiktok';
$stream=LiveStream::find($request->livestream_id);
$social=Social::where('code',$platform_code)->first();
    // تخزين في قاعدة البيانات
    // TikTokComment::create($data);
    try {
        $comment_time=$request->timestamp->toDateTimeString();
        $comment = LiveComment::updateOrCreate( [
            'platform'   => $platform_code,
            'comment_id' => $request->commentId,
        ],
        [
            'marketer_id' => $stream->marketer_id,
            'agora_live_id' => $stream->agora_live_id,
            'live_stream_id'=> $stream->id,
            'platform' => $platform_code,
            'comment_id' => $request->commentId,
            'author_name' =>   $request->author_name,
            'message' => $request->comment,
            'comment_time' => $request->createtime,
            'social_id'=>$social->id,
        ]);
        if ($comment->wasRecentlyCreated) {
            $helpctrlr=new HelpController();

            //$newcomment_time =  Carbon::parse($comment_time)->timezone(config('app.default_timezone'))->toIso8601String();
            $newcomment_time= $helpctrlr->offset_timezone($comment_time,config('app.default_timezone'));
            //  $newSaved[] = [
            $newSaved = [
            'platform'=>'youtube',
            'comment_id'=> $request->commentId,
            'author_name'=>$request->author_name,
            'message'=>$request->comment,
            'comment_time'=>strval($newcomment_time),
            'social_id'=>strval($social->id),
        ];
        \Log::info('tiktok send noify', [
            'data' => $newSaved,
        ]);
        SendMarketerNotification::dispatch(
            [$stream->marketer_id],'','',$newSaved ,['database', 'fcm']);   

    }

    } catch (\Exception $e) {
        \Log::warning('Tiktok save comment failed: '.$e->getMessage());
        return ["saved" => false];
    }
    return ["saved" => true];
}}
}
