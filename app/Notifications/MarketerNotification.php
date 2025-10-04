<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Broadcasting\FcmChannel;
use Google\Client as GoogleClient;
class MarketerNotification extends Notification
{
    use Queueable;
    protected $title;
    protected $body;
    protected $data;
    protected $channels;
    /**
     * Create a new notification instance.
     */
    public function __construct($title, $body,$data=[],$channels = ['database', 'fcm'])
    {
        $this->title = $title;
        $this->body  = $body;
        $this->data = $data; // بيانات إضافية
         $this->channels=$channels;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return array_map(function ($channel) {
            return $channel === 'fcm'
                ? FcmChannel::class   // 👈 هنا نبدل النص باسم الكلاس الكامل
                : $channel;
        }, $this->channels);
      //  return ['database', 'fcm'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }
    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->title,
            'body'  => $this->body,
            'data' => $this->data,
        ];
    }


    public function toFcm($notifiable)
    {
        if (!($notifiable->firebase_token) ||$notifiable->firebase_token == '') {
            return 'no-token';
        }

        // if (is_null($notifiable->firebase_token) || $notifiable->firebase_token == '') {
        //     return 'no-token';
        // } else
        //  {
            $dataArr["title"] = $this->title;
             $dataArr["body"]= $this->body;
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
                    'token' => $notifiable->firebase_token,
                    // 'registration_ids'=>[$token_to],
                    'android'=> [
                        'priority'=> "high",
                      ],
                     "notification" =>
                     // [ "title" =>null ]
                     [ "title" => $this->title ,"body"=>$this->body]
                     ,
                     "data" => $dataArr ,   
                     'apns' => [
                        'payload' => [
                            'aps' => [
                                'alert' => [
                                    'title' => $this->title,
                                    'body' => $this->body,
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


       // }
    }
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
