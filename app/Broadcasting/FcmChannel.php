<?php

namespace App\Broadcasting;

use App\Models\Marketer;
use Illuminate\Notifications\Notification;
class FcmChannel
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     */
    public function join( $user): array|bool
    {
        //
    }
    public function send($notifiable, Notification $notification)
    {
        if (method_exists($notification, 'toFcm')) {
            return $notification->toFcm($notifiable);
        }
    }
}
