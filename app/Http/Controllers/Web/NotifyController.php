<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Jobs\SendMarketerNotification;
use Carbon\Traits\ToStringFormat;
use Illuminate\Http\Request;
use Google\Client as GoogleClient;
// use GuzzleHttp\Client as HttpClient;
class NotifyController extends Controller
{
    public function index()
    {
        return view('site.test');
    }


    public function savetoken(Request $request)
    {
       
        $formdata = $request->all();
        $res= $formdata['token'];
        return $res;
        $to_token = "";
        $title = '';
        $body = '';
        if (isset($formdata['input_token'])) {
            $to_token = $formdata['input_token'];

        }
        if (isset($formdata['title'])) {
            $title = $formdata['title'];

        }
        if (isset($formdata['body'])) {
            $body = $formdata['body'];

        }
        $dataArr = [
            'id' => strval('22'),
            'expert_id' => '1',
            'notes' => 'auto'
        ];
        $res = $this->send_to_fcm($to_token, $title, $body, $dataArr);
        return $res;

    }

    public function sendbytoken(Request $request)
    {
        $formdata = $request->all();
        $to_token = "";
        $title = '';
        $body = '';
        if (isset($formdata['input_token'])) {
            $to_token = $formdata['input_token'];

        }
        if (isset($formdata['title'])) {
            $title = $formdata['title'];

        }
        if (isset($formdata['body'])) {
            $body = $formdata['body'];

        }
        $dataArr = [
            'id' => strval('22'),
           // 'expert_id' => '1',
            'notes' => 'auto'
        ];
        $newSaved[]=    [
            'platform'=>'youtube',
            'comment_id'=>10,
            'author_name'=>"ahmad",
            'message'=>"45",
            'comment_time'=>now(),
            'social_id'=>3]
            
            ;
        SendMarketerNotification::dispatch(
            [8],'','',$newSaved);
            
       // $res = $this->send_to_web_fcm($to_token, $title, $body, $dataArr);
        return "ok";

    }

    // public function send_to_fcm($to_token, $title, $body, $dataArr = null)
    // {
    //     if (is_null($to_token) || $to_token == '') {
    //         return 'no-token';
    //     } else {
    //         $credentialsFilePath = storage_path('app/rouh-app-firebase-adminsdk-cecz8-895e8731d2.json');

    //         $client = new GoogleClient();

    //         $client->setAuthConfig($credentialsFilePath);

    //         $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
    //         //  $client->refreshTokenWithAssertion();
    //       //  $token = $client->fetchAccessTokenWithAssertion();
    //         $client->refreshTokenWithAssertion();
    //           $token = $client->getAccessToken();

    //         $access_token = $token['access_token'];

    //         // Set up the HTTP headers
    //         $headers = [
    //             "Authorization: Bearer $access_token",
    //             'Content-Type: application/json'
    //         ];
    //         $data = [
    //             "message" => [
    //                 'token' => $to_token,
    //                 'notification' => [
    //                     'title' => $title,
    //                     'body' => $body,                        
    //                 ],
    //                 'data' => $dataArr,                                       
    //                                    ]
    //         ];
    //       //  $payload = json_encode($data);
    //      $postcmnd= new HttpClient();
    //      $response=  $postcmnd->post('https://fcm.googleapis.com/v1/projects/rouh-app/messages:send', [
    //             'headers' => [
    //                 'Authorization' => 'Bearer ' . $access_token,
    //                 'Content-Type' => 'application/json',
    //             ],
    //             'json' =>$data,
    //         ]);


    //         // $ch = curl_init();
    //         // curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/rouh-app/messages:send');
    //         // curl_setopt($ch, CURLOPT_POST, true);
    //         // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //         // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //         // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //         // curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    //         // curl_setopt($ch, CURLOPT_VERBOSE, false); // Enable verbose output for debugging
    //         // $response = curl_exec($ch);
    //         // $err = curl_error($ch);
    //         // curl_close($ch);
    //       //  return $response ;
    //    //   return $response->getStatusCode();
    //       return   $response->getBody()->getContents();
    //     }

    // }

    // public function send_to_fcm($to_token, $title, $body, $dataArr = null)
    // {
    //     if (is_null($to_token) || $to_token == '') {
    //         return 'no-token';
    //     } else {
    //         $credentialsFilePath = storage_path('app/rouh-app-firebase-adminsdk-cecz8-895e8731d2.json');

    //         $client = new GoogleClient();

    //         $client->setAuthConfig($credentialsFilePath);

    //         $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
    //         //  $client->refreshTokenWithAssertion();
    //         $token = $client->fetchAccessTokenWithAssertion();
    //         // $token = $client->getAccessToken();

