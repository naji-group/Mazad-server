<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class LiveComment extends Model
{
    protected $table = 'live_comments';
    protected $fillable = [
'marketer_id',
'live_stream_id',
'agora_live_id',
'platform',
'comment_id',
'author_name',
'message',
'comment_time',
    ];

    protected $dates = ['comment_time'];
    public function livestream(): BelongsTo
    {
        return $this->belongsTo(Marketer::class, 'live_stream_id')->withDefault();
    }
}

