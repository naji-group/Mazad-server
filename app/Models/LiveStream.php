<?php

namespace App\Models;

use App\Http\Controllers\Api\HelpController;
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

    protected $appends = ['live_duration'];
    public function getLiveDurationAttribute()
    {
        $conv = "";
        if($this->start_date && $this->end_date)      
   {
$help=new HelpController();
$res=$help->date_diff($this->start_date,$this->end_date);
$conv=$res['duration_str'];
   }
       return $conv;
    }


    // public function getLiveDurationInSecondsAttribute()
    // {
    //     if (!$this->start_date) {
    //         return 0;
    //     }
        
    //     $endDate = $this->end_date ?? now();
    //     return $this->start_date->diffInSeconds($endDate);
    // }

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
trait SortableLiveDuration
{
    public function scopeOrderByLiveDuration($query, $direction = 'asc')
    {
        return $query->orderByRaw('
            CASE 
                WHEN end_date IS NOT NULL 
                THEN TIMESTAMPDIFF(SECOND, start_date, end_date)
                ELSE TIMESTAMPDIFF(SECOND, start_date, NOW())
            END
            ' . ($direction === 'desc' ? 'DESC' : 'ASC')
        );
    }
}
