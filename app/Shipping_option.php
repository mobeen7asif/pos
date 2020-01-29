<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Auth;

class Shipping_option extends Model
{
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'shipping_options';

    /** 
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['created_at','updated_at'];
    
    /**
     * belongs To relation Company
     */
    public function company()
    {
    	return $this->belongsTo(Company::class, 'company_id');
    }
    
    
    /**
     * boot
     */
    protected static function boot ()
    {
    	parent::boot();
    	static::deleting(function($sipping_options) {
    		
    	});
    }
    
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['company_id', 'name', 'cost'];    
	
}
