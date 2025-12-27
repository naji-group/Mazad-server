<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Auction extends Model
{
    protected $table = 'auctions';
    protected $fillable = [
        'marketer_id',
        'live_video_id',
        'is_active',
        'price',
        'social_id',
        'customer_name',
        'customer_link',
    ];
  

    public function marketer(): BelongsTo
    {
        return $this->belongsTo(Marketer::class, 'marketer_id')->withDefault();
    }
    public function social(): BelongsTo
    {
        return $this->belongsTo(Social::class, 'social_id')->withDefault();
    }
    public function livestream(): BelongsTo
    {
        return $this->belongsTo(LiveStream::class, 'live_video_id')->withDefault();
    }

}
