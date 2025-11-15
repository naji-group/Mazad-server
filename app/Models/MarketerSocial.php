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
'access_token',
'refresh_token',
'expires_in',
'expires_in_date',
    ];
    protected $casts = [
        'expires_in_date' => 'datetime',
    ];
    public function social(): BelongsTo
    {
        return $this->belongsTo(Social::class, 'social_id')->withDefault();
    }
    public function marketer(): BelongsTo
    {
        return $this->belongsTo(Marketer::class, 'marketer_id')->withDefault();
    }
}
