<?php

namespace App;

use App\Notifications\CustomerResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'id';

    /**
     * belongs To relation User
     */

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * belongs To relation orders
     */

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer');
    }

    public function cards()
    {
        return $this->hasMany(CustomerCard::class, 'customer_id');
    }

    /**
     * boot
     */
    protected static function boot ()
    {
        parent::boot();
        static::deleting(function($product) {

        });
        static::deleting(function($customer) {

            $customer->cards()->delete();
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','name','first_name','last_name','dob','country_id','mobile','ref_code','company_name','address','state','city','zip_code', 'email', 'password',
        'note','current_billing_address','current_shipping_delivery_address','customer_group_id','profile_image'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomerResetPassword($token));
    }

    protected $appends = array('thumbnail','fullImage','customer_id');

    public function getcustomerIdAttribute()
    {
        return $this->attributes['id'];
    }
    public function getthumbnailAttribute()
    {
        $image_name = 'no_image.png';
        if($this->attributes['profile_image']){
            $image_name = 'customers/thumbs/'.$this->attributes['profile_image'];
        }
        if (\File::exists(public_path('uploads/'.$image_name))){
            return asset('uploads/'.$image_name);
        } else {
            return asset('uploads/user_placeholder.png');
        }

        //return checkImage($image_name);

    }
    public function getfullImageAttribute()
    {
        $image_name = 'no_image.png';
        if($this->attributes['profile_image']){
            $image_name = 'customers/'.$this->attributes['profile_image'];
        }
        if (\File::exists(public_path('uploads/'.$image_name))){
            return asset('uploads/'.$image_name);
        } else {
            return asset('uploads/user_placeholder.png');
        }

        //return checkImage($image_name);

    }
    use SoftDeletes;
    protected $dates = ['deleted_at'];
}
