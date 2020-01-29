<?php

namespace App;

use App\Notifications\CompanyResetPassword;
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Discount extends Authenticatable
{
    use Notifiable;
    use HasRoles;



    protected $table = 'discounts';

    /**
     * boot
     */
    protected static function boot ()
    {
        parent::boot();
        static::deleting(function($discount) {

            $discount->discountCategories()->delete();
            $discount->discountProducts()->delete();
            $discount->discountBogo()->delete();
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'store_id','company_id','name', 'date_time', 'discount_type', 'amount', 'created_at', 'updated_at','start_time','end_time','type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [ 'password', 'remember_token','created_at','updated_at','deleted_at' ];


    public function discountCategories()
    {
        return $this->hasMany('App\DiscountCategory','discount_id');
    }

    public function discountProducts()
    {
        return $this->hasMany('App\DiscountProduct','discount_id');
    }
    public function discountBogo()
    {
        return $this->hasMany('App\DiscountBogo','discount_id');
    }
//    public function getStartDateAttribute($value)
//    {
//        if(isset($value)){
//            $store = Store::where('id',$this->attributes['store_id'])->first();
//            if($store){
//                $time_zone =  $store->time_zone;
//                if($time_zone && $time_zone != null){
//                    $offset = explode(',',$time_zone);
//                    $order_time = $value;
//                    if(count($offset) > 0){
//                        if($offset[1][0] == '+'){
//                            //add time
//                            $time = str_replace('+','',$offset[1]);
//                            $time = explode(':',$time);
//                            $hours = $time[0];
//                            $new_time = Carbon::parse($order_time)->addHour($hours);
//                            $value = $new_time;
//                            return $value;
//                        } else {
//                            //subtract time
//                            $time = str_replace('-','',$offset[1]);
//                            $time = explode(':',$time);
//                            $hours = $time[0];
//                            $new_time = Carbon::parse($order_time)->subHour($hours);
//                            $value = $new_time;
//                            return $value;
//                        }
//                    }
//
//                }
//            }
//        }
//
//    }
}
