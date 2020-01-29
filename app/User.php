<?php

namespace App;


use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;


    
    /**
     * has Many relation OauthAccessToken
    */
    
    public function AauthAcessToken(){
        return $this->hasMany('\App\OauthAccessToken');         
    }
    
    
    /**
     * belong to relation Items
     */
    public function country()
    {
        return $this->belongsTo(Country::class,'user_country','code');
    }
    
    /**
     * belong to relation Items
     */
    public function store()
    {
        return $this->belongsTo(Store::class,'store_id');
    }
    
    /**
     * hasMany relation Order
     */
    public function orders()
    {
        return $this->hasMany(Order::class,'biller_id');
    }

    public function logs()
    {
        return $this->hasMany(User_log::class,'user_id');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'gender', 'phone', 'pin_code','is_login','ip','printer_type', 'status', 'profile_image', 'password','store_id','last_online_time','role_name'
    ];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
   protected $hidden = ['user_score','user_country','password','remember_token','created_at','updated_at'];

    protected $appends = array('profileThumbnail');

    public function getprofileThumbnailAttribute()
    {
        $image_name = 'no_image.png';
        if($this->attributes['profile_image']){
            $image_name = 'users/thumbs/'.$this->attributes['profile_image'];
        }
        //return 'hello';
        return checkImage($image_name);

    }
}
