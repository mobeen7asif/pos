<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Auth;

class DutySetting extends Model
{
    
    /**
     * The database table used by the model.
     *
     * @var string
     */ 
    protected $table = 'duty_settings';

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


    protected static function boot ()
    {
    	parent::boot();
        
    }
    
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['company_id','store_id','logo','detail','ip'];
	
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['created_at','updated_at'];
}
