<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Social extends Model
{
    protected $table = 'socials';
    protected $fillable = [      
'name',
'link',
'is_active',
'icon',
    ]; 

    public function marketersocials(): HasMany
    {
        return $this->hasMany(MarketerSocial::class, 'social_id');
    }
}
