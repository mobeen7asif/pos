<?php

namespace App;

use App\Notifications\CompanyResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Company extends Authenticatable
{
    use Notifiable;
    use HasRoles;
    
    protected $guard_name = 'company'; // or whatever guard you want to use
        
    
    /**
     * has Many relation Company
     */
    public function store()
    {
    	return $this->hasMany(Store::class, 'company_id');
    }
    
    /**
     * has Many relation Customer
     */
    public function customers()
    {
    	return $this->hasMany(Customer::class, 'company_id');
    }      
    
    /**
     * has Many relation Currency
     */
    public function currencies()
    {
    	return $this->hasMany(Currency::class, 'company_id');
    }
    
    /**
     * has Many relation Tax_rates
     */
    public function tax_rates()
    {
    	return $this->hasMany(Tax_rates::class, 'company_id');
    }
    
      /**
     * has Many relation Shipping_option
     */
    public function shipping_options()
    {
    	return $this->hasMany(Shipping_option::class, 'company_id');
    }
    public function ads()
    {
        return $this->hasMany(Ad::class, 'company_id');
    }
    
    /**
     * hasOne relation Company Setting
     */
    public function company_setting()
    {
    	return $this->hasOne(Company_setting::class, 'company_id', 'id');
    }
    public function duty_setting()
    {
        return $this->hasOne(DutySetting::class, 'company_id', 'id');
    }
    
    /**
     * hasMany relation Customer Group
     */
    public function customer_groups()
    {
    	return $this->hasMany(Customer_group::class, 'company_id');
    }
    
    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CompanyResetPassword($token));
    }
    
    /** 
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'logo', 'country', 'state', 'city', 'zip', 'address', 'phone', 'mobile','company_type'
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
