<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class LiveStream extends Model
{
    protected $table = 'live_streams';
    protected $fillable = [
        'marketer_id',
        'agora_live_id',
        'is_active',
        'youtube_live_chat_id',
        'youtube_access_token',
        
        'youtube_refresh_access_token',
        'youtube_channel_id',
        'youtube_video_id',

        'facebook_live_video_id',
        'facebook_access_token',
        'instagram_live_video_id',
        'instagram_access_token',
        'tiktok_live_video_id',
        'tiktok_access_token',
        'jaco_live_video_id',
        'jaco_access_token',

        'facebook_is_active',
        'instagram_is_active',
        'youtube_is_active',
        'tiktok_is_active',        
        'jaco_is_active',     
       'start_date',
'end_date',


    ];
    public function marketer(): BelongsTo
    {
        return $this->belongsTo(Marketer::class, 'marketer_id')->withDefault();
    }
    public function livecomments(): HasMany
    {
        return $this->hasMany(LiveComment::class, 'live_stream_id');
    }
    public function livestreamsocials(): HasMany
    {
        return $this->hasMany(LivestreamSocial::class, 'live_stream_id');
    }
}
