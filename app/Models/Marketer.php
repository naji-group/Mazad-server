<?php

namespace App\Models;

use App\Http\Controllers\Api\HelpController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
class Marketer extends Authenticatable implements JWTSubject
{
    use HasApiTokens,Notifiable;
    protected $table = 'marketers';
    protected $fillable = [
      'first_name',
'last_name',
'full_name',
'login_type',
'username',
'password',
'is_active',
'email',
'email_verified_at',
'image',
'local_image',
'provider',
'provider_user_id',
'social_id',
'provider_token',
'provider_refresh_token',
'firebase_token',
    ];
    protected $appends= ['status_conv','local_image_url'];
    public function getLocalImageUrlAttribute(){
        $conv="";
        $helpCtrlr = new HelpController(); 
        if(is_null($this->local_image) ){
             $conv =$helpCtrlr->getdefaultbyCode('default-marketer'); 
        }else if($this->local_image==''){
            $conv =$helpCtrlr->getdefaultbyCode('default-marketer'); 
        } else {
            $conv = $helpCtrlr->getpublicurl($this->local_image);
            //$conv =  $url.$this->local_image;
        }     
       
            return  $conv;
     }

//      public function getFullNameAttribute(){
//   return (string)  ($value ?? "") ; 
//      }
       /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
     
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

       /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function social(): BelongsTo
    {
        return $this->belongsTo(Social::class, 'social_id')->withDefault();
    }
    public function marketersocials(): HasMany
    {
        return $this->hasMany(MarketerSocial::class, 'marketer_id');
    }
 
    public function getStatusConvAttribute(){
           $conv="";
          if($this->is_active==1){
           $conv='فعال';
          }else{
           $conv='غير فعال';
          }      
               return  $conv;
        }
}
