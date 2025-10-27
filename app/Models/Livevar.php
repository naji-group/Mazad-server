<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Livevar extends Model
{
    protected $table = 'livevars';
    protected $fillable = [
        'marketer_id',
        'live_video_id',
        'first_value',
        'second_value',
        'notes',
        'is_active',
        'social',
    ];
    public function marketer(): BelongsTo
    {
        return $this->belongsTo(Marketer::class, 'marketer_id')->withDefault();
    }
}
