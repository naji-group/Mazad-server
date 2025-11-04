<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Agora\src\RtcTokenBuilder2;
class AgoraController extends Controller
{
//     public function generateToken1($client_id,$expireTime,$channel)
//     {
//     // $class= new \RtcTokenBuilder;
//      $appId = '994b5aa07c8142848dd9ec69b2e7a3cb';
// // Need to set environment variable AGORA_APP_CERTIFICATE
// $appCertificate ='3a7e5dba8e154897acf69b5470708709';

// //$channelName = "7d72365eb983485397e3e3f9d460bdda";
// $channelName = $channel;
// //$uid = 2882341273;
// $uid =$client_id;
// //$uidStr = "2882341273";
// $role = RtcTokenBuilder::RoleAttendee;
// //$expireTimeInSeconds = 3600;
// $expireTimeInSeconds = $expireTime;
// $currentTimestamp = (new DateTime("now", new DateTimeZone('UTC')))->getTimestamp();
// $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;
// // echo "App Id: " . $appId . PHP_EOL;
// // echo "App Certificate: " . $appCertificate . PHP_EOL;
// if ($appId == "" || $appCertificate == "") {
//    // echo "Need to set environment variable AGORA_APP_ID and AGORA_APP_CERTIFICATE" . PHP_EOL;
//    return "";
// }

// $token = RtcTokenBuilder::buildTokenWithUid($appId, $appCertificate, $channelName, $uid, $role, $privilegeExpiredTs);
// //echo 'Token with int uid: ' . $token . PHP_EOL;

// // $token = \RtcTokenBuilder::buildTokenWithUserAccount($appId, $appCertificate, $channelName, $uidStr, $role, $privilegeExpiredTs);
// // echo 'Token with user account: ' . $token . PHP_EOL;
//   return $token; 
// }

public function generateToken()
{
    $appId = config('services.agora.app_id');
    $appCertificate = config('services.agora.app_certificate');
    $prefix =  'auction';
    $randomPart = uniqid(); // أو يمكنك استخدام Str::uuid()
    $channelName = "{$prefix}_{$randomPart}";
   // $channelName = $request->input('channelName');
    $uid = auth('api_marketers')->user()->id;
    $role = 'publisher'; // publisher or subscriber

    $expireTimeInSeconds = 3600*2; // ساعة
    $currentTimestamp = time();
    $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

    // if (!$channelName) {
    //     return response()->json(['error' => 'Channel name required'], 400);
    // }
    $token = RtcTokenBuilder2::buildTokenWithUid(
        $appId,
        $appCertificate,
        $channelName,
        $uid,
        $role === 'publisher' ? RtcTokenBuilder2::ROLE_PUBLISHER : RtcTokenBuilder2::ROLE_SUBSCRIBER,
        $privilegeExpiredTs
    );
$res=   [
    //'success' => true,
    'token' => $token,
    'channelName' => $channelName,
    'uid' => $uid,
    'role' => $role,
    'expire_at' => $privilegeExpiredTs
];
    return $res;
}

}
