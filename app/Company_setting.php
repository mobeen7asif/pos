<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Auth;

class Company_setting extends Model
{
    
    /**
     * The database table used by the model.
     *
     * @var string
     */ 
    protected $table = 'company_settings';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';
    
    
    /**
     * has One relation Company
     */
    public function company()
    {
    	return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * belongsTo relation Company
     */
    public function currency()
    {
    	return $this->belongsTo(Currency::class, 'currency_id');
    }
    
    /**
     * belongsTo relation Tax_rates
     */
    public function tax_rate()
    {
    	return $this->belongsTo(Tax_rates::class, 'tax_id');
    }
    
    /**
     * belongsTo relation Shipping_option
     */
    public function shipping_option()
    {
    	return $this->belongsTo(Shipping_option::class, 'shipping_id');
    }
    
    
    /**
     * boot
     */
    protected static function boot ()
    {
    	parent::boot();
        
    }
    
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['discount_status','company_id', 'currency_id', 'email','store_id','tax_id','shipping_id','sales_notifications','offline_mode'];
	
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['created_at','updated_at'];
}
