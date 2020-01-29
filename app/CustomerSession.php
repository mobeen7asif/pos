<?php

namespace App;

use App\Notifications\CompanyResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class CustomerSession extends Authenticatable
{

    protected $table = 'customer_sessions';
    
    /**
     * has Many relation Customer
     */
    public function customer()
    {
    	return $this->belongsTo(Customer::class, 'customer_id');
    }      

    /** 
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'device_id', 'customer_id', 'device_type','session_token','created_at','updated_at'
    ];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [ 'password', 'remember_token','created_at','updated_at','deleted_at' ];

    

}
