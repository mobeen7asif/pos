<?php

namespace App;

use App\Notifications\CompanyResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class StoreCustomer extends Authenticatable
{
    use Notifiable;
    use HasRoles;

    protected $table = 'store_customers';
    //protected $guard_name = 'company'; // or whatever guard you want to use
        
    
    /**
     * has Many relation Company
     */
    public function store()
    {
    	return $this->belongsTo(Store::class, 'store_id');
    }
    
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
        'store_id', 'customer_id', 'company_id',
    ];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [ 'password', 'remember_token','created_at','updated_at','deleted_at' ];

    
    
    use SoftDeletes;
    protected $dates = ['deleted_at'];
}
