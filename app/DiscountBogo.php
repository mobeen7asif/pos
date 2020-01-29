<?php

namespace App;

use App\Notifications\CompanyResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class DiscountBogo extends Authenticatable
{
    use Notifiable;
    use HasRoles;
    


    protected $table = 'discount_bogo';

    
    /** 
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'discount_id','store_id', 'from_id','to_id','type', 'from_product_ids','to_product_ids','created_at', 'updated_at'
    ];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [ 'password', 'remember_token','created_at','updated_at','deleted_at' ];

    public function product()
    {
        return $this->belongsTo('App\Products','product_id');
    }
    public function discount()
    {
        return $this->belongsTo(Discount::class, 'discount_id');
    }

}
