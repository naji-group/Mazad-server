<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Http\Controllers\Api\HelpController;
class Social extends Model
{
    protected $table = 'socials';
    protected $fillable = [
        'name',
        'code',
        'link',
        'is_active',
        'icon',
        'sequence',
        'is_extra'
    ];
    protected $appends = ['image_url'];
    public function getImageUrlAttribute()
    {
        $conv = "";
        $helpCtrlr = new HelpController();      
            //check
            if ((is_null($this->icon) || $this->icon == '')) {
                $conv = $helpCtrlr->getdefaultbyCode('default-social');
            } else {
              
                $conv = $helpCtrlr->getpublicurl($this->icon);
            }
    

        return $conv;
    }
    public function marketersocials(): HasMany
    {
        return $this->hasMany(MarketerSocial::class, 'social_id');
    }
    public function auctions(): HasMany
    {
        return $this->hasMany(Auction::class, 'social_id');
    }
    public function liveComments(): HasMany
    {
        return $this->hasMany(LiveComment::class, 'social_id');
    }
    public function livestreamsocials(): HasMany
    {
        return $this->hasMany(LivestreamSocial::class, 'social_id');
    }
}
