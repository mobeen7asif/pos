<?php

namespace App;

use App\Notifications\CompanyResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class DiscountCategory extends Authenticatable
{
    use Notifiable;
    use HasRoles;
    


    protected $table = 'discount_categories';

    
    /** 
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'discount_id','store_id', 'cat_id', 'created_at', 'updated_at'
    ];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [ 'password', 'remember_token','created_at','updated_at','deleted_at' ];

    public function category()
    {
        return $this->belongsTo('App\Categories','cat_id');
    }
    public function discount()
    {
        return $this->belongsTo(Discount::class, 'discount_id');
    }

}
