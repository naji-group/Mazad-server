<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class MarketerSocial extends Model
{
    protected $table = 'marketer_socials';
    protected $fillable = [
      'marketer_id',
'social_id',
'link',
'is_active',

    ];
    public function social(): BelongsTo
    {
        return $this->belongsTo(Social::class, 'social_id')->withDefault();
    }
    public function marketer(): BelongsTo
    {
        return $this->belongsTo(Social::class, 'marketer_id')->withDefault();
    }
}