    //         $access_token = $token['access_token'];

    //         // Set up the HTTP headers
    //         $headers = [
    //             "Authorization: Bearer $access_token",
    //             'Content-Type: application/json'
    //         ];
    //         $data = [
    //             "message" => [
    //                 'token' => $to_token,
    //                 // 'registration_ids'=>[$token_to],
    //                 "notification" => [
    //                     "title" => $title,
    //                     "body" => $body,

    //                 ],
    //                 "data" => $dataArr,
    //                 "apns" => [
    //                     "payload" => [
    //                         "aps" => [
    //                             "sound" => "default"
    //                         ],

    //                     ]
    //                 ]
    //             ]
    //         ];
    //         $payload = json_encode($data);

    //         $ch = curl_init();
    //         curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/rouh-app/messages:send');
    //         curl_setopt($ch, CURLOPT_POST, true);
    //         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //         curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    //         curl_setopt($ch, CURLOPT_VERBOSE, false); // Enable verbose output for debugging
    //         $response = curl_exec($ch);
    //         $err = curl_error($ch);
    //         curl_close($ch);
    //         return $response;

    //     }

    // }


      public function send_to_web_fcm($to_token, $title, $body, $dataArr = null)
    {


        if (is_null($to_token) || $to_token == '') {
            return 'no-token';
        } else {
            $dataArr["title"] = $title;
             $dataArr["body"]= $body;
             $dataArr["click_action"]= "FLUTTER_NOTIFICATION_CLICK";
           
            $credentialsFilePath = storage_path('app/zawed-app-firebase-adminsdk.json');

            $client = new GoogleClient();

            $client->setAuthConfig($credentialsFilePath);

            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            //  $client->refreshTokenWithAssertion();
            $token = $client->fetchAccessTokenWithAssertion();
            $client->refreshTokenWithAssertion();
          $token = $client->getAccessToken();

            $access_token = $token['access_token'];
 
            // Set up the HTTP headers
            $headers = [
                "Authorization: Bearer $access_token",
                'Content-Type: application/json'
            ];
            $data = [
                "message" => [
                    'token' => $to_token,
                    // 'registration_ids'=>[$token_to],
                    'android'=> [
                        'priority'=> "high",
                      ],
                     "notification" => [ "title" =>$title   
                     ,
                     "body"=>$body                
                    ],
                   
                     'apns' => [
                        'payload' => [
                            'aps' => [
                                'alert' => [
                                    'title' => $title,
                                    'body' => $body,
                                ],
                                'sound' => 'default',
                                'content-available' => 1,
                            ],
                        ],
                        'headers' => [
                            'apns-priority' => '10', // إشعار عالي الأولوية
                        ],
                    ],    
                ]
            ];
            $payload = json_encode($data);
$projectname='zawed-app';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/'.$projectname.'/messages:send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_VERBOSE, false); // Enable verbose output for debugging
            $response = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);
            return $response;


        }

    }

    public function send_to_fcm($to_token, $title, $body, $dataArr = null) //correct for mobile
    {


        if (is_null($to_token) || $to_token == '') {
            return 'no-token';
        } else {
            $dataArr["title"] = $title;
             $dataArr["body"]= $body;
             $dataArr["click_action"]= "FLUTTER_NOTIFICATION_CLICK";
           
            $credentialsFilePath = storage_path('app/zawed-app-firebase-adminsdk.json');

            $client = new GoogleClient();

            $client->setAuthConfig($credentialsFilePath);

            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            //  $client->refreshTokenWithAssertion();
            $token = $client->fetchAccessTokenWithAssertion();
            $client->refreshTokenWithAssertion();
          $token = $client->getAccessToken();

            $access_token = $token['access_token'];
 
            // Set up the HTTP headers
            $headers = [
                "Authorization: Bearer $access_token",
                'Content-Type: application/json'
            ];
            $data = [
                "message" => [
                    'token' => $to_token,
                    // 'registration_ids'=>[$token_to],
                    'android'=> [
                        'priority'=> "high",
                      ],
                     "notification" => [ "title" =>null                     
                    ],
                     "data" => $dataArr ,   
                     'apns' => [
                        'payload' => [
                            'aps' => [
                                'alert' => [
                                    'title' => $title,
                                    'body' => $body,
                                ],
                                'sound' => 'default',
                                'content-available' => 1,
                            ],
                        ],
                        'headers' => [
                            'apns-priority' => '10', // إشعار عالي الأولوية
                        ],
                    ],    
                ]
            ];
            $payload = json_encode($data);
$projectname='zawed-app';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/'.$projectname.'/messages:send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_VERBOSE, false); // Enable verbose output for debugging
            $response = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);
            return $response;


        }

    }
}
