<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class LivestreamSocial extends Model
{
    protected $table = 'livestreams_socials';
    protected $fillable = [
        'live_stream_id',
        'social_id',
        'real_comments_count',
        'views_count',
        'likes_count',
        'notes',

        'dislike_count',
        'favorite_count',
        'duration',
        'duration_str',
        'start_date',
        'end_date',
    ];
    public function livestream(): BelongsTo
    {
        return $this->belongsTo(LiveStream::class, 'live_stream_id')->withDefault();
    }
    public function social(): BelongsTo
    {
        return $this->belongsTo(LiveComment::class, 'social_id')->withDefault();
    }
}
