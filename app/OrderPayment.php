<?php

namespace App;

use App\Notifications\CompanyResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class OrderPayment extends Authenticatable
{
    use Notifiable;
    use HasRoles;

    protected $table = 'order_payments';
    protected $guard_name = 'company'; // or whatever guard you want to use

    
    /** 
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id', 'payment_status', 'payment_method', 'payment_type','payment_received','order_total','payment_detail','transaction_detail','card_name', 'created_at', 'updated_at','tip','transaction_id','check_id'
    ];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [ 'password', 'remember_token','created_at','updated_at', ];

}
